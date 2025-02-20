<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\Serie;
use App\Models\Genre;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class TmdbApiService
{
    protected $apiKey;
    protected $languages = ['en', 'pl', 'de']; // available languages

    public function __construct()
    {
        $this->apiKey = env('TMDB_API_KEY');
    }

    //fetch data from TMDB API
    
    private function fetchData(string $endpoint, int $itemsToFetch, string $primaryLang): array
    {
        $data = [];
        $perPage = 20;
        $pagesToFetch = ceil($itemsToFetch / $perPage);
        $ids = [];

        try 
        {
            for ($page = 1; $page <= $pagesToFetch; $page++) 
            {
                $response = Http::get("https://api.themoviedb.org/3/{$endpoint}", [
                    'api_key' => $this->apiKey,
                    'language' => $primaryLang,
                    'page' => $page
                ]);
                $items = $response->json()['results'] ?? [];
                
                foreach ($items as $item) 
                {
                    $id = $item['id'];
                    if (!isset($data[$id])) 
                    {
                        $ids[] = $id;
                        $data[$id][$primaryLang] = $this->extractItemData($item, $primaryLang);
                    }
                    if (count($ids) >= $itemsToFetch) break 2;
                }
            }
        } 
        catch (\Exception $e) 
        {
            Log::error("Error fetching data from TMDB: {$e->getMessage()}");
        }

        return [$data, $ids];
    }

    //extract item data for a specific language
    
    private function extractItemData(array $item, string $lang): array
    {
        return [
            'title' => $item['title'] ?? $item['name'] ?? null,
            'description' => $item['overview'] ?? null,
            'poster_path' => $item['poster_path'] ?? null,
        ];
    }

    //Fetch translations for a list of items
    
    private function fetchTranslations(array &$data, array $ids, string $type): void
    {
        foreach ($this->languages as $lang) 
        {
            if ($lang === 'en') continue;
            foreach ($ids as $id) 
            {
                $this->fetchTranslation($data, $id, $lang, $type);
            }
        }
    }

    //Fetch a single translation

    private function fetchTranslation(array &$data, int $id, string $lang, string $type): void
    {
        try 
        {
            $response = Http::get("https://api.themoviedb.org/3/{$type}/{$id}", [
                'api_key' => $this->apiKey,
                'language' => $lang
            ]);
            $item = $response->json();
            $data[$id][$lang] = [
                'title' => $item['title'] ?? $item['name'] ?? null,
                'description' => $item['overview'] ?? null
            ];
        } 
        catch (\Exception $e) 
        {
            Log::error("Error fetching translation for {$type} {$id}: {$e->getMessage()}");
        }
    }

    //Save data to database

    private function saveData(array $data, $model, string $type): void
    {
        foreach ($data as $id => $dataItem) 
        {
            $attributes = $this->mapAttributes($dataItem);
            if ($type === 'movie') 
            {
                $model::updateOrCreate(['tmdb_id' => $id], $attributes);
            } 
            elseif ($type === 'tv') 
            {
                $model::updateOrCreate(['title_en' => $dataItem['en']['title']], $attributes);
            } 
            elseif ($type === 'genre') 
            {
                $model::updateOrCreate(['name_en' => $dataItem['en']['name']], $attributes);
            }
        }
    }

    //Map the attributes for saving
     
    private function mapAttributes(array $dataItem): array
    {
        return [
            'title_en' => $dataItem['en']['title'] ?? null,
            'title_pl' => $dataItem['pl']['title'] ?? null,
            'title_de' => $dataItem['de']['title'] ?? null,
            'description_en' => $dataItem['en']['description'] ?? null,
            'description_pl' => $dataItem['pl']['description'] ?? null,
            'description_de' => $dataItem['de']['description'] ?? null,
            'poster_path' => $dataItem['en']['poster_path'] ?? null,
        ];
    }

    //Fetch and save movies table
    
    public function fetchMovies(): void
    {
        $primaryLang = 'en';
        list($moviesData, $movieIds) = $this->fetchData('movie/popular', 50, $primaryLang);
        $this->fetchTranslations($moviesData, $movieIds, 'movie');
        $this->saveData($moviesData, Movie::class, 'movie');
    }

    //Fetch and save series table
    
    public function fetchSeries(): void
    {
        $primaryLang = 'en';
        list($seriesData, $seriesIds) = $this->fetchData('tv/popular', 10, $primaryLang);
        $this->fetchTranslations($seriesData, $seriesIds, 'tv');
        $this->saveData($seriesData, Serie::class, 'tv');
    }

    /**
     * Fetch and save genres table
     */
    public function fetchGenres(): void
    {
        $genresData = [];
        foreach ($this->languages as $lang) 
        {
            try 
            {
                $response = Http::get("https://api.themoviedb.org/3/genre/movie/list", [
                    'api_key' => $this->apiKey,
                    'language' => $lang
                ]);

                $genres = $response->json()['genres'] ?? [];
                foreach ($genres as $genre) {
                    $id = $genre['id'];
                    $genresData[$id][$lang] = ['name' => $genre['name']];
                }
            } 
            catch (\Exception $e) 
            {
                Log::error("Error fetching genres: {$e->getMessage()}");
            }
        }

        $this->saveData($genresData, Genre::class, 'genre');
    }
}
