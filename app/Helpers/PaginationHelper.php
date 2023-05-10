<?php

namespace App\Helpers;

class PaginationHelper
{
    public static function paginate($query, $request, $defaultPerPage = 12)
    {
        $perPage = $request->query('per_page') ?? $defaultPerPage;

        return $query->paginate($perPage)->appends(['per_page' => $perPage]);
    }
}
