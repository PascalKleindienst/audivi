<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audio_books', static function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->float('volume')->nullable();
            $table->text('description')->nullable();
            $table->float('rating')->nullable();
            $table->string('cover')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('audio_book_author', static function (Blueprint $table) {
            $table->foreignId('author_id')->constrained()->onDelete('cascade');
            $table->foreignId('audio_book_id')->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audio_books');
        Schema::dropIfExists('audio_book_author');
    }
};
