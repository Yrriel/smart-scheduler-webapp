<?php

// Load .env manually
$envPath = __DIR__ . '/.env';

if (!file_exists($envPath)) {
    die('.env file not found');
}

$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($lines as $line) {
    $line = trim($line);

    if ($line === '' || str_starts_with($line, '#')) {
        continue;
    }

    [$key, $value] = explode('=', $line, 2);
    $_ENV[$key] = $value;
}

// Validate
if (!isset($_ENV['OPENAI_API_KEY'])) {
    die('OPENAI_API_KEY not set');
}

// Expose variable for your existing code
$OPENAI_API_KEY = $_ENV['OPENAI_API_KEY'];
