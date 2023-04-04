<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePropertyListRequest;
use App\Http\Requests\UpdatePropertyListRequest;
use App\Http\Resources\ListResource;
use App\Models\PropertyList;
use Illuminate\Http\Request;

class ListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $lists = $user->lists()->paginate(10);

        return ListResource::collection($lists);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePropertyListRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PropertyList $propertyList)
    {
        // return new ListResource($propertyList);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePropertyListRequest $request, PropertyList $propertyList)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PropertyList $propertyList)
    {
        //
    }
}
