<?php

use Illuminate\Support\Facades\Schedule;

// Aggiorna il palinsesto degli anime ogni 6 ore
Schedule::command('anime:sync-schedule')->everySixHours();
