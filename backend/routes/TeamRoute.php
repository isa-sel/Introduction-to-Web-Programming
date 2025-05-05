<?php
require_once 'BaseRoute.php';

/**
 * Team Routes
 */
class TeamRoute extends BaseRoute {
    
    /**
     * Register team routes
     */
    protected function registerRoutes() {
        // Get all teams
        $this->app->route('GET /api/teams', function() {
            try {
                $teamService = $this->serviceManager->get('team');
                $teams = $teamService->getAll();
                
                $this->success($teams);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 500);
            }
        });
        
        // Get team by ID
        $this->app->route('GET /api/teams/@id', function($id) {
            try {
                $teamService = $this->serviceManager->get('team');
                $team = $teamService->getById($id);
                
                $this->success($team);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 404);
            }
        });
        
        // Create team
        $this->app->route('POST /api/teams', function() {
            $data = $this->getJsonBody();
            
            $this->validateRequired($data, ['name', 'location', 'founded_year', 'category']);
            
            try {
                $teamService = $this->serviceManager->get('team');
                $teamId = $teamService->create($data);
                
                $team = $teamService->getById($teamId);
                
                $this->success($team, 201);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Update team
        $this->app->route('PUT /api/teams/@id', function($id) {
            $data = $this->getJsonBody();
            
            try {
                $teamService = $this->serviceManager->get('team');
                $teamService->update($id, $data);
                
                $team = $teamService->getById($id);
                
                $this->success($team);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Delete team
        $this->app->route('DELETE /api/teams/@id', function($id) {
            try {
                $teamService = $this->serviceManager->get('team');
                $teamService->delete($id);
                
                $this->success(['message' => 'Team deleted successfully']);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Get teams by category
        $this->app->route('GET /api/teams/category/@category', function($category) {
            try {
                $teamService = $this->serviceManager->get('team');
                $teams = $teamService->getByCategory($category);
                
                $this->success($teams);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Get teams by location
        $this->app->route('GET /api/teams/location/@location', function($location) {
            try {
                $teamService = $this->serviceManager->get('team');
                $teams = $teamService->getByLocation($location);
                
                $this->success($teams);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
    }
}