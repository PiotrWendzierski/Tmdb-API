<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;
    // Dodajemy pola do fillable
    protected $fillable = 
    [
        'name_en',
        'name_pl',
        'name_de'
    ];

    public function getTranslatedTitle($lang = 'en')
    {
        return $this->{"name_{$lang}"} ?? $this->name_en;
    }
}

