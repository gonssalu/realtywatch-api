<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Requests\List\StorePropertyListRequest;
use App\Http\Requests\List\UpdatePropertyListRequest;
use App\Http\Resources\ListResource;
use App\Http\Resources\Property\PropertyHeaderResource;
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
        $lists = $user->lists();

        $lists =
            PaginationHelper::paginate($lists, request(), 10);

        return ListResource::collection($lists);
    }

    /**
     * Display a FULL listing of the resource.
     */
    public function indexAll(Request $request)
    {
        $user = $request->user();
        $lists = $user->lists();

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
        $properties = PaginationHelper::paginate($propertyList->properties(), request(), 12);
        PropertyHeaderResource::collection($properties);

        $data = [
            'list' => new ListResource($propertyList),
            'properties' => $properties->toArray(),
        ];

        //$data = PropertyHeaderResource::collection($properties);

        return $data;
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
        $propertyList->delete();
    }
}
