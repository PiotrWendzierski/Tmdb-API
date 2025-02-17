<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    use HasFactory;
    // Dodajemy pola do fillable
    protected $fillable = 
    [
        'title_en',
        'title_pl',
        'title_de',
        'description_en',
        'description_pl',
        'description_de',
        'poster_path'
    ];

    public function getTranslatedTitle($lang = 'en')
    {
        return $this->{"title_{$lang}"} ?? $this->title_en;
    }

    public function getTranslatedDescription($lang = 'en')
    {
        return $this->{"description_{$lang}"} ?? $this->description_en;
    }
}

