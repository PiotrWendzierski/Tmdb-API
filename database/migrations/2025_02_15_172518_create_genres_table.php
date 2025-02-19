<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

return new class extends Migration
{
    //create genre table
    //Run the migrations.
    
    public function up(): void
    {
        try
        {
            Schema::create('genres', function (Blueprint $table) {
            $table->id();
            $table->string('name_en')->nullable();
            $table->string('name_pl')->nullable();
            $table->string('name_de')->nullable();
            $table->timestamps();

            Log::info('Table "genres" created succesfully.');
        });
        }
        catch (QueryException $e) 
        {
            // logs
            Log::error('An error occured while creating "genres": ' . $e->getMessage());

            // new exeption
            throw new \RuntimeException('An error occured while creating "genres".');
        }
        
    }
    

    //Reverse the migrations.
    //delete genres table
    public function down(): void
    {
        try
        {
            Schema::dropIfExists('genres');
            Log::info('Table "genres" deleted succefully.');
        }
        catch (QueryException $e) 
        {
            // logs
            Log::error('An error occured while deleting "genres": ' . $e->getMessage());

            // new exception
            throw new \RuntimeException('An error occured while deleting "genres".');
        }
    }
};
