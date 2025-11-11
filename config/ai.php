<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default AI Model
    |--------------------------------------------------------------------------
    |
    | This option controls the default AI model that will be used for
    | generating queries and formatting responses.
    |
    | Supported: "local", "openai", "anthropic"
    |
    */

    'default' => env('AI_MODEL', 'local'),

    /*
    |--------------------------------------------------------------------------
    | AI Model Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many AI model connections as you wish.
    |
    */

    'models' => [
        'local' => [
            'driver' => 'local',
            'base_url' => env('LOCAL_AI_URL', 'http://localhost:11434'),
            'model' => env('LOCAL_AI_MODEL', 'llama2'),
        ],

        'openai' => [
            'driver' => 'openai',
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_MODEL', 'gpt-4'),
        ],

        'anthropic' => [
            'driver' => 'anthropic',
            'api_key' => env('ANTHROPIC_API_KEY'),
            'model' => env('ANTHROPIC_MODEL', 'claude-3-sonnet-20240229'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Local AI Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for local AI models (Ollama, LM Studio, etc.)
    |
    */

    'local' => [
        'base_url' => env('LOCAL_AI_URL', 'http://localhost:11434'),
        'model' => env('LOCAL_AI_MODEL', 'llama2'),
        'timeout' => env('LOCAL_AI_TIMEOUT', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenAI Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for OpenAI models
    |
    */

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4'),
        'temperature' => 0.3,
    ],

];

