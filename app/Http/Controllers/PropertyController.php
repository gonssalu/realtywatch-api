<?php

namespace App\Http\Controllers;

use App\Http\Requests\Property\SearchPropertyRequest;
use App\Http\Resources\Property\PropertyDetailsResource;
use App\Http\Resources\Property\PropertyFullResource;
use App\Http\Resources\Property\PropertyHeaderResource;
use App\Http\Resources\Property\PropertyResource;
use App\Models\Property;
use DB;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $properties = $user->properties()->paginate(10);

        return PropertyHeaderResource::collection($properties);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /*TODO: Needs planning
        $user = $request->user();
        $property = $user->properties()->create($request->all());

        return response(
            [
                'message' => 'Property created',
                'property' => new PropertyResource($property),
            ],
            201
        );*/
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property)
    {
        return new PropertyFullResource($property);
    }

    public function showDetails(Property $property)
    {
        return new PropertyDetailsResource($property);
    }

    public function search(SearchPropertyRequest $request)
    {
        $search = $request->validated();
        $ui = $request->user()->id;

        // Search for a property with the query
        $properties = Property::query()->whereUserId($ui);

        if (isset($search['query']))
            $properties->where('title', 'like', '%' . $search['query'] . '%');

        if (isset($search['tags'])) {
            $tags = json_decode($search['tags'], true);

            // Check if property has all tags
            $properties->whereHas('tags', function ($query) use ($tags) {
                $query->whereIn('name', $tags);
            }, '=', count($tags));
        }

        // Check if property is in the specified administrative area
        if (isset($search['adm_id'])) {
            $adm_level = $search['adm_level'];
            $adm_id = $search['adm_id'];

            $properties->whereHas('address', function ($query) use ($adm_id, $adm_level) {
                $query->where('adm' . $adm_level . '_id', $adm_id);
            });
        }

        return PropertyHeaderResource::collection(
            $properties->paginate(10)
        );
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
        //
    }
}
