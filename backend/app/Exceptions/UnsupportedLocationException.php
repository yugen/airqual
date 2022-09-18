<?php

namespace App\Exceptions;

use Exception;

class UnsupportedLocationException extends Exception
{
    public function __construct(string $location)
    {
        parent::__construct('No stations where found near your location.  Try searching for another city or a state/province.', 404);
    }
}
