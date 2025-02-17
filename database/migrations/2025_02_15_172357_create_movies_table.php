<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('movies', function (Blueprint $table) {
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
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
