<?php
/**
 * Route Registration
 * 
 * This file registers all routes for the API
 */

// Include route files
require_once __DIR__ . '/TeamRoute.php';
require_once __DIR__ . '/PlayerRoute.php';
require_once __DIR__ . '/VenueRoute.php';
require_once __DIR__ . '/MatchRoute.php';
require_once __DIR__ . '/StatisticsRoute.php';

/**
 * Register all routes with the Flight app
 * 
 * @param \Flight $app Flight app instance
 */
function registerRoutes($app) {
    // Register API documentation route
    $app->route('GET /api-docs', function() {
        include __DIR__ . '/../swagger.php';
    });
    
    // Register all entity routes
    new TeamRoute($app);
    new PlayerRoute($app);
    new VenueRoute($app);
    new MatchRoute($app);
    new StatisticsRoute($app);
    
    // Register default API route
    $app->route('GET /api', function() {
        echo json_encode([
            'status' => 'success',
            'message' => 'Handball League Management API',
            'version' => '1.0.0',
            'documentation' => '/api-docs'
        ]);
    });
}