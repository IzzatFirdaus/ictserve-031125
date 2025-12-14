<?php

declare(strict_types=1);

return [
    // Kekalkan komponen AWS di `us-east-1` mengikut spesifikasi projek.
    // Jangan benarkan override melalui env untuk mengelakkan drift konfigurasi.
    'region' => 'us-east-1',
    'version' => env('AWS_BEDROCK_VERSION', 'latest'),
    'credentials' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
    ],

    // Toggle utama untuk integrasi Bedrock (Phase 13.2).
    'enabled' => env('AWS_BEDROCK_ENABLED', false),

    // Jika true, benarkan Bedrock hanya apabila pemanggil mengesahkan residensi Malaysia.
    // Nota: Sistem ini tidak melakukan geolokasi IP di sini.
    'enforce_malaysia_residency' => env('BEDROCK_ENFORCE_MALAYSIA_RESIDENCY', false),

    // Jika true, elakkan menghantar PII (IC/telefon/e-mel) ke cloud.
    'prevent_cloud_pii' => env('AWS_BEDROCK_PREVENT_CLOUD_PII', true),

    // Model lalai (diguna jika pemilihan model khusus tidak wujud).
    // Selaras dengan spesifikasi (Claude 4.5): Haiku sebagai lalai untuk respons pantas.
    'model_id' => env('AWS_BEDROCK_MODEL_ID', 'us.anthropic.claude-haiku-4-5-20251001-v1:0'),

    // Pemetaan model untuk ModelRouter (Claude 4.5 + Nova + Titan).
    // Selaras dengan spesifikasi (Claude 4.5) dan Nova models baharu.
    'models' => [
        // Claude 4.5 Models (Existing)
        'opus' => env('AWS_BEDROCK_MODEL_OPUS', 'global.anthropic.claude-opus-4-5-20251101-v1:0'),
        'sonnet' => env('AWS_BEDROCK_MODEL_SONNET', 'us.anthropic.claude-sonnet-4-5-20250929-v1:0'),
        'haiku' => env('AWS_BEDROCK_MODEL_HAIKU', 'us.anthropic.claude-haiku-4-5-20251001-v1:0'),

        // Amazon Nova Models (New)
        'nova_micro' => env('AWS_BEDROCK_MODEL_NOVA_MICRO', 'amazon.nova-micro-v1:0'),
        'nova_lite' => env('AWS_BEDROCK_MODEL_NOVA_LITE', 'amazon.nova-lite-v1:0'),
        'nova_pro' => env('AWS_BEDROCK_MODEL_NOVA_PRO', 'amazon.nova-pro-v1:0'),

        // Amazon Titan Models (New)
        'titan_text_lite' => env('AWS_BEDROCK_MODEL_TITAN_TEXT_LITE', 'amazon.titan-text-lite-v1'),
        'titan_text_express' => env('AWS_BEDROCK_MODEL_TITAN_TEXT_EXPRESS', 'amazon.titan-text-express-v1'),
        'titan_embed_image' => env('AWS_BEDROCK_MODEL_TITAN_EMBED_IMAGE', 'amazon.titan-embed-image-v1'),
    ],

    // Had kadar (rate limit) untuk panggilan Bedrock.
    // Mengikut spesifikasi: had berbeza untuk setiap model.
    'rate_limits' => [
        'enabled' => env('AWS_BEDROCK_RATE_LIMIT_ENABLED', true),

        // Keserasian ke belakang: jika had per-model tidak disediakan.
        'max_attempts_per_minute' => (int) env('AWS_BEDROCK_RATE_LIMIT_PER_MINUTE', 30),

        // Had per model (requests/tokens per minit).
        // Nota: Penguatkuasaan tokens/minit adalah berdasarkan anggaran.
        'models' => [
            // Claude 4.5 Models
            'opus' => [
                'requests_per_minute' => (int) env('AWS_BEDROCK_RATE_LIMIT_OPUS_RPM', 10),
                'tokens_per_minute' => (int) env('AWS_BEDROCK_RATE_LIMIT_OPUS_TPM', 20000),
            ],
            'sonnet' => [
                'requests_per_minute' => (int) env('AWS_BEDROCK_RATE_LIMIT_SONNET_RPM', 20),
                'tokens_per_minute' => (int) env('AWS_BEDROCK_RATE_LIMIT_SONNET_TPM', 40000),
            ],
            'haiku' => [
                'requests_per_minute' => (int) env('AWS_BEDROCK_RATE_LIMIT_HAIKU_RPM', 50),
                'tokens_per_minute' => (int) env('AWS_BEDROCK_RATE_LIMIT_HAIKU_TPM', 100000),
            ],

            // Amazon Nova Models
            'nova_micro' => [
                'requests_per_minute' => (int) env('AWS_BEDROCK_RATE_LIMIT_NOVA_MICRO_RPM', 100),
                'tokens_per_minute' => (int) env('AWS_BEDROCK_RATE_LIMIT_NOVA_MICRO_TPM', 150000),
            ],
            'nova_lite' => [
                'requests_per_minute' => (int) env('AWS_BEDROCK_RATE_LIMIT_NOVA_LITE_RPM', 80),
                'tokens_per_minute' => (int) env('AWS_BEDROCK_RATE_LIMIT_NOVA_LITE_TPM', 120000),
            ],
            'nova_pro' => [
                'requests_per_minute' => (int) env('AWS_BEDROCK_RATE_LIMIT_NOVA_PRO_RPM', 40),
                'tokens_per_minute' => (int) env('AWS_BEDROCK_RATE_LIMIT_NOVA_PRO_TPM', 60000),
            ],

            // Amazon Titan Models
            'titan_text_lite' => [
                'requests_per_minute' => (int) env('AWS_BEDROCK_RATE_LIMIT_TITAN_TEXT_LITE_RPM', 60),
                'tokens_per_minute' => (int) env('AWS_BEDROCK_RATE_LIMIT_TITAN_TEXT_LITE_TPM', 80000),
            ],
            'titan_text_express' => [
                'requests_per_minute' => (int) env('AWS_BEDROCK_RATE_LIMIT_TITAN_TEXT_EXPRESS_RPM', 30),
                'tokens_per_minute' => (int) env('AWS_BEDROCK_RATE_LIMIT_TITAN_TEXT_EXPRESS_TPM', 50000),
            ],
        ],
    ],

    // Tetapan penghalaan (boleh dioverride melalui konfigurasi admin berasaskan cache).
    'routing' => [
        'cache_ttl_seconds' => (int) env('AWS_BEDROCK_ROUTING_CACHE_TTL', 3600),
        'simple_faq_max_words' => (int) env('AWS_BEDROCK_SIMPLE_FAQ_MAX_WORDS', 50),
        'max_prompt_chars' => (int) env('AWS_BEDROCK_MAX_PROMPT_CHARS', 10000),
    ],

    // Polisi pengelasan data (asas) untuk mengawal pemprosesan cloud.
    'classification' => [
        // Jika true, data `internal` memerlukan persetujuan eksplisit sebelum dihantar ke cloud.
        'require_consent_for_internal' => (bool) env('AWS_BEDROCK_REQUIRE_CONSENT_FOR_INTERNAL', true),

        // Jika true, `restricted` akan disekat daripada pemprosesan AI (cloud & lokal).
        // Nota: Penguatkuasaan penuh perlu dilakukan di layer pemanggil.
        'block_restricted' => (bool) env('AWS_BEDROCK_BLOCK_RESTRICTED', true),
    ],

    // Kawalan bajet & kos (asas). Kos sebenar bergantung kepada konfigurasi model.
    'budgets' => [
        'enabled' => (bool) env('AWS_BEDROCK_BUDGET_ENABLED', false),
        'monthly_budget_usd' => (float) env('AWS_BEDROCK_MONTHLY_BUDGET_USD', 0),
        'hard_stop' => (bool) env('AWS_BEDROCK_BUDGET_HARD_STOP', false),
    ],
];
