<?php

namespace App\Http\Controllers;

use App\Helpers\StorageLocation;
use App\Http\Requests\Property\IndexPolygonPropertiesRequest;
use App\Http\Requests\Property\SearchPropertyRequest;
use App\Http\Requests\Property\StorePropertyRequest;
use App\Http\Requests\Property\UpdatePropertyRequest;
use App\Http\Requests\Tag\CreateTagRequest;
use App\Http\Resources\Property\PropertyFullResource;
use App\Http\Resources\Property\PropertyHeaderResource;
use App\Models\AdministrativeDivision;
use App\Models\Property;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Str;

class PropertyController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePropertyRequest $request)
    {
        $propertyReq = $request->validated();
        $originalReq = $propertyReq; //TODO: disable the debug mode
        $user = $request->user();

        //Configure missing values
        $propertyReq['user_id'] = $user->id;

        $propertyReq['listing_type'] = 'none';

        // Validate status
        $propertyReq['status'] = $propertyReq['status'] ?? 'unknown';

        // Validate address
        $addressReq = $this->validateAddressReq($propertyReq);
        unset($propertyReq['address']);

        $addressReq['user_id'] = $user->id;

        // Start transaction!
        DB::beginTransaction();

        $mediaAdded = [];
        try {
            $property = Property::create($propertyReq);

            $property->address()->create($addressReq);

            // Add coordinates to address
            if (isset($addressReq['coordinates_'])) {
                DB::table('property_addresses')->where('property_id', $property->id)->update([
                    'coordinates' => $addressReq['coordinates_'],
                ]);
            }

            if (isset($propertyReq['tags'])) {
                $this->updateTagsHelper($property, $user, $propertyReq['tags']);
            }

            if (isset($propertyReq['lists'])) {
                $this->updateListsHelper($property, $user, $propertyReq['lists']);
            }

            if (isset($propertyReq['media'])) {
                $mediaReq = $propertyReq['media'];

                if (isset($mediaReq['images'])) {
                    $mediaAdded[] = $this->processMedia($property, $mediaReq['images'], 'image', true);
                }

                if (isset($mediaReq['blueprints'])) {
                    $mediaAdded[] = $this->processMedia($property, $mediaReq['blueprints'], 'blueprint');
                }

                if (isset($mediaReq['videos'])) {
                    $mediaAdded[] = $this->processMedia($property, $mediaReq['videos'], 'video');
                }
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
                    $offerHelper = $this->createOfferHelper($property, $offerReq);
                    $offer = $offerHelper[0]; // Offer instance
                    $ph = $offerHelper[1]; // PriceHistory instance

                    if ($offer->listing_type == 'sale' && (!$minPriceSale || $ph->price < $minPriceSale)) {
                        $hasSaleOffer = true;
                        $minPriceSale = $ph->price;
                    } elseif ($offer->listing_type == 'rent' && (!$minPriceRent || $ph->price < $minPriceRent)) {
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

                if ($hasSaleOffer && $hasRentOffer) {
                    $property->listing_type = 'both';
                }

                $property->save();
            }

            // Process characteristics
            if (isset($propertyReq['characteristics'])) {
                $characteristicsReq = $propertyReq['characteristics'];
                foreach ($characteristicsReq as $characteristicReq) {
                    $charac = $user->customCharacteristics()->where(DB::raw('LOWER(`name`)'), '=', Str::lower($characteristicReq['name']))->where('type', $characteristicReq['type'])->first();

                    if (!$charac) {
                        $characteristicReq['user_id'] = $user->id;
                        $charac = $user->customCharacteristics()->create($characteristicReq);
                    }

                    if (!$charac->properties()->where('properties.id', $property->id)->exists()) {
                        $charac->properties()->attach($property, ['value' => $characteristicReq['value']]);
                    }
                }
            }

            // Commit the transaction if everything is successful
            DB::commit();
        } catch (Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollback();
            // Delete the added media until now
            foreach ($mediaAdded as $media) {
                foreach ($media as $path) {
                    Storage::delete(StorageLocation::PROPERTY_MEDIA . '/' . $path);
                }
            }

            //TODO: disable the debug mode
            return response()->json([
                'message' => 'Something went wrong while creating the property',
                'error' => $e->getMessage(),
                'request' => $originalReq,
            ], 500);
        }

        return response()->json([
            'message' => 'Property created',
            'data' => new PropertyFullResource($property),
        ], 201);
    }

    private function createOfferHelper(Property $property, $offerReq)
    {
        $offer = $property->offers()->create($offerReq);
        $ph = $offer->priceHistory()->create([
            'price' => isset($offerReq['price']) ? $offerReq['price'] : null,
            'datetime' => Carbon::now(),
            'latest' => true,
        ]);

        return [$offer, $ph];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePropertyRequest $request, Property $property)
    {
        $propertyReq = $request->validated();
        $user = $request->user();

        // Validate status
        $propertyReq['status'] = $propertyReq['status'] ?? 'unknown';

        // Validate address
        $addressReq = $this->validateAddressReq($propertyReq);
        unset($propertyReq['address']);

        // Start transaction!
        DB::beginTransaction();

        $mediaAdded = [];
        $mediaToRemove = [];
        try {
            $property->update($propertyReq);

            $property->address()->update($addressReq);

            // Update address coordinates
            if (isset($addressReq['coordinates_'])) {
                DB::table('property_addresses')->where('property_id', $property->id)->update([
                    'coordinates' => $addressReq['coordinates_'],
                ]);
            }

            if (isset($propertyReq['tags'])) {
                $property->tags()->detach();
                $this->updateTagsHelper($property, $user, $propertyReq['tags']);
            }

            if (isset($propertyReq['lists'])) {
                $property->lists()->detach();
                $this->updateListsHelper($property, $user, $propertyReq['lists']);
            }

            if (isset($propertyReq['media'])) {
                $mediaReq = $propertyReq['media'];

                if (isset($mediaReq['remove'])) {
                    $mediaToRemove = $mediaReq['remove'];
                    foreach ($mediaToRemove as $mediaId) {
                        $media = $property->media()->where('id', $mediaId)->first();
                        if ($media) {
                            $media->delete();
                            $mediaToRemove[] = $media->url;
                        }
                    }
                }

                if (isset($mediaReq['images'])) {
                    $lastEntry = $property->photos()->last();
                    $lastOrder = $lastEntry ? $lastEntry->order + 1 : 0;
                    $mediaAdded[] = $this->processMedia($property, $mediaReq['images'], 'image', false, $lastOrder);
                }

                if (isset($mediaReq['blueprints'])) {
                    $lastEntry = $property->blueprints()->last();
                    $lastOrder = $lastEntry ? $lastEntry->order + 1 : 0;
                    $mediaAdded[] = $this->processMedia($property, $mediaReq['blueprints'], 'blueprint', false, $lastOrder);
                }

                if (isset($mediaReq['videos'])) {
                    $lastEntry = $property->videos()->last();
                    $lastOrder = $lastEntry ? $lastEntry->order + 1 : 0;
                    $mediaAdded[] = $this->processMedia($property, $mediaReq['videos'], 'video', false, $lastOrder);
                }
            }

            // Process offers
            $hasRentOffer = false;
            $hasSaleOffer = false;
            $minPriceRent = null;
            $minPriceSale = null;

            if (isset($propertyReq['offers'])) {
                $offersReq = $propertyReq['offers'];

                foreach ($offersReq as $offerReq) {
                    $offerId = isset($offerReq['id']) ? $offerReq['id'] : null;
                    if ($offerId) {
                        $offer = $property->offers()->where('id', $offerId)->first();
                        $offer->update($offerReq);

                        $newPrice = isset($offerReq['price']) ? $offerReq['price'] : null;

                        if ($offer->priceHistory()->last()->price != $newPrice) {
                            $offer->priceHistory()->last()->update([
                                'latest' => false,
                            ]);

                            $offer->priceHistory()->create([
                                'price' => isset($offerReq['price']) ? $offerReq['price'] : null,
                                'datetime' => Carbon::now(),
                                'latest' => true,
                            ]);
                        }
                    } else {
                        $offerReq['property_id'] = $property->id;
                        $this->createOfferHelper($property, $offerReq);
                    }
                }

                $hasSaleOffer = $property->offersSale()->exists();
                $hasRentOffer = $property->offersRent()->exists();

                if ($hasSaleOffer) {
                    $property->listing_type = 'sale';
                    $minPriceSale = $property->offersSale()->where('latest', true)->min('price');
                    $property->current_price_sale = $minPriceSale;
                }

                if ($hasRentOffer) {
                    $property->listing_type = 'rent';
                    $minPriceRent = $property->offersRent()->where('latest', true)->min('price');
                    $property->current_price_rent = $minPriceRent;
                }

                if ($hasSaleOffer && $hasRentOffer) {
                    $property->listing_type = 'both';
                }

                $property->save();
            }

            // Process characteristics
            if (isset($propertyReq['characteristics'])) {
                $characteristicsReq = $propertyReq['characteristics'];
                $property->customCharacteristics()->detach();
                foreach ($characteristicsReq as $characteristicReq) {
                    $charac = $user->customCharacteristics()->where(DB::raw('LOWER(`name`)'), '=', Str::lower($characteristicReq['name']))->where('type', $characteristicReq['type'])->first();

                    if (!$charac) {
                        $characteristicReq['user_id'] = $user->id;
                        $charac = $user->customCharacteristics()->create($characteristicReq);
                    }

                    if (!$charac->properties()->where('properties.id', $property->id)->exists()) {
                        $charac->properties()->attach($property, ['value' => $characteristicReq['value']]);
                    }
                }
            }

            // Commit the transaction if everything is successful
            DB::commit();
        } catch (Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollback();

            // Delete the added media until now
            foreach ($mediaAdded as $media) {
                foreach ($media as $path) {
                    Storage::delete(StorageLocation::PROPERTY_MEDIA . '/' . $path);
                }
            }

            //TODO: disable the debug mode
            return response()->json([
                'message' => 'Something went wrong while updating the property',
                'error' => $e->getMessage(),
            ], 500);
        }

        // Delete the removed media
        foreach ($mediaToRemove as $path) {
            Storage::delete(StorageLocation::PROPERTY_MEDIA . '/' . $path);
        }

        return response()->json([
            'message' => 'Property updated',
            'data' => new PropertyFullResource($property),
        ], 200);
    }

    public function processMedia($property, $mediaList, $type, $useAsCover = false, $startingOrder = 0): array
    {
        $hasCover = false;
        $orderImage = $startingOrder;
        $imgsAdded = [];

        // Store every image
        foreach ($mediaList as $media) {
            $pathImg = $this->saveMedia($property, $media, $orderImage, $type);
            $imgsAdded[] = $pathImg;

            if ($orderImage != 2000000000)
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

    private function handleSearch($search, $props)
    {
        $properties = $props;
        // Search for a property with the query
        //BUG: MAJOR BUG CUZ OF ORWHERE
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

        return $properties;
    }

    public function index(SearchPropertyRequest $request)
    {
        $search = $request->validated();
        $properties = $request->user()->properties();
        $properties = $this->handleSearch($search, $properties);

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
            if (!$tag['exists']) {
                $new++;
            }
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

    private function validateAddressReq($propertyReq)
    {
        $addressReq = $propertyReq['address'];

        // Process coordinates
        if (isset($addressReq['latitude']) && isset($addressReq['longitude'])) {
            $addressReq['coordinates_'] = DB::raw('POINT(' . $addressReq['latitude'] . ', ' . $addressReq['longitude'] . ')');
            unset($addressReq['latitude']);
            unset($addressReq['longitude']);
        }

        // Process administrative divisions & validate them
        // This prevents a user from maliciously trying to use a different adm division
        if (isset($addressReq['adm3_id'])) {
            $adm3 = AdministrativeDivision::whereId($addressReq['adm3_id'])->first();
            $addressReq['adm2_id'] = $adm3->parent->id;
            $addressReq['adm1_id'] = $adm3->parent->parent->id;
        } elseif (isset($addressReq['adm2_id'])) {
            $adm2 = AdministrativeDivision::whereId($addressReq['adm2_id'])->first();
            $addressReq['adm1_id'] = $adm2->parent->id;
        }

        return $addressReq;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property)
    {
        $property->delete();

        return response()->json([
            'message' => 'Property has been trashed',
        ], 200);
    }

    private function permanentDeleteProp(Property $property)
    {
        $mediaPaths = [];
        DB::beginTransaction();
        try {
            $mediaPaths = $property->media()->pluck('url')->toArray();
            $property->media()->delete();
            $property->priceHistories()->delete();
            $property->offers()->delete();
            $property->address()->delete();
            $property->characteristics()->detach();
            $property->tags()->detach();
            $property->lists()->detach();
            $property->forceDelete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            return null;
        }

        return $mediaPaths;
    }

    public function permanentDestroy(Request $request, $trashedProperty)
    {
        $property = $request->user()->properties()->onlyTrashed()->where('id', $trashedProperty)->first();

        if (!$property) {
            return response()->json([
                'message' => 'Property not found in trash',
            ], 404);
        }

        $mediaPaths = $this->permanentDeleteProp($property);

        if (!$mediaPaths) {
            return response()->json([
                'message' => 'Something went wrong while permanently deleting the property'/*,
                'error' => $e->getMessage(),*/
            ], 500);
        }

        foreach ($mediaPaths as $path) {
            Storage::delete(StorageLocation::PROPERTY_MEDIA . '/' . $path);
        }

        return response()->json([
            'message' => 'Property has been permanently deleted',
        ], 200);
    }

    public function trashed(Request $request)
    {
        $properties = $request->user()->properties()->onlyTrashed()->paginate(12);

        return PropertyHeaderResource::collection($properties);
    }

    public function restore(Request $request, $trashedProperty)
    {
        $property = $request->user()->properties()->onlyTrashed()->where('id', $trashedProperty)->first();
        if (!$property) {
            return response()->json([
                'message' => 'Property not found in trash',
            ], 404);
        }

        $property->restore();

        return response()->json([
            'message' => 'Property has been restored',
        ], 200);
    }

    public function restoreAll(Request $request)
    {
        $request->user()->properties()->onlyTrashed()->restore();
        return response()->json([
            'message' => 'All properties have been restored',
        ], 200);
    }

    public function emptyTrash(Request $request)
    {
        $properties = $request->user()->properties()->onlyTrashed()->get();
        $errors = [];
        foreach ($properties as $property) {
            $propId = $property->id;
            $mediaPaths = $this->permanentDeleteProp($property);
            if (!$mediaPaths) {
                $errors[] = $propId;
                continue;
            }
            foreach ($mediaPaths as $path) {
                Storage::delete(StorageLocation::PROPERTY_MEDIA . '/' . $path);
            }
        }

        if (count($errors) > 0) {
            $errors = array_map(function ($id) {
                return ['property_id' => $id, 'message' => 'Something went wrong while permanently deleting the property'];
            }, $errors);
            return response()->json([
                'message' => 'Success, but some properties could not be permanently deleted.',
                'errors' => $errors,
            ], 207);
        }

        return response()->json([
            'message' => 'All properties in the trash have been permanently deleted',
        ], 200);
    }

    public function indexPropertiesInPolygon(IndexPolygonPropertiesRequest $request)
    {
        $polygonReq = $request->validated();
        $props = $request->user()->properties();
        $props = $this->handleSearch($polygonReq, $props);

        //HACK if no polygon is specified, return all properties
        if (!isset($polygonReq['p']))
            return PropertyHeaderResource::collection($props->paginate(12));

        $polygon = $polygonReq['p'];

        $text = 'POLYGON((';
        foreach ($polygon as $point) {
            $text .= $point['x'] . ' ' . $point['y'] . ', ';
        }
        $text .= $polygon[0]['x'] . ' ' . $polygon[0]['y'] . '))';

        $properties = $props->whereHas('address', function ($query) use ($text) {
            $query->whereRaw("ST_CONTAINS(ST_GEOMFROMTEXT('$text'), coordinates)");
        })->paginate(12);

        return PropertyHeaderResource::collection($properties);
    }
}
