<?php
namespace Ibu\Web\Routes;

class UserRoute extends BaseRoute
{
    protected function registerRoutes()
    {
        // Get all users - ADMIN ONLY
        $this->app->route('GET /api/users', function() {
            if (!$this->requireAdmin()) {
                return;
            }
            
            try {
                $userService = $this->serviceManager->get('user');
                $users = $userService->getAll();
                
                // Remove passwords from response
                foreach ($users as &$user) {
                    unset($user['password']);
                }
                
                $this->success($users);
            } catch (\Exception $e) {
                $this->error($e->getMessage(), 500);
            }
        });
        
        // Get user by ID - ADMIN ONLY (or own profile)
        $this->app->route('GET /api/users/@id', function($id) {
            if (!$this->requireAuth()) {
                return;
            }
            
            $currentUser = $this->getCurrentUser();
            
            // Users can only view their own profile, admins can view any
            if ($currentUser['role'] !== 'admin' && $currentUser['id'] != $id) {
                $this->error('Access denied', 403);
                return;
            }
            
            try {
                $userService = $this->serviceManager->get('user');
                $user = $userService->getById($id);
                unset($user['password']); // Never send password
                
                $this->success($user);
            } catch (\Exception $e) {
                $this->error($e->getMessage(), 404);
            }
        });
        
        // Update user - ADMIN ONLY (or own profile for basic info)
        $this->app->route('PUT /api/users/@id', function($id) {
            if (!$this->requireAuth()) {
                return;
            }
            
            $currentUser = $this->getCurrentUser();
            $data = $this->getJsonBody();
            
            // Users can only update their own profile, admins can update any
            if ($currentUser['role'] !== 'admin' && $currentUser['id'] != $id) {
                $this->error('Access denied', 403);
                return;
            }
            
            // Regular users cannot change their role
            if ($currentUser['role'] !== 'admin' && isset($data['role'])) {
                unset($data['role']);
            }
            
            try {
                $userService = $this->serviceManager->get('user');
                $userService->update($id, $data);
                
                $user = $userService->getById($id);
                unset($user['password']);
                
                $this->success($user);
            } catch (\Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Delete user - ADMIN ONLY
        $this->app->route('DELETE /api/users/@id', function($id) {
            if (!$this->requireAdmin()) {
                return;
            }
            
            try {
                $userService = $this->serviceManager->get('user');
                $userService->delete($id);
                
                $this->success(['message' => 'User deleted successfully']);
            } catch (\Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // Get users by role - ADMIN ONLY
        $this->app->route('GET /api/users/role/@role', function($role) {
            if (!$this->requireAdmin()) {
                return;
            }
            
            try {
                $userService = $this->serviceManager->get('user');
                $users = $userService->getByRole($role);
                
                // Remove passwords from response
                foreach ($users as &$user) {
                    unset($user['password']);
                }
                
                $this->success($users);
            } catch (\Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
        
        // NOTE: Login and Register routes are handled in AuthRoute.php
        // This UserRoute is for user management operations
    }
}