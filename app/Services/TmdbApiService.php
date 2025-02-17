<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\Movie;
use App\Models\Serie;
use App\Models\Genre;

class TmdbApiService
{
    protected $client;
    protected $apiKey;
    protected $languages = ['en', 'pl', 'de']; // Obsługiwane języki

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('TMDB_API_KEY');
    }

    public function fetchMovies()
    {
        $moviesData = [];
    
        foreach ($this->languages as $lang) {
            $response = $this->client->get("https://api.themoviedb.org/3/movie/popular?api_key={$this->apiKey}&language={$lang}");
            $movies = json_decode($response->getBody(), true)['results'] ?? [];
    
            foreach ($movies as $movie) {
                $id = $movie['id'];
    
                $moviesData[$id][$lang] = [
                    'title' => $movie['title'],
                    'description' => $movie['overview'],
                    'poster_path' => $movie['poster_path']
                ];
            }
        }
    
        foreach ($moviesData as $id => $data) {
            Movie::updateOrCreate(
                ['tmdb_id' => $id],
                [
                    'title_en' => $data['en']['title'] ?? null,
                    'title_pl' => $data['pl']['title'] ?? null,
                    'title_de' => $data['de']['title'] ?? null,
                    'description_en' => $data['en']['description'] ?? null,
                    'description_pl' => $data['pl']['description'] ?? null,
                    'description_de' => $data['de']['description'] ?? null,
                    'poster_path' => $data['en']['poster_path'] ?? null,  // Przechowywanie ścieżki do plakatu
                ]
            );
        }
    }
    
    

    public function fetchSeries()
    {
        $seriesData = [];
    
        foreach ($this->languages as $lang) {
            $response = $this->client->get("https://api.themoviedb.org/3/tv/popular?api_key={$this->apiKey}&language={$lang}");
            $series = json_decode($response->getBody(), true)['results'] ?? [];
    
            foreach ($series as $serie) {
                $id = $serie['id'];
    
                $seriesData[$id][$lang] = [
                    'title' => $serie['name'],
                    'description' => $serie['overview'],
                    'poster_path' => $serie['poster_path']
                ];
            }
        }
    
        foreach ($seriesData as $id => $data) {
            Serie::updateOrCreate(
                ['id' => $id],
                [
                    'title_en' => $data['en']['title'] ?? null,
                    'title_pl' => $data['pl']['title'] ?? null,
                    'title_de' => $data['de']['title'] ?? null,
                    'description_en' => $data['en']['description'] ?? null,
                    'description_pl' => $data['pl']['description'] ?? null,
                    'description_de' => $data['de']['description'] ?? null,
                    'poster_path' => $data['en']['poster_path'] ?? null,  // Przechowywanie ścieżki do plakatu
                ]
            );
        }
    }
    
    
    public function fetchGenres()
    {
        $genresData = [];
    
        foreach ($this->languages as $lang) {
            $response = $this->client->get("https://api.themoviedb.org/3/genre/movie/list?api_key={$this->apiKey}&language={$lang}");
            $genres = json_decode($response->getBody(), true)['genres'] ?? [];
    
            foreach ($genres as $genre) {
                $id = $genre['id'];
    
                $genresData[$id][$lang] = [
                    'name' => $genre['name']
                ];
            }
        }
    
        foreach ($genresData as $id => $data) {
            Genre::updateOrCreate(
                ['id' => $id],
                [
                    'name_en' => $data['en']['name'] ?? null,
                    'name_pl' => $data['pl']['name'] ?? null,
                    'name_de' => $data['de']['name'] ?? null,
                ]
            );
        }
    }
    
}