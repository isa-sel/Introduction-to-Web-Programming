<?php
require_once 'TeamService.php';
require_once 'PlayerService.php';
require_once 'VenueService.php';
require_once 'MatchService.php';
require_once 'StatisticsService.php';

/**
 * Service Manager to handle service initialization
 */
class ServiceManager {
    private static $instance = null;
    private $services = [];
    
    /**
     * Private constructor to enforce singleton pattern
     */
    private function __construct() {
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
     * Get a service instance
     * 
     * @param string $serviceName Name of the service to get
     * @return mixed The service instance
     * @throws Exception If service not found
     */
    public function get($serviceName) {
        if (isset($this->services[$serviceName])) {
            return $this->services[$serviceName];
        }
        
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
                throw new Exception("Service '$serviceName' not found");
        }
        
        return $this->services[$serviceName];
    }
}