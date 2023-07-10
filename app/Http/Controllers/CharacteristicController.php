<?php

namespace App\Http\Controllers;

use App\Http\Requests\Characteristic\CharacteristicStoreUpdateRequest;
use App\Http\Requests\Characteristic\DestroyMultipleCharacteristicsRequest;
use App\Http\Resources\CharacteristicResource;
use App\Models\Characteristic;
use Illuminate\Http\Request;

class CharacteristicController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $characteristics = [
            'textual' => CharacteristicResource::collection($user->textualCharacteristics()),
            'numerical' => CharacteristicResource::collection(
                $user->numericalCharacteristics()
            ),
            'other' => CharacteristicResource::collection($user->otherCharacteristics()),
        ];

        return response()->json([
            'data' => $characteristics,
        ], 200);
    }

    public function indexPaginated(Request $request)
    {
        $user = $request->user();
        $characteristics = $user->customCharacteristics()->paginate(10);

        return response()->json([
            'data' => CharacteristicResource::collection($characteristics),
        ], 200);
    }

    public function store(CharacteristicStoreUpdateRequest $request)
    {
        $characteristicReq = $request->validated();

        $user = $request->user();
        $characteristic = $user->customCharacteristics()->create($characteristicReq);

        return response()->json([
            'data' => new CharacteristicResource($characteristic),
        ], 201);
    }

    public function update(Characteristic $characteristic, CharacteristicStoreUpdateRequest $request)
    {
        $characteristicReq = $request->validated();

        $characteristic->update($characteristicReq);

        return response()->json([
            'data' => new CharacteristicResource($characteristic),
        ], 200);
    }

    public function destroy(Characteristic $characteristic)
    {
        $characteristic->delete();

        return response()->json([
            'data' => new CharacteristicResource($characteristic),
        ], 200);
    }

    public function destroyMultiple(DestroyMultipleCharacteristicsRequest $request)
    {
        $user = $request->user();

        $characteristicReq = $request->validated();

        $characteristicToDelete = $user->customCharacteristics()->whereIn('id', $characteristicReq['characteristics'])->get();
        $count = 0;

        foreach ($characteristicToDelete as $characteristic) {
            $characteristic->properties()->detach();
            $characteristic->delete();
            $count++;
        }

        if ($count == 0) {
            return response()->json([
                'message' => 'No valid characteristics were provided',
            ], 422);
        }

        return response()->json([
            'message' => 'Characteristics deleted successfully',
        ], 200);
    }
}
