<?php

use App\Modules\AI\CaptureReplay;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('ai:capture {--model= : modelo de Gemini; vacío elige el primer Flash disponible} {--timeout= : plazo de la captura en ms (por defecto 5000, máximo 60000)} {--thinking= : nivel de pensamiento para modelos 3.x: minimal, low, medium o high}', function () {
    return (new CaptureReplay)->run($this, $this->option('model'), $this->option('timeout') ? (int) $this->option('timeout') : null, $this->option('thinking'));
})->purpose('Captura una respuesta real de Gemini como reproducción declarada (AC-05-13)');
