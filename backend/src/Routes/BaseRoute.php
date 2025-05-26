<?php
namespace Ibu\Web\Routes;

use Ibu\Web\Services\ServiceManager;

/**
 * Base Route class for handling common route functions
 */
abstract class BaseRoute {
    protected $app;
    protected $serviceManager;

    public function __construct($app, $serviceManager) {
        $this->app = $app;
        $this->serviceManager = $serviceManager;
        $this->registerRoutes();
    }

    /**
     * Register routes - to be implemented by subclasses
     */
    abstract protected function registerRoutes();

    /**
     * Generate success response
     *
     * @param mixed $data Response data
     * @param int $status HTTP status code
     */
    protected function success($data, $status = 200) {
        $this->app->json([
            'status' => 'success',
            'data' => $data
        ], $status);
    }

    /**
     * Generate error response
     *
     * @param string $message Error message
     * @param int $status HTTP status code
     */
    protected function error($message, $status = 400) {
        $this->app->response()->status($status);
        $this->app->json([
            'status' => 'error',
            'message' => $message
        ]);
        $this->app->stop();
    }

    /**
     * Get request body as JSON
     *
     * @return array Parsed JSON data
     */
    protected function getJsonBody() {
        $requestBody = $this->app->request()->getBody();
        $data = json_decode($requestBody, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON data', 400);
        }

        return $data;
    }

    /**
     * Validate required parameters
     *
     * @param array $params Parameters to check
     * @param array $required Required parameter names
     */
    protected function validateRequired($params, $required) {
        foreach ($required as $field) {
            if (!isset($params[$field]) || (is_string($params[$field]) && trim($params[$field]) === '')) {
                $this->error("$field is required", 400);
            }
        }
    }
    
    /**
     * Get current authenticated user from Flight
     *
     * @return array|null User data or null if not authenticated
     */
    protected function getCurrentUser() {
        return $this->app->get('user');
    }
    
    /**
     * Check if current user is admin
     *
     * @return bool
     */
    protected function isAdmin() {
        $user = $this->getCurrentUser();
        return $user && $user['role'] === 'admin';
    }
    
    /**
     * Check if current user has specific role
     *
     * @param string $role Role to check
     * @return bool
     */
    protected function hasRole($role) {
        $user = $this->getCurrentUser();
        return $user && $user['role'] === $role;
    }
    
    /**
     * Check if request is authenticated and user is admin
     * Stops execution with error if not authorized
     *
     * @return bool
     */
    protected function requireAdmin() {
        if (!\Ibu\Web\Middleware\AuthMiddleware::authenticate()) {
            return false;
        }
        
        $user = $this->app->get('user');
        if ($user['role'] !== 'admin') {
            $this->error('Admin access required', 403);
            return false;
        }
        
        return true;
    }
    
    /**
     * Check if request is authenticated
     * Stops execution with error if not authenticated
     *
     * @return bool
     */
    protected function requireAuth() {
        return \Ibu\Web\Middleware\AuthMiddleware::authenticate();
    }
}