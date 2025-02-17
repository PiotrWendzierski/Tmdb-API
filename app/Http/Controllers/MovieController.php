<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;

class MovieController extends Controller
{
    public function index(Request $request)
    {
        $lang = $request->query('lang', 'en'); // Domyślnie angielski

        $movies = Movie::all()->map(function ($movie) use ($lang) {
            return 
            [
                'id' => $movie->id,
                'title' => $movie->getTranslatedTitle($lang),
                'description' => $movie->getTranslatedDescription($lang),
            ];
        });

        return response()->json($movies);
    }
}
