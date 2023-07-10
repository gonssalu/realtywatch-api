<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tag\CreateTagRequest;
use App\Http\Requests\Tag\DestroyMultipleTagsRequest;
use App\Http\Resources\Tag\TagManageResource;
use App\Http\Resources\TagResource;
use App\Models\Tag;
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

        $tags = $tags->paginate(10);

        return TagManageResource::collection($tags);
    }

    /**
     * Display a paginated listing of the resource.
     */
    public function indexAll(Request $request)
    {
        $user = $request->user();
        $tags = $user->tags()->get();

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

    public static function createTag($user, $name)
    {
        $tag = $user->tags()->where('name', $name)->first();
        if ($tag) {
            return ['tag' => $tag, 'exists' => true];
        }

        $tag = $user->tags()->create(['name' => $name]);

        return ['tag' => $tag, 'exists' => false];
    }

    //Before creating a tag check if user already has a tag with the same toLower() name
    public function create(CreateTagRequest $request)
    {
        $user = $request->user();

        $tagReq = $request->validated();

        if ($request->has('name')) {
            $tag = TagController::createTag($user, $tagReq['name']);

            return response()->json([
                'message' => $tag['exists'] ? 'Tag already exists' : 'Tag created successfully',
                'data' => new TagResource($tag['tag']),
            ], $tag['exists'] ? 200 : 201);
        }

        $newTags = [];
        $new = 0;

        foreach ($tagReq['names'] as $newTag) {
            $tag = TagController::createTag($user, $newTag);
            $newTags[] = $tag['tag'];
            if (!$tag['exists']) {
                $new++;
            }
        }

        return response()->json([
            'message' => $new > 0 ? 'New tags were created successfully' : 'All of the tags provided already existed',
            'data' => TagResource::collection($newTags),
        ], $new > 0 ? 201 : 200);
    }

    public function destroy(Tag $tag, Request $request)
    {
        // Check if user owns tag
        if ($tag->user->id != $request->user()->id) {
            return response()->json([
                'message' => 'You are not authorized to delete this tag',
            ], 403);
        }

        // Remove tag from all properties
        $tag->properties()->detach();
        // Remove tag from all lists
        $tag->lists()->detach();

        $tag->delete();

        return response()->json([
            'message' => 'Tag deleted successfully',
        ], 200);
    }

    public function destroyMultiple(DestroyMultipleTagsRequest $request)
    {
        $user = $request->user();

        $tagReq = $request->validated();

        $tagsToDelete = $user->lists()->whereIn('id', $tagReq['tags'])->get();
        $count = 0;
        foreach ($tagsToDelete as $tag) {
            // Detach all properties from list
            $tag->properties()->detach();

            // Detach all tags from list
            $tag->lists()->detach();

            $tag->delete();
            $count++;
        }

        if ($count == 0) {
            return response()->json([
                'message' => 'No valid tags were provided',
            ], 422);
        }

        return response()->json([
            'message' => 'Tags deleted successfully',
        ], 200);
    }
}
