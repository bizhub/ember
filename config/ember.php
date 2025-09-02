<?php

return [
    // Your PrismPHP API key
    'prism_api_key' => env('PRISM_API_KEY', 'your-prism-api-key'),

    // Qdrant host for vector database
    'qdrant_host' => env('QDRANT_HOST', 'http://localhost:6333'),

    // Default AI model to use (for summarization, chat, etc.)
    'default_model' => env('AI_MODEL', 'gpt-4'),
];
