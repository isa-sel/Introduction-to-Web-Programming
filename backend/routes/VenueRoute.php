<?php
require_once 'BaseRoute.php';

/**
 * Venue Routes
 */
class VenueRoute extends BaseRoute {
    
    /**
     * Register venue routes
     */
    protected function registerRoutes() {
        // Get all venues
        $this->app->route('GET /api/venues', function() {
            try {
                $venueService = $this->serviceManager->get('venue');
                $venues = $venueService->getAll();
                
                $this->success($venues);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 500);
            }
        });
        
        // Get venue by ID
        $this->app->route('GET /api/venues/@id', function($id) {
            try {
                $venueService = $this->serviceManager->get('venue');
                $venue = $venueService->getById($id);
                
                $this->success($venue);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 404);
            }
        });
        
        // Create venue
        $this->app->route('POST /api/venues', function() {
            $data = $this->getJsonBody();
            
            $this->validateRequired($data, ['name', 'location', 'address', 'capacity']);
            
            try {
                $venueService = $this->serviceManager->get('venue');
                $venueId = $venueService->create($data);
                
                $venue = $venueService->getById($venueId);
                
                $this->success($venue, 201);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Update venue
        $this->app->route('PUT /api/venues/@id', function($id) {
            $data = $this->getJsonBody();
            
            try {
                $venueService = $this->serviceManager->get('venue');
                $venueService->update($id, $data);
                
                $venue = $venueService->getById($id);
                
                $this->success($venue);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Delete venue
        $this->app->route('DELETE /api/venues/@id', function($id) {
            try {
                $venueService = $this->serviceManager->get('venue');
                $venueService->delete($id);
                
                $this->success(['message' => 'Venue deleted successfully']);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Get venues by location
        $this->app->route('GET /api/venues/location/@location', function($location) {
            try {
                $venueService = $this->serviceManager->get('venue');
                $venues = $venueService->getByLocation($location);
                
                $this->success($venues);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Get venues by minimum capacity
        $this->app->route('GET /api/venues/capacity/@capacity', function($capacity) {
            try {
                $venueService = $this->serviceManager->get('venue');
                $venues = $venueService->getByMinCapacity($capacity);
                
                $this->success($venues);
            } catch (Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
    }
}