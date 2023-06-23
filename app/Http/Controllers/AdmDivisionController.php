<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdmDivision\AdmDivisionResource;
use App\Http\Resources\AdmDivision\SimpleAdmDivisionResource;
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

    public function level($level)
    {
        $adms = AdministrativeDivision::whereLevel($level);

        return SimpleAdmDivisionResource::collection($adms);
    }

    public function all()
    {
        $adms = AdministrativeDivision::all();

        return SimpleAdmDivisionResource::collection($adms);
    }
}
