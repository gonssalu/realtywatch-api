<?php

namespace App\Http\Controllers;

use App\Http\Requests\Property\SearchPropertyRequest;
use App\Http\Resources\Property\PropertyDetailsResource;
use App\Http\Resources\Property\PropertyFullResource;
use App\Http\Resources\Property\PropertyHeaderResource;
use App\Http\Resources\Property\PropertyResource;
use App\Models\Property;
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

        $properties = Property::whereUserId($ui)->where('title', 'like', '%' . $search['query'] . '%')->paginate(10);

        return PropertyHeaderResource::collection($properties);
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
