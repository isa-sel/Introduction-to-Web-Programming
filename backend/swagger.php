<?php
// Set the content type to JSON
header('Content-Type: application/json');

// OpenAPI specification - simplified version to avoid syntax errors
$openapi = [
    'openapi' => '3.0.0',
    'info' => [
        'title' => 'Handball League Management API',
        'description' => 'API for managing handball leagues, teams, players, venues, matches, and statistics',
        'version' => '1.0.0'
    ],
    'servers' => [
        [
            'url' => 'http://localhost/WebDev/Web%20Milestone%202/backend',
            'description' => 'Development server'
        ]
    ],
    'paths' => [
        '/index.php' => [
            'get' => [
                'summary' => 'API Information',
                'description' => 'Get information about the API'
            ]
        ],
        '/index.php?route=teams' => [
            'get' => [
                'summary' => 'Get all teams',
                'description' => 'Get a list of all teams',
                'tags' => ['Teams']
            ]
        ],
        '/index.php?route=players' => [
            'get' => [
                'summary' => 'Get all players',
                'description' => 'Get a list of all players',
                'tags' => ['Players']
            ]
        ],
        '/index.php?route=venues' => [
            'get' => [
                'summary' => 'Get all venues',
                'description' => 'Get a list of all venues',
                'tags' => ['Venues']
            ]
        ],
        '/index.php?route=matches' => [
            'get' => [
                'summary' => 'Get all matches',
                'description' => 'Get a list of all matches',
                'tags' => ['Matches']
            ]
        ],
        '/index.php?route=statistics' => [
            'get' => [
                'summary' => 'Get all statistics',
                'description' => 'Get a list of all statistics',
                'tags' => ['Statistics']
            ]
        ]
    ]
];

// Output the OpenAPI specification as JSON
echo json_encode($openapi, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);