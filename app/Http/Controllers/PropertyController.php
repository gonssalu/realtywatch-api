<?php

namespace App\Http\Controllers;

use App\Helpers\StorageLocation;
use App\Http\Requests\Property\SearchPropertyRequest;
use App\Http\Requests\Property\StorePropertyRequest;
use App\Http\Requests\Tag\CreateTagRequest;
use App\Http\Resources\Property\PropertyFullResource;
use App\Http\Resources\Property\PropertyHeaderResource;
use App\Http\Resources\TagResource;
use App\Models\AdministrativeDivision;
use App\Models\Property;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use CreateTagsTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePropertyRequest $request)
    {
        $propertyReq = $request->validated();
        $user = $request->user();

        //Configure missing values
        $propertyReq['user_id'] = $user->id;

        //TODO: Price stuff
        $propertyReq['listing_type'] = 'none';

        $addressReq = $propertyReq['address'];
        unset($propertyReq['address']);

        // Process coordinates
        if (isset($addressReq['latitude']) && isset($addressReq['longitude'])) {
            $addressReq['coordinates_'] = DB::raw('POINT(' . $addressReq['latitude'] . ', ' . $addressReq['longitude'] . ')');
            unset($addressReq['latitude']);
            unset($addressReq['longitude']);
        }

        $addressReq['user_id'] = $user->id;

        // Start transaction!
        DB::beginTransaction();

        $mediaAdded = [];
        try {
            $property = Property::create($propertyReq);

            $property->address()->create($addressReq);

            // Add coordinates to address
            DB::table('property_addresses')->where('property_id', $property->id)->update([
                'coordinates' => $addressReq["coordinates_"],
            ]);

            if (isset($propertyReq['tags']))
                $this->updateTagsHelper($property, $user, $propertyReq['tags']);

            if (isset($propertyReq['lists']))
                $this->updateListsHelper($property, $user, $propertyReq['lists']);

            if (isset($propertyReq['media'])) {
                $mediaReq = $propertyReq['media'];

                if (isset($mediaReq['images']))
                    $mediaAdded[] = $this->processMedia($property, $mediaReq['images'], 'image', true);

                if (isset($mediaReq['blueprints']))
                    $mediaAdded[] = $this->processMedia($property, $mediaReq['blueprints'], 'blueprint');

                if (isset($mediaReq['videos']))
                    $mediaAdded[] = $this->processMedia($property, $mediaReq['videos'], 'video');
            }

            // Process offers
            $hasRentOffer = false;
            $hasSaleOffer = false;
            $minPriceRent = null;
            $minPriceSale = null;
            if (isset($propertyReq['offers'])) {
                $offersReq = $propertyReq['offers'];
                foreach ($offersReq as $offerReq) {
                    $offerReq['property_id'] = $property->id;
                    $offer = $property->offers()->create($offerReq);
                    $ph = $offer->priceHistory()->create([
                        'price' => isset($offerReq['price']) ? $offerReq['price'] : null,
                        'datetime' => Carbon::now(),
                        'latest' => true,
                    ]);

                    if ($offer->type == 'sale' && (!$minPriceSale || $ph->price < $minPriceSale)) {
                        $hasSaleOffer = true;
                        $minPriceSale = $ph->price;
                    } else if ($offer->type == 'rent' && (!$minPriceRent || $ph->price < $minPriceRent)) {
                        $hasRentOffer = true;
                        $minPriceRent = $ph->price;
                    }
                }

                if ($hasSaleOffer) {
                    $property->listing_type = 'sale';
                    $property->current_price_sale = $minPriceSale;
                }

                if ($hasRentOffer) {
                    $property->listing_type = 'rent';
                    $property->current_price_rent = $minPriceRent;
                }

                if ($hasSaleOffer && $hasRentOffer)
                    $property->listing_type = 'both';

                $property->save();
            }

            // Commit the transaction if everything is successful
            DB::commit();
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollback();

            // Delete the added media until now
            foreach ($mediaAdded as $media)
                foreach ($media as $path)
                    Storage::delete(StorageLocation::PROPERTY_MEDIA . '/' . $path);

            return response()->json([
                'message' => 'Something went wrong while creating the property'/*,
                'error' => $e->getMessage(),*/
            ], 500);
        }


        return response()->json([
            'message' => 'Property created',
            'data' => new PropertyFullResource($property),
        ], 201);
    }

    public function processMedia($property, $mediaList, $type, $useAsCover = false): array
    {
        $hasCover = false;
        $orderImage = 0;
        $imgsAdded = [];

        // Store every image
        foreach ($mediaList as $media) {
            $pathImg = $this->saveMedia($property, $media, $orderImage, $type);
            $imgsAdded[] = $pathImg;
            $orderImage++;

            // Set the first image as cover
            if (!$hasCover && $useAsCover) {
                $property->cover_url = $pathImg;
                $hasCover = true;
                $property->save();
            }
        }

        return $imgsAdded;
    }

    public function saveMedia($property, $image, $orderImage, $type): string
    {
        $pathImg = StorageLocation::GenerateFileName($property->id) . '.' . $image->extension();

        // Create db record
        $property->media()->create([
            'type' => $type,
            'order' => $orderImage,
            'url' => $pathImg,
        ]);

        // Store the media
        $image->storeAs(StorageLocation::PROPERTY_MEDIA, $pathImg);

        return $pathImg;
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property)
    {
        return new PropertyFullResource($property);
    }

    public function index(SearchPropertyRequest $request)
    {
        //TODO: Isto ser algo geral para ser chamado dentro de cenas ooo NASTY THOU! IT WORK OM>G
        $search = $request->validated();
        $properties = $request->user()->properties();

        // Search for a property with the query
        if (isset($search['query'])) {
            $properties->where('title', 'like', '%' . $search['query'] . '%')->orWhere('description', 'like', '%' . $search['query'] . '%');
        }

        // Check if property is in the specified list
        if (isset($search['list_id'])) {
            $listId = $search['list_id'];
            $properties->whereHas('lists', function ($query) use ($listId) {
                $query->where('id', $listId);
            }, '=', 1);
        }

        // Check if property has all tags
        if (isset($search['include_tags'])) {
            $tags = json_decode($search['include_tags'], true);

            $properties->whereHas('tags', function ($query) use ($tags) {
                $query->whereIn('id', $tags);
            }, '=', count($tags));
        }

        //Check if property has none of the tags
        if (isset($search['exclude_tags'])) {
            $tags = json_decode($search['exclude_tags'], true);

            $properties->whereHas('tags', function ($query) use ($tags) {
                $query->whereIn('id', $tags);
            }, '=', 0);
        }

        // Check if property is in the specified administrative area
        if (isset($search['adm_id'])) {
            $adm_id = $search['adm_id'];
            $adm = AdministrativeDivision::whereId($adm_id)->get();
            $adm_level = $adm ? $adm->level : 1;

            $properties->whereHas('address', function ($query) use ($adm_id, $adm_level) {
                $query->where('adm' . $adm_level . '_id', $adm_id);
            });
        }

        return PropertyHeaderResource::collection(
            $properties->paginate(12)
        );
    }

    // Remove tag from property
    public function removeTag(Property $property, Tag $tag)
    {
        if ($property->tags()->where('id', $tag->id)->count() == 0) {
            return response()->json([
                'message' => 'Tag not found in property',
            ], 404);
        }

        $property->tags()->detach($tag);

        return response()->json([
            'message' => 'Tag removed from property',
            'data' => new PropertyFullResource($property),
        ], 200);
    }

    // This is here just for future reference / testing purposes (not used)
    public function updateTags(Property $property, CreateTagRequest $request)
    {
        $tagReq = $request->validated();
        $user = $request->user();

        $newTagsCreated =
            $this->updateTagsHelper(
                $property,
                $user,
                $request->has('name') ? [$tagReq['name']] : $tagReq['names']
            );

        return response()->json([
            'message' => 'Property tags were successfully updated',
            'data' => new PropertyFullResource($property),
        ], $newTagsCreated ? 201 : 200);
    }

    // Update a property's tags
    // @return bool true if new tags were added
    public function updateTagsHelper(Property $property, User $user, $tags)
    {
        $new = 0;

        // Detach all tags
        $property->tags()->detach();

        foreach ($tags as $newTag) {
            $tag = TagController::createTag($user, $newTag);
            $tag['tag']->properties()->attach($property);
            if (!$tag['exists'])
                $new++;
        }

        return $new > 0;
    }

    public function updateListsHelper(Property $property, User $user, $lists)
    {
        // Detach all lists from property
        $property->lists()->detach();

        foreach ($lists as $listId) {
            $list = $user->lists()->whereId($listId)->first();
            $list->properties()->attach($property);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Property $property)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property)
    {
        $property->delete();
    }
}
