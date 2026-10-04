<?php

use Illuminate\Support\Facades\Schedule;

// The commands live in app/Console/Commands; this file only says when they run.
Schedule::command('erp:depreciate')->lastDayOfMonth('23:30');
Schedule::command('erp:recurring')->dailyAt('06:00');
