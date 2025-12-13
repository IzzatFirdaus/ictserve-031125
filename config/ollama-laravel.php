<?php

// Config for Cloudstudio/Ollama

return [
    'model' => env('OLLAMA_MODEL', 'llama2'),
    'url' => env('OLLAMA_URL', 'http://127.0.0.1:11434'),
    // D15 v3.6.0: Bahasa Melayu sahaja
    'default_prompt' => env('OLLAMA_DEFAULT_PROMPT', 'Anda adalah pembantu AI untuk sistem ICTServe MOTAC. Sentiasa jawab dalam Bahasa Melayu sahaja dan berikan maklumat yang tepat dan membantu.'),

    /*
    |--------------------------------------------------------------------------
    | Keep Alive Duration
    |--------------------------------------------------------------------------
    |
    | Controls how long models stay loaded in memory after a request.
    | Set to null to use the Ollama server's default configuration.
    | Examples: '5m' (5 minutes), '1h' (1 hour), '30s' (30 seconds)
    |
    */
    'keep_alive' => env('OLLAMA_KEEP_ALIVE', null),

    'connection' => [
        'timeout' => env('OLLAMA_CONNECTION_TIMEOUT', 300),
    ],
    'headers' => [
        'Authorization' => 'Bearer '.env('OLLAMA_API_KEY'),
    ],
];
