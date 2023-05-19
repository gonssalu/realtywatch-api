<?php

namespace App\Http\Controllers;

use App\Http\Resources\TagResource;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a FULL listing of the resource.
     */
    public function indexAll(Request $request)
    {
        $user = $request->user();
        $tags = $user->tags()->get();

        return TagResource::collection($tags);
    }

    //Before creating a tag check if user already has a tag with the same toLower() name
}
