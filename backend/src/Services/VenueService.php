<?php
namespace Ibu\Web\Services;

use Ibu\Web\Dao\VenueDao;
/**
 * Service for handling Venue operations
 */
class VenueService extends BaseService {
    
    public function __construct() {
        $this->dao = new VenueDao();
    }

    /**
     * Get venues by location
     * 
     * @param string $location Location to filter by
     * @return array Venues in the specified location
     */
    public function getByLocation($location) {
        if (empty($location)) {
            throw new InvalidArgumentException("Location cannot be empty");
        }
        
        return $this->dao->getByLocation($location);
    }

    /**
     * Get venues with minimum capacity
     * 
     * @param int $capacity Minimum capacity
     * @return array Venues with at least the specified capacity
     */
    public function getByMinCapacity($capacity) {
        if (!is_numeric($capacity) || $capacity < 0) {
            throw new InvalidArgumentException("Capacity must be a non-negative number");
        }
        
        return $this->dao->getByMinCapacity($capacity);
    }

    /**
     * Validate venue data before saving
     * 
     * @param array $data Venue data to validate
     * @param bool $isCreating Whether this is for creating a new venue
     * @throws InvalidArgumentException If validation fails
     */
    protected function validateData($data, $isCreating = true) {
        $requiredFields = ['name', 'location', 'address', 'capacity'];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new InvalidArgumentException("$field is required and cannot be empty");
            }
        }
        
        // Validate name length
        if (strlen($data['name']) > 100) {
            throw new InvalidArgumentException("Venue name cannot exceed 100 characters");
        }
        
        // Validate location length
        if (strlen($data['location']) > 100) {
            throw new InvalidArgumentException("Location cannot exceed 100 characters");
        }
        
        // Validate address length
        if (strlen($data['address']) > 255) {
            throw new InvalidArgumentException("Address cannot exceed 255 characters");
        }
        
        // Validate capacity
        if (!is_numeric($data['capacity']) || $data['capacity'] < 50) {
            throw new InvalidArgumentException("Capacity must be at least 50");
        }
    }
}