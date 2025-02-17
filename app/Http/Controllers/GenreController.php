<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Genre;

class GenreController extends Controller
{
    public function index(Request $request)
    {
        $lang = $request->query('lang', 'en'); // Domyślnie angielski

        $genres = Genre::all()->map(function ($genre) use ($lang) {
            return [
                'id' => $genre->id,
                'name' => $genre->getTranslatedTitle($lang),
            ];
        });

        return response()->json($genres);
    }
}
