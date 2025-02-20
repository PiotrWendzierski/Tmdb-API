<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class MovieController extends Controller
{
    // all movies with translation
    public function index(Request $request): JsonResponse
    {
        try 
        {
            $lang = $request->query('lang', 'en');

            // validate language
            if (!in_array($lang, Movie::AVAILABLE_LANGUAGES)) 
            {
                Log::warning("Invalid language requested: {$lang}. Defaulting to English.");
                $lang = 'en';
            }

            // get all movies
            $movies = Movie::all();

            // add translated title and description directly to the response
            foreach ($movies as $movie) 
            {
                $movie->title = $movie->getTranslatedTitle($lang);
                $movie->description = $movie->getTranslatedDescription($lang);
            }

            return response()->json($movies, 200);
        } 
        catch (\Exception $e) 
        {
            Log::error('Error fetching movies: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}
