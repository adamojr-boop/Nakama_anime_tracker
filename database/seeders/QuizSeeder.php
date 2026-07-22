<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quiz;
use App\Models\Question;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Quiz Anime Shonen Classic
        $quiz1 = Quiz::create([
            'title' => 'Sfida Shonen Legend',
            'category' => 'Anime Shonen',
            'description' => 'Metti alla prova la tua conoscenza sui pilastri dello Shonen!',
            'difficulty' => 'medium',
        ]);

        Question::create([
            'quiz_id' => $quiz1->id,
            'question_text' => 'Qual è il nome della spada principale di Ichigo Kurosaki in Bleach?',
            'options' => ['Zangetsu', 'Senbonzakura', 'Kyoka Suigetsu', 'Wado Ichimonji'],
            'correct_option_index' => 0, // Zangetsu
            'time_limit_seconds' => 15,
        ]);

        Question::create([
            'quiz_id' => $quiz1->id,
            'question_text' => 'In Dragon Ball Z, chi elimina definitivamente Cell Perfetto?',
            'options' => ['Goku', 'Vegeta', 'Gohan', 'Trunks del Futuro'],
            'correct_option_index' => 2, // Gohan
            'time_limit_seconds' => 15,
        ]);

        Question::create([
            'quiz_id' => $quiz1->id,
            'question_text' => 'In Naruto, qual è la prima evocazione (Kuchiyose no Jutsu) completata da Naruto?',
            'options' => ['Gamabunta', 'Gamakichi', 'Gamatatsu', 'Un girino'],
            'correct_option_index' => 3, // Un girino
            'time_limit_seconds' => 15,
        ]);

        // 2. Quiz Attack on Titan
        $quiz2 = Quiz::create([
            'title' => 'L\'Attacco dei Giganti - Level Hard',
            'category' => 'Attack on Titan',
            'description' => 'Solo per veri membri della Squadra di Ricerca!',
            'difficulty' => 'hard',
        ]);

        Question::create([
            'quiz_id' => $quiz2->id,
            'question_text' => 'Qual è il vero nome del Gigante Colossale all\'inizio della storia?',
            'options' => ['Reiner Braun', 'Bertolt Hoover', 'Armin Arlert', 'Zeke Yeager'],
            'correct_option_index' => 1, // Bertolt Hoover
            'time_limit_seconds' => 15,
        ]);

        Question::create([
            'quiz_id' => $quiz2->id,
            'question_text' => 'Quale muro viene brecciato per primo nell\'anno 845?',
            'options' => ['Wall Maria', 'Wall Rose', 'Wall Sina', 'Wall Paradis'],
            'correct_option_index' => 0, // Wall Maria
            'time_limit_seconds' => 15,
        ]);
    }
}
