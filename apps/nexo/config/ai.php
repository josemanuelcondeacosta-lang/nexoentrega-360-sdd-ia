<?php

// Asistente de IA (SPEC-05, ADR-004). La clave solo vive en el entorno del backend: nunca en Git, JavaScript ni evidencias.
return [
    // gemini | replay | fixture | none. Por defecto none: la demo y el CI funcionan sin clave usando el resumen alternativo.
    'provider' => env('AI_PROVIDER', 'none'),
    'key' => env('AI_API_KEY'),
    // Vacío: ai:capture elige el primer modelo Flash disponible para la clave y lo informa.
    'model' => env('AI_MODEL'),
    'gemini_base_url' => env('AI_GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com'),
    'force_ipv4' => (bool) env('AI_FORCE_IPV4', false),
    // Solo modelos 3.x: minimal | low | medium | high. Vacío: el valor por defecto del modelo.
    'thinking_level' => env('AI_THINKING_LEVEL'),
    // Plazo del proveedor (RF12): 5 s.
    'timeout_ms' => 5000,
    'fixtures_file' => resource_path('ai/ai-provider-fixtures.json'),
    'fixture_id' => env('AI_FIXTURE', 'AI-VALID'),
    'replay_dir' => resource_path('ai/replay'),
    // Nombre de archivo dentro de replay_dir; vacío usa la captura más reciente.
    'replay_file' => env('AI_REPLAY_FILE'),
];
