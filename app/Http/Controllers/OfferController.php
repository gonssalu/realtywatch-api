<?php

namespace App\Http\Controllers;

use App\Models\PropertyOffer;
use DB;
use Exception;
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

        DB::beginTransaction();
        try {

            $offer->priceHistory()->delete();
            $offer->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'An error occured while deleting the offer.',
            ], 500);
        }

        return response()->json([
            'message' => 'Offer deleted successfully',
        ], 200);
    }
}
