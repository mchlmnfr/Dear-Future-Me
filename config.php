<?php

$databaseUrl = getenv('DATABASE_URL');

if ($databaseUrl) {
    $parts = parse_url($databaseUrl);

    return [
        'db' => [
            'host'     => $parts['host'] ?? 'aws-1-ap-south-1.pooler.supabase.com',
            'port'     => $parts['port'] ?? '6543',
            'database' => isset($parts['path']) ? ltrim($parts['path'], '/') : 'postgres',
            'username' => $parts['user'] ?? 'postgres.ibhkvdkrelayzharzhmg',
            'password' => isset($parts['pass']) ? urldecode($parts['pass']) : 'F6ICgS06nWwUN8D0',
        ],
        'base_url' => getenv('BASE_URL') ?: 'http://futureme',
    ];
}

return [
    'db' => [
        'host'     => getenv('DB_HOST') ?: 'aws-1-ap-south-1.pooler.supabase.com',
        'port'     => getenv('DB_PORT') ?: '6543',
        'database' => getenv('DB_NAME') ?: 'postgres',
        'username' => getenv('DB_USER') ?: 'postgres.ibhkvdkrelayzharzhmg',
        'password' => getenv('DB_PASS') ?: 'F6ICgS06nWwUN8D0',
    ],
    'base_url' => getenv('BASE_URL') ?: 'http://futureme',
];