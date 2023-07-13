<?php

namespace App\Exceptions;

use Exception;

class TooMuchMediaProperty extends Exception
{
    protected $maxNumber;

    protected $mediaType;

    public function __construct($maxNumber, $mediaType)
    {
        $this->maxNumber = $maxNumber;
        $this->mediaType = $mediaType;
        $this->message = "There is a maximum of {$maxNumber} {$mediaType} per property.";
    }

    public function render()
    {
        return response()->json([
            'message' => $this->message,
        ], 409);
    }
}
