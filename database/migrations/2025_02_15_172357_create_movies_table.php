<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

return new class extends Migration
{
    //create movies table, if not already exist
    //Run the migrations.

    public function up(): void
    {
        try
        {
            Schema::create('movies', function (Blueprint $table) 
            {
                $table->id();
                $table->bigInteger('tmdb_id')->unique();
                $table->string('title_en')->nullable();
                $table->string('title_pl')->nullable();
                $table->string('title_de')->nullable();
                $table->text('description_en')->nullable();
                $table->text('description_pl')->nullable();
                $table->text('description_de')->nullable();
                $table->string('poster_path')->nullable();
                $table->timestamps();
            });

            Log::info('Table "movies" created succesfully.');
        }
        catch (QueryException $e) 
        {
            // logs
            Log::error('An error occured while creating "movies": ' . $e->getMessage());

            // new exeption
            throw new \RuntimeException('An error occured while creating "movies".');
        }
    }

    //Reverse the migrations.
    // delete table movies
    public function down(): void
    {
        try
        {
            Schema::dropIfExists('movies');
            Log::info('Table "movies" deleted succefully.');
        }
        catch (QueryException $e) 
        {
            // logs
            Log::error('An error occured while deleting "movies": ' . $e->getMessage());

            // new exception
            throw new \RuntimeException('An error occured while deleting "movies".');
        }
    }
};
