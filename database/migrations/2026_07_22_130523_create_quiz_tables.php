<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    { // 1. Tabella dei Quiz
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category'); // es. "Anime Shonen", "Isekai", "Generale"
            $table->text('description')->nullable();
            $table->string('difficulty')->default('medium'); // easy, medium, hard
            $table->timestamps();
        });
        // 2. Tabella delle Domande
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->text('question_text');
            $table->json('options'); // ['Opzione A', 'Opzione B', 'Opzione C', 'Opzione D']
            $table->integer('correct_option_index'); // 0, 1, 2 o 3
            $table->integer('time_limit_seconds')->default(15); // secondi a disposizione
            $table->timestamps();
        });
        // 3. Tabella dei Tentativi/Punteggi Utente
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->integer('score')->default(0);
            $table->integer('correct_answers')->default(0);
            $table->integer('total_questions')->default(0);
            $table->integer('time_spent_seconds')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('quizzes');
    }
};
