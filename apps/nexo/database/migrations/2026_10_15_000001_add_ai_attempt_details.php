<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ADR-004 (CL-05): el intento registra origen y motivo sanitizado; nunca el prompt, la clave ni el texto del modelo.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_attempts', function (Blueprint $t) {
            $t->string('origin_type')->nullable();
            $t->string('reason')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('ai_attempts', fn (Blueprint $t) => $t->dropColumn(['origin_type', 'reason']));
    }
};
