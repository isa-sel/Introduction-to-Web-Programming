<?php
namespace Ibu\Web\Services;

/**
 * Service Manager to handle service initialization
 */
class ServiceManager {
    private static $instance = null;
    private $services = [];
    private $serviceFactories = [];
    
    /**
     * Private constructor to enforce singleton pattern
     */
    private function __construct() {
        // Initialize default service mappings
        $this->initializeDefaultServices();
    }
    
    /**
     * Get instance (singleton pattern)
     * 
     * @return ServiceManager The ServiceManager instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new ServiceManager();
        }
        
        return self::$instance;
    }
    
    /**
     * Initialize default service mappings
     */
    private function initializeDefaultServices() {
        $this->serviceFactories = [
            'team' => function() { return new TeamService(); },
            'player' => function() { return new PlayerService(); },
            'venue' => function() { return new VenueService(); },
            'match' => function() { return new MatchService(); },
            'statistics' => function() { return new StatisticsService(); },
        ];
    }
    
    /**
     * Register a service with a factory function
     * 
     * @param string $serviceName Name of the service
     * @param callable $factory Factory function that returns service instance
     */
    public function register($serviceName, callable $factory) {
        $this->serviceFactories[$serviceName] = $factory;
        
        // Remove existing instance if it exists (force recreation on next get)
        if (isset($this->services[$serviceName])) {
            unset($this->services[$serviceName]);
        }
    }
    
    /**
     * Get a service instance
     * 
     * @param string $serviceName Name of the service to get
     * @return mixed The service instance
     * @throws \Exception If service not found
     */
    public function get($serviceName) {
        // Return existing instance if already created
        if (isset($this->services[$serviceName])) {
            return $this->services[$serviceName];
        }
        
        // Check if we have a factory for this service
        if (isset($this->serviceFactories[$serviceName])) {
            $factory = $this->serviceFactories[$serviceName];
            $this->services[$serviceName] = $factory();
            return $this->services[$serviceName];
        }
        
        // Fallback to legacy switch statement for backward compatibility
        return $this->createLegacyService($serviceName);
    }
    
    /**
     * Legacy service creation method (for backward compatibility)
     * 
     * @param string $serviceName Name of the service to create
     * @return mixed The service instance
     * @throws \Exception If service not found
     */
    private function createLegacyService($serviceName) {
        switch ($serviceName) {
            case 'team':
                $this->services[$serviceName] = new TeamService();
                break;
                
            case 'player':
                $this->services[$serviceName] = new PlayerService();
                break;
                
            case 'venue':
                $this->services[$serviceName] = new VenueService();
                break;
                
            case 'match':
                $this->services[$serviceName] = new MatchService();
                break;
                
            case 'statistics':
                $this->services[$serviceName] = new StatisticsService();
                break;
                
            default:
                throw new \Exception("Service '$serviceName' not found");
        }
        
        return $this->services[$serviceName];
    }
    
    /**
     * Check if a service is registered
     * 
     * @param string $serviceName Name of the service
     * @return bool True if service is registered
     */
    public function has($serviceName) {
        return isset($this->serviceFactories[$serviceName]);
    }
    
    /**
     * Clear a specific service or all services
     * 
     * @param string|null $serviceName Name of service to clear, or null to clear all
     */
    public function clear($serviceName = null) {
        if ($serviceName === null) {
            $this->services = [];
        } elseif (isset($this->services[$serviceName])) {
            unset($this->services[$serviceName]);
        }
    }
    
    /**
     * Get all registered service names
     * 
     * @return array Array of service names
     */
    public function getRegisteredServices() {
        return array_keys($this->serviceFactories);
    }
}