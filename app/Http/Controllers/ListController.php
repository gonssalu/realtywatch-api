<?php

namespace App\Http\Controllers;

use App\Http\Requests\List\StorePropertyListRequest;
use App\Http\Requests\List\UpdatePropertyListRequest;
use App\Http\Resources\List\ListResource;
use App\Http\Resources\List\ListWithPropertiesResource;
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
        $lists = $user->lists()->with('tags')->paginate(12);

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

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePropertyListRequest $request)
    {
        $user = $request->user();
        $listReq = $request->validated();

        $list = $user->lists()->create($listReq);

        return response()->json([
            'message' => 'List created successfully',
            'data' => new ListResource($list)
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

        // Check if list belongs to user
        if ($propertyList->user_id !== $user->id) {
            return response()->json([
                'message' => 'You are not authorized to update this list'
            ], 403);
        }

        $propertyList->update($listReq);

        return response()->json([
            'message' => 'List updated successfully',
            'data' => new ListResource($propertyList)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PropertyList $propertyList)
    {
        $propertyList->delete();
    }
}
