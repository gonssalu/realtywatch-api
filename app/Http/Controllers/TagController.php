<?php

namespace App\Http\Controllers;

use App\Http\Resources\TagResource;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a paginated listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $tags = $user->tags();

        $tags = $tags->paginate(12);

        return TagResource::collection($tags);
    }

    /**
     * Display a FULL listing of the resources that have properties.
     */
    public function indexSidebar(Request $request)
    {
        $user = $request->user();
        $tags = $user->tags()->has('properties')->get();

        return TagResource::collection($tags);
    }

    //Before creating a tag check if user already has a tag with the same toLower() name
}
