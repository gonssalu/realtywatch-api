<?php

namespace App\Http\Controllers;

use App\Http\Requests\Property\SearchPropertyRequest;
use App\Http\Resources\Property\PropertyFullResource;
use App\Http\Resources\Property\PropertyHeaderResource;
use App\Models\AdministrativeDivision;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /* TODO: Needs planning
        $user = $request->user();
        $property = $user->properties()->create($request->all());

        return response(
            [
                'message' => 'Property created',
                'property' => new PropertyResource($property),
            ],
            201
        );
        */
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
