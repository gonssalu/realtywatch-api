<?php

namespace App\Http\Controllers;

use App\Http\Requests\List\AddMultiplePropertiesRequest;
use App\Http\Requests\List\DestroyMultipleListsRequest;
use App\Http\Requests\List\StorePropertyListRequest;
use App\Http\Requests\List\UpdatePropertyListRequest;
use App\Http\Resources\List\ListResource;
use App\Http\Resources\List\ListWithPropertiesResource;
use App\Models\Property;
use App\Models\PropertyList;
use Illuminate\Http\Request;

class ListController extends Controller
{
    /**
     * Display a paginated listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $lists = $user->lists()->orderBy('name')->paginate(10);

        return ListResource::collection($lists);
    }

    /**
     * Display a FULL listing of the resources that have properties.
     */
    public function indexSidebar(Request $request)
    {
        $user = $request->user();
        $lists = $user->lists()->has('properties')->get();

        return ['data' => ListResource::collection($lists), 'total' => $user->lists()->count()];
    }

    /**
     * Display a FULL listing of the resource.
     */
    public function indexAll(Request $request)
    {
        $user = $request->user();
        $lists = $user->lists()->get();

        return ['data' => ListResource::collection($lists), 'total' => $user->lists()->count()];
    }

    public function addMultipleProperties(PropertyList $propertyList, AddMultiplePropertiesRequest $request)
    {
        $listReq = $request->validated();

        $propertyList->properties()->attach($listReq['properties']);

        return response()->json([
            'message' => 'Properties added successfully',
            'data' => new ListWithPropertiesResource($propertyList),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePropertyListRequest $request)
    {
        $user = $request->user();
        $listReq = $request->validated();

        //Check if user already has a list with the same name
        if ($user->lists()->where('name', $listReq['name'])->first()) {
            return response()->json([
                'message' => 'A list with that name already exists',
            ], 409);
        }

        $list = $user->lists()->create($listReq);

        return response()->json([
            'message' => 'List created successfully',
            'data' => new ListResource($list),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(PropertyList $propertyList)
    {
        return new ListWithPropertiesResource($propertyList);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePropertyListRequest $request, PropertyList $propertyList)
    {
        $user = $request->user();
        $listReq = $request->validated();

        //Check if user already has a list (excluding this one) with the same name
        if ($user->lists()->where('name', $listReq['name'])->where('id', '!=', $propertyList->id)->first()) {
            return response()->json([
                'message' => 'A list with that name already exists',
            ], 409);
        }

        $propertyList->update($listReq);

        return response()->json([
            'message' => 'List updated successfully',
            'data' => new ListWithPropertiesResource($propertyList),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PropertyList $propertyList, Request $request)
    {
        $user = $request->user();

        // Detach all properties from list
        $propertyList->properties()->detach();

        // Detach all tags from list
        $propertyList->tags()->detach();

        $propertyList->delete();

        return response()->json([
            'message' => 'List deleted successfully',
        ], 200);
    }

    public function destroyMultiple(DestroyMultipleListsRequest $request)
    {
        $user = $request->user();

        $listReq = $request->validated();

        $listsToDelete = $user->lists()->whereIn('id', $listReq['lists'])->get();
        $count = 0;
        foreach ($listsToDelete as $list) {
            // Detach all properties from list
            $list->properties()->detach();

            // Detach all tags from list
            $list->tags()->detach();

            $list->delete();
            $count++;
        }

        if ($count == 0) {
            return response()->json([
                'message' => 'No valid lists were provided',
            ], 422);
        }

        return response()->json([
            'message' => 'Lists deleted successfully',
        ], 200);
    }

    public function removeProperty(PropertyList $propertyList, Property $property)
    {
        if (!$propertyList->properties()->find($property->id)) {
            return response()->json([
                'message' => 'Property not found in list',
            ], 404);
        }

        $propertyList->properties()->detach($property->id);

        return response()->json([
            'message' => 'Property removed successfully',
        ], 200);
    }
}
