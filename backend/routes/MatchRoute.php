<?php
require_once 'BaseRoute.php';

/**
 * Match Routes
 */
class MatchRoute extends BaseRoute {
    
    /**
     * Register match routes
     */
    protected function registerRoutes() {
        // Get all matches
        $this->app->route('GET /api/matches', function() {
            try {
                $matchService = $this->serviceManager->get('match');
                $matches = $matchService->getAll();
                
                $this->success($matches);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 500);
            }
        });
        
        // Get match by ID
        $this->app->route('GET /api/matches/@id', function($id) {
            try {
                $matchService = $this->serviceManager->get('match');
                $match = $matchService->getById($id);
                
                $this->success($match);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 404);
            }
        });
        
        // Create match
        $this->app->route('POST /api/matches', function() {
            $data = $this->getJsonBody();
            
            $this->validateRequired($data, ['home_team_id', 'away_team_id', 'venue_id', 'match_time']);
            
            try {
                $matchService = $this->serviceManager->get('match');
                $matchId = $matchService->create($data);
                
                $match = $matchService->getById($matchId);
                
                $this->success($match, 201);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Update match
        $this->app->route('PUT /api/matches/@id', function($id) {
            $data = $this->getJsonBody();
            
            try {
                $matchService = $this->serviceManager->get('match');
                $matchService->update($id, $data);
                
                $match = $matchService->getById($id);
                
                $this->success($match);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Delete match
        $this->app->route('DELETE /api/matches/@id', function($id) {
            try {
                $matchService = $this->serviceManager->get('match');
                $matchService->delete($id);
                
                $this->success(['message' => 'Match deleted successfully']);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Update match result
        $this->app->route('PUT /api/matches/@id/result', function($id) {
            $data = $this->getJsonBody();
            
            $this->validateRequired($data, ['home_score', 'away_score']);
            
            try {
                $matchService = $this->serviceManager->get('match');
                
                $status = isset($data['status']) ? $data['status'] : 'completed';
                $matchService->updateResult($id, $data['home_score'], $data['away_score'], $status);
                
                $match = $matchService->getById($id);
                
                $this->success($match);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Get matches by team
        $this->app->route('GET /api/matches/team/@teamId', function($teamId) {
            try {
                $matchService = $this->serviceManager->get('match');
                $matches = $matchService->getByTeam($teamId);
                
                $this->success($matches);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Get matches by venue
        $this->app->route('GET /api/matches/venue/@venueId', function($venueId) {
            try {
                $matchService = $this->serviceManager->get('match');
                $matches = $matchService->getByVenue($venueId);
                
                $this->success($matches);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Get matches by status
        $this->app->route('GET /api/matches/status/@status', function($status) {
            try {
                $matchService = $this->serviceManager->get('match');
                $matches = $matchService->getByStatus($status);
                
                $this->success($matches);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Get upcoming matches
        $this->app->route('GET /api/matches/upcoming/@limit', function($limit) {
            try {
                $matchService = $this->serviceManager->get('match');
                $matches = $matchService->getUpcomingMatches($limit);
                
                $this->success($matches);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
    }
}