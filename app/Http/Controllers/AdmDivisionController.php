<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdmDivision\AdmDivisionResource;
use App\Models\AdministrativeDivision;
use Illuminate\Http\Request;

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
}
