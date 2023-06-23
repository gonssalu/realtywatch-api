<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdmDivision\AdmDivisionResource;
use App\Http\Resources\AdmDivision\SimpleAdmDivisionResource;
use App\Models\AdministrativeDivision;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdmDivisionController extends Controller
{
    /**
     * Display a paginated listing of the resource.
     */
    public function index(Request $request)
    {
        $adm = AdministrativeDivision::whereLevel(1)->whereHas('addresses1', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->get();

        return AdmDivisionResource::collection($adm);
    }

    public function level($level, Request $request)
    {
        $adms = AdministrativeDivision::whereLevel((int)$level);

        if ($level != 1)
            if ($request->has('parent_id')) {
                // Validate parent id
                $parent_id = $request->validate([
                    'parent_id' => [
                        'integer',
                        Rule::exists('administrative_divisions', 'id')->where(function ($query) use ($level) {
                            $query->where('level', $level - 1);
                        })
                    ],
                ], [
                    'parent_id.exists' => 'That parent_id does not exist for level ' . ($level - 1) . ' administrative divisions.',
                ])['parent_id'];

                $adms = $adms->whereParentId($parent_id);
            }

        return SimpleAdmDivisionResource::collection($adms->get());
    }

    public function all()
    {
        $adms = AdministrativeDivision::all();

        return SimpleAdmDivisionResource::collection($adms);
    }
}
