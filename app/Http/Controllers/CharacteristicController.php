<?php

namespace App\Http\Controllers;

use App\Http\Resources\CharacteristicResource;
use Illuminate\Http\Request;

class CharacteristicController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $characteristics = [
            "textual" => CharacteristicResource::collection($user->textualCharacteristics()),
            "numerical" => CharacteristicResource::collection(
                $user->numericalCharacteristics()
            ),
            "other" => CharacteristicResource::collection($user->otherCharacteristics()),
        ];

        return response()->json([
            'data' => $characteristics,
        ], 200);
    }
}
