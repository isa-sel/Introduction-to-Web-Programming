<?php
namespace Ibu\Web\Services;

use Ibu\Web\Dao\TeamDao;

/**
 * Service for handling Team operations
 */
class TeamService extends BaseService {
    
    public function __construct() {
        $this->dao = new TeamDao();
    }

    /**
     * Get teams by category
     * 
     * @param string $category Category to filter by
     * @return array Teams in the specified category
     */
    public function getByCategory($category) {
        if (empty($category)) {
            throw new \InvalidArgumentException("Category cannot be empty");
        }
        
        return $this->dao->getByCategory($category);
    }

    /**
     * Get teams by location
     * 
     * @param string $location Location to filter by
     * @return array Teams in the specified location
     */
    public function getByLocation($location) {
        if (empty($location)) {
            throw new \InvalidArgumentException("Location cannot be empty");
        }
        
        return $this->dao->getByLocation($location);
    }

    /**
     * Validate team data before saving
     * 
     * @param array $data Team data to validate
     * @param bool $isCreating Whether this is for creating a new team
     * @throws \InvalidArgumentException If validation fails
     */
    protected function validateData($data, $isCreating = true) {
        $requiredFields = ['name', 'location', 'founded_year', 'category'];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new \InvalidArgumentException("$field is required and cannot be empty");
            }
        }
        
        // Validate name length
        if (strlen($data['name']) > 100) {
            throw new \InvalidArgumentException("Team name cannot exceed 100 characters");
        }
        
        // Validate location length
        if (strlen($data['location']) > 100) {
            throw new \InvalidArgumentException("Location cannot exceed 100 characters");
        }
        
        // Validate founded_year
        if (!is_numeric($data['founded_year']) || $data['founded_year'] < 1900 || $data['founded_year'] > date('Y')) {
            throw new \InvalidArgumentException("Founded year must be between 1900 and current year");
        }
        
        // Validate category
        $validCategories = ['Senior Men', 'Senior Women', 'Junior Men', 'Junior Women'];
        if (!in_array($data['category'], $validCategories)) {
            throw new \InvalidArgumentException("Category must be one of: " . implode(', ', $validCategories));
        }
    }
}