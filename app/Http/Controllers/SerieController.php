<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Serie;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class SerieController extends Controller
{
    //all series with tranlations
    public function index(Request $request): JsonResponse
    {
        try 
        {
            $lang = $request->query('lang', 'en');

            // vlidate language
            if (!in_array($lang, Serie::AVAILABLE_LANGUAGES)) {
                Log::warning("Invalid language requested: {$lang}. Defaulting to English.");
                $lang = 'en';
            }

            // get all series and translate titles and descriptions
            $series = Serie::all();

            // add translated title and description directly to the response
            foreach ($series as $serie) 
            {
                $serie->title = $serie->getTranslatedTitle($lang);
                $serie->description = $serie->getTranslatedDescription($lang);
            }

            return response()->json($series, 200);
        } 
        catch (\Exception $e) 
        {
            Log::error('Error fetching series: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}
