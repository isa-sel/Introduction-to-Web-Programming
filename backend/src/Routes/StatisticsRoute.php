<?php
namespace Ibu\Web\Routes;

/**
 * Statistics Routes
 */
class StatisticsRoute extends BaseRoute {
    
    /**
     * Register statistics routes
     */
    protected function registerRoutes() {
        // Get all statistics - PUBLIC ACCESS
        $this->app->route('GET /api/statistics', function() {
            try {
                $statisticsService = $this->serviceManager->get('statistics');
                $statistics = $statisticsService->getAll();
                
                $this->success($statistics);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 500);
            }
        });
        
        // Get statistics by ID - PUBLIC ACCESS
        $this->app->route('GET /api/statistics/@id', function($id) {
            try {
                $statisticsService = $this->serviceManager->get('statistics');
                $statistics = $statisticsService->getById($id);
                
                $this->success($statistics);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 404);
            }
        });
        
        // Create statistics - ADMIN ONLY
        $this->app->route('POST /api/statistics', function() {
            if (!$this->requireAdmin()) {
                return;
            }
            
            $data = $this->getJsonBody();
            
            $this->validateRequired($data, ['match_id', 'player_id']);
            
            try {
                $statisticsService = $this->serviceManager->get('statistics');
                $statisticsId = $statisticsService->create($data);
                
                $statistics = $statisticsService->getById($statisticsId);
                
                $this->success($statistics, 201);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Update statistics - ADMIN ONLY
        $this->app->route('PUT /api/statistics/@id', function($id) {
            if (!$this->requireAdmin()) {
                return;
            }
            
            $data = $this->getJsonBody();
            
            try {
                $statisticsService = $this->serviceManager->get('statistics');
                $statisticsService->update($id, $data);
                
                $statistics = $statisticsService->getById($id);
                
                $this->success($statistics);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Delete statistics - ADMIN ONLY
        $this->app->route('DELETE /api/statistics/@id', function($id) {
            if (!$this->requireAdmin()) {
                return;
            }
            
            try {
                $statisticsService = $this->serviceManager->get('statistics');
                $statisticsService->delete($id);
                
                $this->success(['message' => 'Statistics deleted successfully']);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Get statistics by match - PUBLIC ACCESS
        $this->app->route('GET /api/statistics/match/@matchId', function($matchId) {
            try {
                $statisticsService = $this->serviceManager->get('statistics');
                $statistics = $statisticsService->getByMatch($matchId);
                
                $this->success($statistics);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Get statistics by player - PUBLIC ACCESS
        $this->app->route('GET /api/statistics/player/@playerId', function($playerId) {
            try {
                $statisticsService = $this->serviceManager->get('statistics');
                $statistics = $statisticsService->getByPlayer($playerId);
                
                $this->success($statistics);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Get top scorers - PUBLIC ACCESS
        $this->app->route('GET /api/statistics/top-scorers/@limit', function($limit) {
            try {
                $statisticsService = $this->serviceManager->get('statistics');
                $topScorers = $statisticsService->getTopScorers($limit);
                
                $this->success($topScorers);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Get player efficiency - PUBLIC ACCESS
        $this->app->route('GET /api/statistics/player/@playerId/efficiency', function($playerId) {
            try {
                $statisticsService = $this->serviceManager->get('statistics');
                $efficiency = $statisticsService->calculatePlayerEfficiency($playerId);
                
                $this->success(['efficiency' => $efficiency]);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
    }
}