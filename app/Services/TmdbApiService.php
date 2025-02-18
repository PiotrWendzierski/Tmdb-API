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
        $moviesToFetch = 50; // Liczba filmów do pobrania
        $moviesPerPage = 20; // TMDB zwraca 20 filmów na stronę
        $pagesToFetch = ceil($moviesToFetch / $moviesPerPage); // Ile stron pobrać
        $primaryLang = 'en'; // Pobieramy filmy tylko dla tego języka
        $movieIds = []; // Lista unikalnych ID filmów
    
        // 1. Pobieramy filmy w JEDNYM języku (np. en)
        for ($page = 1; $page <= $pagesToFetch; $page++) {
            $response = $this->client->get("https://api.themoviedb.org/3/movie/popular?api_key={$this->apiKey}&language={$primaryLang}&page={$page}");
            $movies = json_decode($response->getBody(), true)['results'] ?? [];
    
            foreach ($movies as $movie) {
                $id = $movie['id'];
    
                if (!isset($moviesData[$id])) { // Zapewniamy unikalność
                    $movieIds[] = $id;
    
                    $moviesData[$id][$primaryLang] = [
                        'title' => $movie['title'],
                        'description' => $movie['overview'],
                        'poster_path' => $movie['poster_path']
                    ];
                }
    
                if (count($movieIds) >= $moviesToFetch) {
                    break 2; // Mamy 50 filmów, kończymy pętlę
                }
            }
        }
    
        // 2. Pobieramy tłumaczenia dla zapisanych filmów
        foreach ($this->languages as $lang) {
            if ($lang === $primaryLang) continue; // Pomijamy główny język
    
            foreach ($movieIds as $id) {
                $response = $this->client->get("https://api.themoviedb.org/3/movie/{$id}?api_key={$this->apiKey}&language={$lang}");
                $movie = json_decode($response->getBody(), true);
    
                $moviesData[$id][$lang] = [
                    'title' => $movie['title'] ?? null,
                    'description' => $movie['overview'] ?? null
                ];
            }
        }
    
        //dd(count($moviesData)); // Teraz powinno zwrócić dokładnie 50!
    
        // 3. Zapisujemy do bazy danych
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
                    'poster_path' => $data['en']['poster_path'] ?? null,
                ]
            );
        }
    }
    


    public function fetchSeries()
    {
        $seriesData = [];
        $seriesToFetch = 10; // Liczba seriali do pobrania
        $seriesPerPage = 20; // TMDB zwraca 20 seriali na stronę
        $pagesToFetch = ceil($seriesToFetch / $seriesPerPage); // Obliczamy liczbę stron do pobrania
        $primaryLang = 'en'; // Pobieramy seriale tylko w jednym języku (np. angielski)
        $seriesIds = []; // Lista unikalnych ID seriali
    
        // 1. Pobieramy seriale w jednym języku (np. en)
        for ($page = 1; $page <= $pagesToFetch; $page++) {
            $response = $this->client->get("https://api.themoviedb.org/3/tv/popular?api_key={$this->apiKey}&language={$primaryLang}&page={$page}");
            $series = json_decode($response->getBody(), true)['results'] ?? [];
    
            foreach ($series as $serie) {
                $id = $serie['id'];
    
                if (!isset($seriesData[$id])) { // Zapewniamy unikalność
                    $seriesIds[] = $id;
    
                    $seriesData[$id][$primaryLang] = [
                        'title' => $serie['name'],
                        'description' => $serie['overview'],
                        'poster_path' => $serie['poster_path']
                    ];
                }
    
                if (count($seriesIds) >= $seriesToFetch) {
                    break 2; // Mamy 10 seriali, kończymy pętlę
                }
            }
        }
    
        // 2. Pobieramy tłumaczenia dla zapisanych seriali
        foreach ($this->languages as $lang) {
            if ($lang === $primaryLang) continue; // Pomijamy główny język
    
            foreach ($seriesIds as $id) {
                $response = $this->client->get("https://api.themoviedb.org/3/tv/{$id}?api_key={$this->apiKey}&language={$lang}");
                $serie = json_decode($response->getBody(), true);
    
                $seriesData[$id][$lang] = [
                    'title' => $serie['name'] ?? null,
                    'description' => $serie['overview'] ?? null
                ];
            }
        }
    
        // 3. Zapisujemy do bazy danych, ale tylko jeśli nie istnieje już taki rekord
        foreach ($seriesData as $id => $data) {
            // Sprawdzamy, czy już istnieje rekord z tymi tytułami
            $existingSeries = Serie::where('title_en', $data['en']['title'])
                ->orWhere('title_pl', $data['pl']['title'])
                ->orWhere('title_de', $data['de']['title'])
                ->exists();
    
            if (!$existingSeries) {
                Serie::updateOrCreate(
                    ['id' => $id], // Używamy 'id' zamiast 'tmdb_id'
                    [
                        'title_en' => $data['en']['title'] ?? null,
                        'title_pl' => $data['pl']['title'] ?? null,
                        'title_de' => $data['de']['title'] ?? null,
                        'description_en' => $data['en']['description'] ?? null,
                        'description_pl' => $data['pl']['description'] ?? null,
                        'description_de' => $data['de']['description'] ?? null,
                        'poster_path' => $data['en']['poster_path'] ?? null,
                    ]
                );
            }
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