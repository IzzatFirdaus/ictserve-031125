<?php

declare(strict_types=1);

/**
 * Konfigurasi Ollama AI untuk ICTServe v3.6.0
 *
 * Fail konfigurasi ini mengandungi tetapan untuk integrasi Ollama LLM
 * dengan sistem ICTServe. Selaras dengan D11 Technical Design Documentation v3.6.0.
 *
 * @version 3.6.0
 *
 * @author Pasukan Pembangunan BPM MOTAC
 *
 * @compliance D11 Technical Design Documentation v3.6.0
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Model Ollama Lalai (Default Ollama Model)
    |--------------------------------------------------------------------------
    |
    | Model LLM yang akan digunakan untuk semua operasi AI. Disyorkan
    | menggunakan model yang telah dikuantisasi untuk prestasi optimum.
    |
    */
    'model' => env('OLLAMA_MODEL', 'llama3.1'),

    /*
    |--------------------------------------------------------------------------
    | URL Pelayan Ollama (Ollama Server URL)
    |--------------------------------------------------------------------------
    |
    | URL lengkap ke pelayan Ollama. Untuk persekitaran pembangunan,
    | gunakan localhost. Untuk produksi, gunakan URL pelayan dalaman.
    |
    */
    'url' => env('OLLAMA_URL', 'http://127.0.0.1:11434'),

    /*
    |--------------------------------------------------------------------------
    | Prompt Lalai Sistem (Default System Prompt)
    |--------------------------------------------------------------------------
    |
    | Prompt sistem lalai dalam Bahasa Melayu sahaja mengikut D15 v3.6.0.
    | Prompt ini akan digunakan untuk semua interaksi AI.
    |
    */
    'default_prompt' => env('OLLAMA_DEFAULT_PROMPT', 'Anda adalah pembantu AI untuk sistem ICTServe MOTAC. Sentiasa jawab dalam Bahasa Melayu sahaja dan berikan maklumat yang tepat dan membantu.'),

    /*
    |--------------------------------------------------------------------------
    | Tetapan Sambungan (Connection Settings)
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk sambungan HTTP ke pelayan Ollama termasuk
    | timeout, percubaan semula, dan kelewatan.
    |
    */
    'connection' => [
        'timeout' => (int) env('OLLAMA_CONNECTION_TIMEOUT', 300), // 5 minit
        'retry_attempts' => 3, // Percubaan semula dengan exponential backoff
        'retry_delay' => 1000, // Kelewatan awal dalam milisaat (1s, 2s, 4s)
        'connect_timeout' => 30, // Timeout sambungan dalam saat
        'read_timeout' => 300, // Timeout bacaan dalam saat
    ],

    /*
    |--------------------------------------------------------------------------
    | Tetapan Cache (Cache Settings)
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk caching respons AI menggunakan Redis.
    | Cache meningkatkan prestasi untuk pertanyaan yang kerap.
    |
    */
    'cache' => [
        'enabled' => env('OLLAMA_CACHE_ENABLED', true),
        'driver' => env('OLLAMA_CACHE_DRIVER', 'redis'), // Gunakan Redis untuk cache
        'ttl' => [
            'faq_queries' => (int) env('OLLAMA_FAQ_CACHE_TTL', 3600), // 1 jam
            'embeddings' => (int) env('OLLAMA_EMBEDDING_CACHE_TTL', 86400), // 24 jam
            'common_queries' => (int) env('OLLAMA_COMMON_CACHE_TTL', 7200), // 2 jam
        ],
        'tags' => [
            'faq' => 'ollama:faq',
            'embedding' => 'ollama:embedding',
            'document' => 'ollama:document',
        ],
        'keys' => [
            'faq_query' => 'ollama:faq:{hash}',
            'embedding' => 'ollama:embedding:{doc_id}:{chunk_index}',
            'health_check' => 'ollama:health',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tetapan Embedding (Embedding Settings)
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk generasi dan caching embedding vektor.
    |
    */
    'embedding' => [
        'cache_ttl' => (int) env('OLLAMA_EMBEDDING_CACHE_TTL', 86400),
        'batch_size' => (int) env('OLLAMA_EMBEDDING_BATCH_SIZE', 10),
        'max_text_length' => (int) env('OLLAMA_EMBEDDING_MAX_TEXT', 8192),
        'performance_target' => (float) env('OLLAMA_EMBEDDING_TARGET_SECONDS', 0.1),
        'cache_key_prefix' => 'embedding',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tetapan Prestasi (Performance Settings)
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk mengoptimumkan prestasi AI mengikut
    | sasaran Core Web Vitals dan keperluan sistem.
    |
    */
    'performance' => [
        'max_response_time' => 5, // Masa respons maksimum dalam saat (Req 8.1)
        'quantized_model' => env('OLLAMA_QUANTIZED_MODEL', true), // Gunakan model terkuantisasi
        'context_window' => 4096, // Saiz tetingkap konteks dalam token
        'max_tokens' => 2048, // Token maksimum untuk respons
        'temperature' => 0.7, // Kreativiti model (0.0 = deterministik, 1.0 = kreatif)
        'top_p' => 0.9, // Nucleus sampling
        'keep_alive' => '5m', // Masa model kekal aktif
        'warm_up_queries' => 50, // Bilangan pertanyaan untuk pemanasan cache
    ],

    /*
    |--------------------------------------------------------------------------
    | Had Kadar (Rate Limiting)
    |--------------------------------------------------------------------------
    |
    | Tetapan untuk menghadkan kadar permintaan mengikut pengguna dan IP
    | untuk mengelakkan penyalahgunaan sistem.
    |
    */
    'rate_limiting' => [
        'per_user' => (int) env('OLLAMA_RATE_LIMIT_USER', 60), // Permintaan per minit setiap pengguna
        'per_ip' => (int) env('OLLAMA_RATE_LIMIT_IP', 1000), // Permintaan per jam setiap IP
        'burst_allowance' => 10, // Elaun burst untuk permintaan pantas
        'throttle_key' => 'ollama_throttle', // Kunci throttle untuk cache
    ],

    /*
    |--------------------------------------------------------------------------
    | Tetapan Keselamatan (Security Settings)
    |--------------------------------------------------------------------------
    |
    | Konfigurasi keselamatan untuk memastikan pemprosesan AI yang selamat
    | dan pematuhan dengan PDPA 2010.
    |
    */
    'security' => [
        'local_processing_only' => true, // Hanya pemprosesan tempatan
        'pii_detection' => true, // Pengesanan PII automatik
        'sanitize_logs' => true, // Sanitasi log untuk audit
        'encrypt_sensitive_data' => true, // Enkripsi data sensitif
        'audit_all_operations' => true, // Audit semua operasi AI
    ],

    /*
    |--------------------------------------------------------------------------
    | Tetapan Pemantauan (Monitoring Settings)
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk pemantauan prestasi menggunakan Laravel Pulse
    | dan integrasi dengan sistem pemantauan ICTServe.
    |
    */
    'monitoring' => [
        'pulse_enabled' => true, // Pemantauan Laravel Pulse
        'health_check_interval' => 60, // Selang pemeriksaan kesihatan dalam saat
        'performance_logging' => true, // Log prestasi operasi
        'slow_query_threshold' => 2.0, // Ambang pertanyaan perlahan dalam saat
        'memory_threshold' => 16 * 1024 * 1024 * 1024, // 16GB dalam bytes
        'cpu_threshold' => 80, // Peratus CPU maksimum
    ],

    /*
    |--------------------------------------------------------------------------
    | Tetapan Degradasi Anggun (Graceful Degradation)
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk degradasi anggun apabila sistem di bawah beban
    | atau mengalami masalah prestasi.
    |
    */
    'degradation' => [
        'enabled' => true,
        'tiers' => [
            1 => 'full_service', // Perkhidmatan penuh
            2 => 'cached_responses', // Respons cache sahaja
            3 => 'static_faq', // FAQ statik sahaja
            4 => 'emergency_mode', // Mod kecemasan
        ],
        'thresholds' => [
            'cpu_high' => 80, // Peratus CPU untuk degradasi
            'memory_high' => 90, // Peratus memori untuk degradasi
            'response_time_high' => 5.0, // Masa respons untuk degradasi
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tetapan Bahasa (Language Settings)
    |--------------------------------------------------------------------------
    |
    | Konfigurasi bahasa mengikut D15 v3.6.0 - Bahasa Melayu sahaja.
    | Tiada sokongan dwibahasa dalam v3.6.0.
    |
    */
    'language' => [
        'default_locale' => 'ms', // Bahasa Melayu sahaja (D15 v3.6.0)
        'force_malay_responses' => true, // Paksa respons Bahasa Melayu
        'technical_terms_english' => true, // Benarkan istilah teknikal dalam BI
        'fallback_language' => null, // Tiada bahasa sandaran (D15 v3.6.0)
    ],

    /*
    |--------------------------------------------------------------------------
    | Tetapan PII (PII Settings)
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk pengesanan dan sanitasi Maklumat Peribadi (PII)
    | mengikut PDPA 2010 dan D09 v3.6.0.
    |
    */
    'pii' => [
        'enabled' => env('OLLAMA_PII_DETECTION', true),
        'log_detections' => true, // Log pengesanan untuk audit
        'encrypt_sensitive' => true, // Enkripsi data sensitif (AES-256)
        'cache_ttl' => 3600, // TTL cache statistik
        'severity_threshold' => 'medium', // Tahap keterukan minimum untuk amaran
    ],

    /*
    |--------------------------------------------------------------------------
    | Tetapan Rangkaian (Network Settings)
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk pemantauan sambungan rangkaian dan
    | pengesanan sambungan luaran tidak dibenarkan (D11 v3.6.0).
    |
    */
    'network' => [
        'monitoring_enabled' => env('OLLAMA_NETWORK_MONITORING', true),
        'alert_threshold' => 3, // Bilangan percubaan sebelum amaran
        'alert_window_minutes' => 5, // Tetingkap masa untuk pengiraan percubaan
        'auto_block' => true, // Sekat domain secara automatik
        'notification_delay_minutes' => 5, // Kelewatan notifikasi admin
    ],

    /*
    |--------------------------------------------------------------------------
    | Tetapan Audit (Audit Settings)
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk hashing kriptografi dan pengesahan integriti
    | log audit mengikut D09 v3.6.0 Dual Audit System.
    |
    */
    'audit' => [
        'hashing_enabled' => env('OLLAMA_AUDIT_HASHING', true),
        'verification_interval_hours' => 24, // Selang pengesahan integriti
        'alert_on_tampering' => true, // Amaran jika pengubahsuaian dikesan
        'retention_days' => [
            'operational' => 90, // Log operasi: 90 hari
            'compliance' => 2555, // Log pematuhan: 7 tahun (2555 hari)
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tetapan Dokumen (Document Settings)
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk pemprosesan dokumen AI termasuk saiz fail,
    | chunking, dan penyimpanan.
    |
    */
    'document' => [
        'max_file_size' => 10485760, // 10MB
        'allowed_types' => ['pdf', 'docx', 'txt'],
        'chunk_size' => 750, // 500-1000 aksara
        'chunk_overlap' => 100, // Pertindihan antara chunks
        'storage_disk' => 'local',
        'storage_path' => 'documents',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tetapan Model (Model Settings)
    |--------------------------------------------------------------------------
    |
    | Konfigurasi khusus untuk model LLM yang berbeza dan
    | tetapan pengoptimuman model.
    |
    */
    'models' => [
        'llama3.1' => [
            'context_length' => 4096,
            'quantization' => 'Q4_K_M',
            'memory_usage' => '8GB',
            'recommended_for' => ['faq', 'general_chat'],
        ],
        'llama3.1:8b' => [
            'context_length' => 4096,
            'quantization' => 'Q4_K_M',
            'memory_usage' => '6GB',
            'recommended_for' => ['faq', 'quick_responses'],
        ],
        'llama3.1:70b' => [
            'context_length' => 4096,
            'quantization' => 'Q4_K_M',
            'memory_usage' => '40GB',
            'recommended_for' => ['document_analysis', 'complex_reasoning'],
        ],
    ],
];
