<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Serie; 


class SerieController extends Controller
{
    public function index(Request $request)
    {
        $lang = $request->query('lang', 'en'); // Domyślnie angielski

        $series = Serie::all()->map(function ($serie) use ($lang) {
            return [
                'id' => $serie->id,
                'title' => $serie->getTranslatedTitle($lang),
                'description' => $serie->getTranslatedDescription($lang),
            ];
        });

        return response()->json($series);
    }
}
