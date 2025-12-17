<?php

declare(strict_types=1);

/**
 * Konfigurasi Ollama AI Integration untuk ICTServe v3.6.0
 *
 * Konfigurasi ini mematuhi D11 Technical Design Documentation v3.6.0
 * dan menyokong True Hybrid Architecture dengan Bahasa Melayu sahaja (D15 v3.6.0).
 *
 * @version 3.6.0
 *
 * @compliance D11 Technical Design Documentation v3.6.0
 *
 * @requirements 6.1, 6.2, 7.1, 8.1, 8.4, 8.5
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Feature Toggle
    |--------------------------------------------------------------------------
    |
    | Aktifkan atau nyahaktifkan integrasi Ollama AI secara keseluruhan.
    | Tetapkan kepada false untuk melumpuhkan semua ciri AI.
    |
    */
    'enabled' => env('OLLAMA_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Model Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi model LLM yang digunakan untuk pemprosesan AI.
    | Gunakan model terkuantisasi untuk prestasi optimum.
    |
    */
    'model' => env('OLLAMA_MODEL', 'gemma3:1b'),
    'quantized_model' => env('OLLAMA_QUANTIZED_MODEL', true),
    'embedding_model' => env('OLLAMA_EMBEDDING_MODEL', 'nomic-embed-text'),
    'default_prompt' => env('OLLAMA_DEFAULT_PROMPT', 'Anda adalah pembantu AI untuk sistem ICTServe MOTAC. Jawab dalam Bahasa Melayu sahaja.'),

    /*
    |--------------------------------------------------------------------------
    | Server Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi sambungan ke pelayan Ollama tempatan.
    | Semua pemprosesan AI berlaku secara tempatan untuk keselamatan data.
    |
    */
    'url' => env('OLLAMA_URL', 'http://127.0.0.1:11434'),
    'api_version' => env('OLLAMA_API_VERSION', 'v1'),

    /*
    |--------------------------------------------------------------------------
    | Connection Settings
    |--------------------------------------------------------------------------
    |
    | Tetapan sambungan dan timeout untuk komunikasi dengan pelayan Ollama.
    |
    */
    'connection' => [
        'timeout' => (int) env('OLLAMA_CONNECTION_TIMEOUT', 60),
        'retry_attempts' => 2,
        'retry_delay' => 500, // milliseconds
        'connect_timeout' => 10, // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi caching untuk meningkatkan prestasi respons AI.
    | Menggunakan Redis untuk penyimpanan cache yang pantas.
    |
    */
    'cache' => [
        'enabled' => env('OLLAMA_CACHE_ENABLED', true),
        'ttl' => [
            'faq_queries' => (int) env('OLLAMA_CACHE_TTL', 3600), // 1 hour
            'embeddings' => 86400, // 24 hours
            'common_queries' => 7200, // 2 hours
        ],
        'driver' => env('OLLAMA_CACHE_DRIVER', 'redis'),
        'prefix' => 'ollama',
        'keys' => [
            'faq_query' => 'ollama:faq:{hash}',
            'embedding' => 'ollama:embedding:{hash}',
            'health_check' => 'ollama:health_check',
        ],
        'tags' => [
            'faq' => 'ollama:faq',
            'embedding' => 'ollama:embedding',
            'document' => 'ollama:document',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    |
    | Tetapan prestasi untuk memenuhi sasaran Core Web Vitals.
    | P95 < 5 saat, P50 < 2 saat mengikut keperluan 8.1.
    |
    */
    'performance' => [
        'max_response_time' => 5, // seconds (Req 8.1)
        'context_window' => 4096, // tokens
        'max_tokens' => 2048,
        'temperature' => 0.7,
        'top_p' => 0.9,
        'stream' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Had kadar untuk mengawal penggunaan sumber dan mencegah penyalahgunaan.
    |
    */
    'rate_limiting' => [
        'per_user' => 60, // requests per minute
        'per_ip' => 1000, // requests per hour
        'burst_allowance' => 10, // additional requests for short spikes
    ],

    /*
    |--------------------------------------------------------------------------
    | RAG Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk Retrieval-Augmented Generation pipeline.
    |
    */
    'rag' => [
        'similarity_threshold' => 0.1, // Lowered from 0.3 to 0.1 for better FAQ matching
        'max_results' => 5,
        'conversation_timeout' => 1800, // 30 minutes
        'max_conversation_turns' => 5,
        'fallback_enabled' => true,
        'chunk_size' => 1000, // characters
        'chunk_overlap' => 100, // characters
        'greeting_enabled' => true, // Enable friendly greeting detection
        'greeting_responses' => [
            'Selamat datang ke FAQ Bot ICTServe! 👋 Saya boleh membantu anda dengan soalan-soalan berkaitan sistem helpdesk dan pinjaman aset ICT. Apa yang boleh saya bantu hari ini?',
            'Hai! Saya FAQ Bot ICTServe. Saya di sini untuk menjawab soalan anda tentang perkhidmatan ICT. Ada apa yang saya boleh bantu?',
            'Hello! 😊 Saya adalah pembantu AI ICTServe. Sila tanya saya tentang sistem helpdesk, pinjaman aset ICT, atau sebarang soalan berkaitan perkhidmatan ICT.',
        ],
        'greeting_patterns' => [
            'hai', 'helo', 'hello', 'hi', 'hey',
            'salam', 'assalamualaikum', 'selamat pagi', 'selamat petang', 'selamat malam',
            'apa khabar', 'terima kasih', 'thanks', 'ok', 'okay',
            'good morning', 'good afternoon', 'good evening', 'good night',
            'thank you', 'thx', 'ty',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Embedding Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk embedding service.
    |
    */
    'embedding' => [
        'cache_ttl' => 86400, // 24 jam
        'batch_size' => 10,
        'max_text_length' => 8192,
        'performance_target' => 0.1, // 100ms
        'cache_key_prefix' => 'embedding',
    ],

    /*
    |--------------------------------------------------------------------------
    | PII Protection
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk perlindungan maklumat peribadi (PDPA 2010).
    |
    */
    'pii' => [
        'detection_enabled' => true,
        'sanitization_enabled' => true,
        'patterns' => [
            'ic' => '/\d{6}-\d{2}-\d{4}/',
            'phone' => '/\+?60\d{9,10}/',
            'email' => '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/',
            'passport' => '/[A-Z]\d{8}/',
            'bank_account' => '/\d{10,16}/',
            'credit_card' => '/\d{4}[\s-]?\d{4}[\s-]?\d{4}[\s-]?\d{4}/',
            'staff_id' => '/MOTAC\d{6}/',
        ],
        'redaction_text' => '[REDACTED]',
        'log_detection' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Network Security
    |--------------------------------------------------------------------------
    |
    | Konfigurasi keselamatan rangkaian untuk mengesan sambungan luaran.
    |
    */
    'network' => [
        'monitor_external_connections' => true,
        'allowed_domains' => [
            '127.0.0.1',
            'localhost',
            '::1',
        ],
        'blocked_domains' => [
            'openai.com',
            'anthropic.com',
            'cohere.ai',
            'huggingface.co',
        ],
        'alert_on_external_connection' => true,
        'alert_email' => env('OLLAMA_SECURITY_ALERT_EMAIL', 'admin@motac.gov.my'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk jejak audit dan pematuhan (D09 v3.6.0).
    |
    */
    'audit' => [
        'enabled' => true,
        'retention_days' => [
            'operational' => 90,
            'compliance' => 2555, // 7 years
        ],
        'hash_algorithm' => 'sha256',
        'immutable_logs' => true,
        'chain_verification' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Accessibility Settings (WCAG 2.2 AA)
    |--------------------------------------------------------------------------
    |
    | Konfigurasi kebolehcapaian mengikut D12-D14 v3.6.0.
    |
    */
    'accessibility' => [
        'screen_reader_support' => true,
        'keyboard_navigation' => true,
        'focus_indicators' => true,
        'aria_live_regions' => true,
        'color_contrast_compliant' => true,
        'minimum_touch_target' => 44, // pixels (44x44px)
        'loading_timeout' => 30, // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Language Settings (D15 v3.6.0)
    |--------------------------------------------------------------------------
    |
    | Konfigurasi bahasa - Bahasa Melayu sahaja untuk v3.6.0.
    |
    */
    'language' => [
        'default_locale' => 'ms',
        'supported_locales' => ['ms'], // Bahasa Melayu sahaja (D15 v3.6.0)
        'fallback_locale' => 'ms',
        'language_switcher_enabled' => false, // Dilumpuhkan untuk v3.6.0
    ],

    /*
    |--------------------------------------------------------------------------
    | Widget Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk floating chat bot widget.
    |
    */
    'widget' => [
        'enabled' => true,
        'position' => 'bottom-right',
        'max_conversation_turns' => 3,
        'auto_minimize_timeout' => 300, // 5 minutes
        'animation_duration' => 300, // milliseconds
        'z_index' => 9999,
        'mobile_responsive' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Prompts (Bahasa Melayu sahaja)
    |--------------------------------------------------------------------------
    |
    | Prompt lalai untuk sistem AI dalam Bahasa Melayu sahaja.
    |
    */
    'prompts' => [
        'system' => 'Anda adalah pembantu AI untuk sistem ICTServe MOTAC. Jawab dalam Bahasa Melayu sahaja. Gunakan konteks yang diberikan untuk memberikan jawapan yang tepat dan membantu.',
        'welcome' => 'Selamat datang ke FAQ Bot ICTServe! Bagaimana saya boleh membantu anda hari ini?',
        'fallback' => 'Maaf, saya tidak dapat memberikan jawapan yang tepat untuk pertanyaan anda. Sila hubungi pasukan sokongan ICT atau cipta tiket helpdesk.',
        'error' => 'Perkhidmatan AI tidak tersedia pada masa ini. Sila cuba lagi kemudian.',
        'no_results' => 'Tiada hasil yang berkaitan ditemui. Sila cuba dengan kata kunci yang berbeza.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring & Alerting
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk pemantauan prestasi dan amaran sistem.
    |
    */
    'monitoring' => [
        'enabled' => true,
        'metrics_collection_interval' => 60, // seconds
        'performance_thresholds' => [
            'response_time_p95' => 5.0, // seconds
            'cpu_usage' => 80, // percentage
            'memory_usage' => 90, // percentage
            'error_rate' => 5, // percentage
        ],
        'alerting' => [
            'email_enabled' => true,
            'slack_enabled' => false,
            'webhook_enabled' => false,
        ],
    ],
];
