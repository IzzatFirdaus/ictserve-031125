<?php

declare(strict_types=1);

return [
    'region' => env('AWS_BEDROCK_REGION', 'us-east-1'),
    'version' => env('AWS_BEDROCK_VERSION', 'latest'),
    'credentials' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
    ],
    'model_id' => env('AWS_BEDROCK_MODEL_ID', 'anthropic.claude-3-sonnet-20240229-v1:0'),
];
