<?php
namespace Ibu\Web\Routes;

use Ibu\Web\Routes\BaseRoute;

/**
 * Team Routes
 */
class TeamRoute extends BaseRoute {
    
    /**
     * Register team routes
     */
    protected function registerRoutes() {
        $teamService = $this->serviceManager->get('team');
        
        // Get all teams - PUBLIC ACCESS
        $this->app->route('GET /api/teams', function() use ($teamService) {
            try {
                $teams = $teamService->getAll();
                $this->success($teams);
            } catch (\Exception $e) {
                $this->error($e->getMessage(), 500);
            }
        });
        
        // Get team by ID - PUBLIC ACCESS
        $this->app->route('GET /api/teams/@id', function($id) use ($teamService) {
            try {
                $team = $teamService->getById($id);
                $this->success($team);
            } catch (\Exception $e) {
                $this->error($e->getMessage(), 404);
            }
        });
        
        // Create team - ADMIN ONLY
        $this->app->route('POST /api/teams', function() use ($teamService) {
            if (!$this->requireAdmin()) {
                return;
            }
            
            $data = $this->getJsonBody();
            $this->validateRequired($data, ['name', 'location', 'founded_year', 'category']);
            
            try {
                $teamId = $teamService->create($data);
                $team = $teamService->getById($teamId);
                $this->success($team, 201);
            } catch (\Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Update team - ADMIN ONLY
        $this->app->route('PUT /api/teams/@id', function($id) use ($teamService) {
            if (!$this->requireAdmin()) {
                return;
            }
            
            $data = $this->getJsonBody();
            
            try {
                $teamService->update($id, $data);
                $team = $teamService->getById($id);
                $this->success($team);
            } catch (\Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Delete team - ADMIN ONLY
        $this->app->route('DELETE /api/teams/@id', function($id) use ($teamService) {
            if (!$this->requireAdmin()) {
                return;
            }
            
            try {
                $teamService->delete($id);
                $this->success(['message' => 'Team deleted successfully']);
            } catch (\Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Get teams by category - PUBLIC ACCESS
        $this->app->route('GET /api/teams/category/@category', function($category) use ($teamService) {
            try {
                $teams = $teamService->getByCategory($category);
                $this->success($teams);
            } catch (\Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Get teams by location - PUBLIC ACCESS
        $this->app->route('GET /api/teams/location/@location', function($location) use ($teamService) {
            try {
                $teams = $teamService->getByLocation($location);
                $this->success($teams);
            } catch (\Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
    }
}