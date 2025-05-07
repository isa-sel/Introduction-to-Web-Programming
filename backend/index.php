<?php
// Enable error reporting in development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set response headers
header('Content-Type: application/json');

// Get requested route
$route = isset($_GET['route']) ? $_GET['route'] : 'home';

// Handle routes
switch ($route) {
    case 'home':
        echo json_encode([
            'status' => 'success',
            'message' => 'Handball League Management API',
            'version' => '1.0.0',
            'available_routes' => [
                'teams' => '?route=teams',
                'players' => '?route=players',
                'venues' => '?route=venues',
                'matches' => '?route=matches',
                'statistics' => '?route=statistics',
                'documentation' => '?route=swagger'
            ]
        ]);
        break;
        
    case 'teams':
        try {
            // Load required files
            if (file_exists(__DIR__ . '/services/BaseService.php')) {
                require_once __DIR__ . '/services/BaseService.php';
            } else {
                throw new Exception("BaseService file not found");
            }
            
            if (file_exists(__DIR__ . '/services/TeamService.php')) {
                require_once __DIR__ . '/services/TeamService.php';
            } else {
                throw new Exception("TeamService file not found");
            }
            
            if (file_exists(__DIR__ . '/dao/TeamDao.php')) {
                require_once __DIR__ . '/dao/TeamDao.php';
            } else {
                throw new Exception("TeamDao file not found");
            }
            
            // Initialize service and get data
            $teamService = new TeamService();
            $teams = $teamService->getAll();
            
            echo json_encode([
                'status' => 'success',
                'data' => $teams
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
        break;
        
    case 'players':
        try {
            // Load required files with error checking
            if (!file_exists(__DIR__ . '/services/BaseService.php')) {
                throw new Exception("BaseService file not found");
            }
            require_once __DIR__ . '/services/BaseService.php';
            
            if (!file_exists(__DIR__ . '/services/PlayerService.php')) {
                throw new Exception("PlayerService file not found");
            }
            require_once __DIR__ . '/services/PlayerService.php';
            
            if (!file_exists(__DIR__ . '/dao/PlayerDao.php')) {
                throw new Exception("PlayerDao file not found");
            }
            require_once __DIR__ . '/dao/PlayerDao.php';
            
            if (!file_exists(__DIR__ . '/dao/TeamDao.php')) {
                throw new Exception("TeamDao file not found");
            }
            require_once __DIR__ . '/dao/TeamDao.php';
            
            $playerService = new PlayerService();
            $players = $playerService->getAll();
            
            echo json_encode([
                'status' => 'success',
                'data' => $players
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
        break;
        
    case 'venues':
        try {
            // Load required files
            require_once __DIR__ . '/services/BaseService.php';
            require_once __DIR__ . '/services/VenueService.php';
            require_once __DIR__ . '/dao/VenueDao.php';
            
            $venueService = new VenueService();
            $venues = $venueService->getAll();
            
            echo json_encode([
                'status' => 'success',
                'data' => $venues
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
        break;
        
    case 'matches':
        try {
            // Load required files
            require_once __DIR__ . '/services/BaseService.php';
            require_once __DIR__ . '/services/MatchService.php';
            require_once __DIR__ . '/dao/MatchDao.php';
            require_once __DIR__ . '/dao/TeamDao.php';
            require_once __DIR__ . '/dao/VenueDao.php';
            
            $matchService = new MatchService();
            $matches = $matchService->getAll();
            
            echo json_encode([
                'status' => 'success',
                'data' => $matches
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
        break;
        
    case 'statistics':
        try {
            // Load required files with file existence checks
            if (!file_exists(__DIR__ . '/services/BaseService.php')) {
                throw new Exception("BaseService file not found");
            }
            require_once __DIR__ . '/services/BaseService.php';
            
            if (!file_exists(__DIR__ . '/services/StatisticsService.php')) {
                throw new Exception("StatisticsService file not found");
            }
            require_once __DIR__ . '/services/StatisticsService.php';
            
            if (!file_exists(__DIR__ . '/dao/StatisticsDao.php')) {
                throw new Exception("StatisticsDao file not found");
            }
            require_once __DIR__ . '/dao/StatisticsDao.php';
            
            if (!file_exists(__DIR__ . '/dao/MatchDao.php')) {
                throw new Exception("MatchDao file not found");
            }
            require_once __DIR__ . '/dao/MatchDao.php';
            
            if (!file_exists(__DIR__ . '/dao/PlayerDao.php')) {
                throw new Exception("PlayerDao file not found");
            }
            require_once __DIR__ . '/dao/PlayerDao.php';
            
            $statisticsService = new StatisticsService();
            $statistics = $statisticsService->getAll();
            
            echo json_encode([
                'status' => 'success',
                'data' => $statistics
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
        break;
        
    case 'swagger':
        // Change header for swagger to render as JSON
        header('Content-Type: application/json');
        
        // Check if swagger.php exists
        if (!file_exists(__DIR__ . '/swagger.php')) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Swagger documentation file not found'
            ]);
            break;
        }
        
        // Include swagger.php for API documentation
        include __DIR__ . '/swagger.php';
        break;
        
    // Team operations with ID
    case strpos($route, 'team-') === 0:
        $parts = explode('-', $route);
        if (count($parts) >= 2) {
            $teamId = $parts[1];
            
            require_once __DIR__ . '/services/BaseService.php';
            require_once __DIR__ . '/services/TeamService.php';
            require_once __DIR__ . '/dao/TeamDao.php';
            
            $teamService = new TeamService();
            
            try {
                $team = $teamService->getById($teamId);
                echo json_encode([
                    'status' => 'success',
                    'data' => $team
                ]);
            } catch (Exception $e) {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid team ID format'
            ]);
        }
        break;
        
    default:
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'Route not found',
            'available_routes' => [
                'teams' => '?route=teams',
                'players' => '?route=players',
                'venues' => '?route=venues',
                'matches' => '?route=matches',
                'statistics' => '?route=statistics',
                'documentation' => '?route=swagger'
            ]
        ]);
}