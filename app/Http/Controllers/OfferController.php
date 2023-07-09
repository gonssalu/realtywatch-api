<?php

namespace App\Http\Controllers;

use App\Models\PropertyOffer;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function destroy(PropertyOffer $offer, Request $request)
    {
        if ($offer->property->user_id !== $request->user()->id()) {
            return response()->json([
                'message' => 'You are not authorized to delete this offer',
            ], 403);
        }

        $offer->delete();

        return response()->json([
            'message' => 'Offer deleted successfully',
        ], 200);
    }
}
