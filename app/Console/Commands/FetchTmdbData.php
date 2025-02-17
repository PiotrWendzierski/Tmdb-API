<?php

namespace App\Console\Commands;

use App\Models\Movie;
use App\Models\Serie;
use App\Models\Genre;
use App\Services\TmdbApiService;
use Illuminate\Console\Command;

class FetchTmdbData extends Command
{
    protected $signature = 'fetch:tmdb-data';
    protected $description = 'Fetch data from TMDB API and save it to the database';
    protected $languages = ['en', 'pl', 'de']; // Obsługiwane języki

    public function handle()
    {
        $this->info('Fetching data from TMDB...');

        $tmdbService = new TmdbApiService();

        // Fetch Movies in multiple languages
        foreach ($this->languages as $lang) {
            $this->info("Fetching movies in {$lang}...");
            $movies = $tmdbService->fetchMovies($lang);

            // Sprawdzamy, czy zmienna $movies nie jest pusta
            if (empty($movies) || !isset($movies['results'])) {
                $this->warn("No movie data found for language {$lang}.");
                continue; // Pomiń ten język, jeśli brak danych
            }

            foreach ($movies['results'] as $movie) {
                Movie::updateOrCreate(
                    ['tmdb_id' => $movie['id']],
                    [
                        "title_{$lang}" => $movie['title'],
                        "description_{$lang}" => $movie['overview'],
                        'poster_path' => $movie['poster_path'],
                    ]
                );
            }
        }

        // Fetch Series in multiple languages
        foreach ($this->languages as $lang) {
            $this->info("Fetching series in {$lang}...");
            $series = $tmdbService->fetchSeries($lang);

            // Sprawdzamy, czy zmienna $series nie jest pusta
            if (empty($series) || !isset($series['results'])) {
                $this->warn("No series data found for language {$lang}.");
                continue; // Pomiń ten język, jeśli brak danych
            }

            foreach ($series['results'] as $serie) {
                Serie::updateOrCreate(
                    ['tmdb_id' => $serie['id']],
                    [
                        "title_{$lang}" => $serie['name'],
                        "description_{$lang}" => $serie['overview'],
                        'poster_path' => $serie['poster_path'],
                    ]
                );
            }
        }

        // Fetch Genres in multiple languages
        foreach ($this->languages as $lang) {
            $this->info("Fetching genres in {$lang}...");
            $genres = $tmdbService->fetchGenres($lang);

            // Sprawdzamy, czy zmienna $genres nie jest pusta
            if (empty($genres) || !isset($genres['genres'])) {
                $this->warn("No genre data found for language {$lang}.");
                continue; // Pomiń ten język, jeśli brak danych
            }

            foreach ($genres['genres'] as $genre) {
                Genre::updateOrCreate(
                    ['tmdb_id' => $genre['id']],
                    [
                        "name_{$lang}" => $genre['name'],
                    ]
                );
            }
        }

        $this->info('Data fetched and saved successfully! :))))');
    }
}
