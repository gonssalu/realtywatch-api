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

        $lists = $lists->paginate(12);

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
        $properties = $propertyList->properties()->paginate(12);
        PropertyHeaderResource::collection($properties); //This line is required

        $data = [
            'list' => new ListResource($propertyList),
            'properties' => $properties,
        ];

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
