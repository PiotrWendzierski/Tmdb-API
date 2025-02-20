<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Genre extends Model
{
    use HasFactory;
    // add to fillable
    protected $fillable = ['name_en','name_pl','name_de'];

    private const AVAILABLE_LANGUAGES = ['en', 'pl', 'de'];

    //get translated genre name, if not available, it set to english

    public function getTranslatedTitle(string $lang = 'en'): string
    {
        if (!in_array($lang, self::AVAILABLE_LANGUAGES)) 
        {
            Log::warning("Error: {$lang}. Used default language.");
            return $this->name_en;
        }

        return $this->{"name_{$lang}"} ?? $this->name_en;
    }
}

