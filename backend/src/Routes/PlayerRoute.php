<?php
namespace Ibu\Web\Routes;

/**
 * Player Routes
 */
class PlayerRoute extends BaseRoute {
    
    /**
     * Register player routes
     */
    protected function registerRoutes() {
        // Get all players - PUBLIC ACCESS
        $this->app->route('GET /api/players', function() {
            try {
                $playerService = $this->serviceManager->get('player');
                $players = $playerService->getAll();
                
                $this->success($players);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 500);
            }
        });
        
        // Get player by ID - PUBLIC ACCESS
        $this->app->route('GET /api/players/@id', function($id) {
            try {
                $playerService = $this->serviceManager->get('player');
                $player = $playerService->getById($id);
                
                $this->success($player);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 404);
            }
        });
        
        // Create player - ADMIN ONLY
        $this->app->route('POST /api/players', function() {
            if (!$this->requireAdmin()) {
                return;
            }
            
            $data = $this->getJsonBody();
            
            $this->validateRequired($data, [
                'team_id', 'first_name', 'last_name', 'date_of_birth', 
                'nationality', 'position', 'jersey_number'
            ]);
            
            try {
                $playerService = $this->serviceManager->get('player');
                $playerId = $playerService->create($data);
                
                $player = $playerService->getById($playerId);
                
                $this->success($player, 201);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Update player - ADMIN ONLY
        $this->app->route('PUT /api/players/@id', function($id) {
            if (!$this->requireAdmin()) {
                return;
            }
            
            $data = $this->getJsonBody();
            
            try {
                $playerService = $this->serviceManager->get('player');
                $playerService->update($id, $data);
                
                $player = $playerService->getById($id);
                
                $this->success($player);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Delete player - ADMIN ONLY
        $this->app->route('DELETE /api/players/@id', function($id) {
            if (!$this->requireAdmin()) {
                return;
            }
            
            try {
                $playerService = $this->serviceManager->get('player');
                $playerService->delete($id);
                
                $this->success(['message' => 'Player deleted successfully']);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Get players by team - PUBLIC ACCESS
        $this->app->route('GET /api/players/team/@teamId', function($teamId) {
            try {
                $playerService = $this->serviceManager->get('player');
                $players = $playerService->getByTeam($teamId);
                
                $this->success($players);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Get players by position - PUBLIC ACCESS
        $this->app->route('GET /api/players/position/@position', function($position) {
            try {
                $playerService = $this->serviceManager->get('player');
                $players = $playerService->getByPosition($position);
                
                $this->success($players);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Get players by nationality - PUBLIC ACCESS
        $this->app->route('GET /api/players/nationality/@nationality', function($nationality) {
            try {
                $playerService = $this->serviceManager->get('player');
                $players = $playerService->getByNationality($nationality);
                
                $this->success($players);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
    }
}