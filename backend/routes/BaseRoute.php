<?php
require_once __DIR__ . '/../services/ServiceManager.php';

/**
 * Base Route class for handling common route functions
 */
abstract class BaseRoute {
    protected $app;
    protected $serviceManager;
    
    /**
     * Constructor
     * 
     * @param \Flight $app Flight instance
     */
    public function __construct($app) {
        $this->app = $app;
        $this->serviceManager = ServiceManager::getInstance();
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
        $this->app->json([
            'status' => 'error',
            'message' => $message
        ], $status);
        
        // Stop execution
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
}