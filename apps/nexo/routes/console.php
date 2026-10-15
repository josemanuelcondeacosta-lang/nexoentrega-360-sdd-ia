<?php

use App\Modules\AI\CaptureReplay;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('ai:capture {--model= : modelo de Gemini; vacío elige el primer Flash disponible}', function () {
    return (new CaptureReplay)->run($this, $this->option('model'));
})->purpose('Captura una respuesta real de Gemini como reproducción declarada (AC-05-13)');
