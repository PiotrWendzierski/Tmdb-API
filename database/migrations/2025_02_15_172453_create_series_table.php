<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

return new class extends Migration
{
    //create series if not exist
    //Run the migrations.
    
    public function up(): void
    {
        try 
        {
            Schema::create('series', function (Blueprint $table) 
            {
                $table->id();
                $table->string('title_en')->nullable();
                $table->string('title_pl')->nullable();
                $table->string('title_de')->nullable();
                $table->text('description_en')->nullable();
                $table->text('description_pl')->nullable();
                $table->text('description_de')->nullable();
                $table->string('poster_path')->nullable();
                $table->timestamps();
            });

            Log::info('Table "series" created succesfully.');
        } 
        catch (QueryException $e) 
        {
            // logs
            Log::error('An error occured while creating "series": ' . $e->getMessage());

            // new exeption
            throw new \RuntimeException('An error occured while creating "series".');
        }
    }

    //Reverse the migrations.
    //delete series table
    public function down(): void
    {
        try 
        {
            Schema::dropIfExists('series');
            Log::info('Table "series" deleted succefully.');
        } 
        catch (QueryException $e) 
        {
            // logs
            Log::error('An error occured while deleting "series": ' . $e->getMessage());

            // new exception
            throw new \RuntimeException('An error occured while deleting "series".');
        }
    }
};
