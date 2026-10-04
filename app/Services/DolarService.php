<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DolarService
{
    public static function cotizacionOficial()
    {
        $respuesta = Http::get('https://dolarapi.com/v1/dolares/oficial');

        if ($respuesta->successful()) {
            return $respuesta->json();
        }

        return null;
    }
}