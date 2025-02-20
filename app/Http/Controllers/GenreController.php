<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Genre;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class GenreController extends Controller
{
    // all genres with translation
    public function index(Request $request): JsonResponse
    {
        try 
        {
            $lang = $request->query('lang', 'en');

            // validate language
            if (!in_array($lang, Genre::AVAILABLE_LANGUAGES)) 
            {
                Log::warning("Invalid language requested: {$lang}. Defaulting to English.");
                $lang = 'en';
            }

            // get all genres
            $genres = Genre::all();

            // add translated name directly to the response
            foreach ($genres as $genre) 
            {
                $genre->name = $genre->getTranslatedTitle($lang);
            }

            return response()->json($genres, 200);
        } 
        catch (\Exception $e) 
        {
            Log::error('Error fetching genres: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}
