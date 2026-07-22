<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CustomListController;
use App\Livewire\Lists\AnimeLists;
use App\Livewire\PlayQuiz;
use App\Livewire\ShowAnime;
use App\Livewire\UserDashboard;
use Illuminate\Support\Facades\Route;

Route::view('/', 'livewire.home')->name('home');
Route::get('/anime/{id}', ShowAnime::class)->name('anime.show');
Route::get('/lists/{id}', [CustomListController::class, 'show'])->name('lists.show');
Route::get('/quiz/{quizId}', PlayQuiz::class)->name('quiz.play');

// Rotte per gli ospiti (chi non è loggato)
Route::middleware('guest')->group(function () {
    // Registrazione
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // Login (Questa risolverà il tuo errore!)
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Rotta per il Logout (solo per chi è loggato)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', UserDashboard::class)->middleware(['auth'])->name('dashboard');
    Route::get('/my-lists', AnimeLists::class)->name('user.lists');
});
