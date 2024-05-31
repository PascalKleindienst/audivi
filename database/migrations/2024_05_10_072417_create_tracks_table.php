<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracks', static function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('position');
            $table->string('path')->nullable();
            $table->float('start')->nullable();
            $table->float('end')->nullable();
            $table->integer('duration')->nullable();
            $table->foreignId('audio_book_id')->constrained()->onDelete('cascade');
            $table->unique(['audio_book_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracks');
    }
};
