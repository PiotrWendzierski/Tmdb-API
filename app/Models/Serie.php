<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Serie extends Model
{
    use HasFactory;
    // add to filable
    protected $fillable = ['title_en','title_pl','title_de','description_en',
    'description_pl','description_de','poster_path'];

    public const AVAILABLE_LANGUAGES = ['en', 'pl', 'de'];
    
    //get translated series name, if not available, it set to english
    public function getTranslatedTitle(string $lang = 'en'): string
    {
        if (!in_array($lang, self::AVAILABLE_LANGUAGES)) 
        {
            Log::warning("Error: {$lang}. Used default language.");
            return $this->title_en;
        }

        return $this->{"title_{$lang}"} ?? $this->title_en;
    }

    //get translation of series decription
    public function getTranslatedDescription(string $lang = 'en'): string
    {
        if (!in_array($lang, self::AVAILABLE_LANGUAGES)) 
        {
            Log::warning("Error: {$lang}. Used default language.");
            return $this->description_en;
        }

        return $this->{"description_{$lang}"} ?? $this->description_en;
    }
}

