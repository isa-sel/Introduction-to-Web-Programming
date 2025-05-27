<?php
namespace Ibu\Web\Services;

use Ibu\Web\Dao\PlayerDao;
use Ibu\Web\Dao\TeamDao;
/**
 * Service for handling Player operations
 */
class PlayerService extends BaseService {
    private $teamDao;
    
    public function __construct() {
        $this->dao = new PlayerDao();
        $this->teamDao = new TeamDao();
    }

    /**
     * Get players by team
     * 
     * @param int $teamId Team ID to filter by
     * @return array Players in the specified team
     */
    public function getByTeam($teamId) {
        if (!is_numeric($teamId) || $teamId <= 0) {
            throw new \InvalidArgumentException("Invalid team ID provided");
        }
        
        // Check if team exists
        $team = $this->teamDao->getById($teamId);
        if (!$team) {
            throw new \Exception("Team with ID $teamId not found");
        }
        
        return $this->dao->getByTeam($teamId);
    }

    /**
     * Get players by position
     * 
     * @param string $position Position to filter by
     * @return array Players with the specified position
     */
    public function getByPosition($position) {
        if (empty($position)) {
            throw new \InvalidArgumentException("Position cannot be empty");
        }
        
        $validPositions = ['Goalkeeper', 'Left Wing', 'Right Wing', 'Left Back', 'Right Back', 'Center Back', 'Pivot'];
        if (!in_array($position, $validPositions)) {
            throw new \InvalidArgumentException("Position must be one of: " . implode(', ', $validPositions));
        }
        
        return $this->dao->getByPosition($position);
    }

    /**
     * Get players by nationality
     * 
     * @param string $nationality Nationality to filter by
     * @return array Players with the specified nationality
     */
    public function getByNationality($nationality) {
        if (empty($nationality)) {
            throw new \InvalidArgumentException("Nationality cannot be empty");
        }
        
        return $this->dao->getByNationality($nationality);
    }

    /**
     * Check if jersey number is available in a team
     * 
     * @param int $teamId Team ID
     * @param int $jerseyNumber Jersey number to check
     * @param int|null $excludePlayerId Player ID to exclude from check (for updates)
     * @return bool True if jersey number is available
     */
    public function isJerseyNumberAvailable($teamId, $jerseyNumber, $excludePlayerId = null) {
        $players = $this->getByTeam($teamId);
        
        foreach ($players as $player) {
            if ($player['jersey_number'] == $jerseyNumber && $player['id'] != $excludePlayerId) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Validate player data before saving
     * 
     * @param array $data Player data to validate
     * @param bool $isCreating Whether this is for creating a new player
     * @throws \InvalidArgumentException If validation fails
     */
    protected function validateData($data, $isCreating = true) {
        $requiredFields = ['team_id', 'first_name', 'last_name', 'date_of_birth', 'nationality', 'position', 'jersey_number'];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || (is_string($data[$field]) && trim($data[$field]) === '')) {
                throw new \InvalidArgumentException("$field is required and cannot be empty");
            }
        }
        
        // Validate team_id
        if (!is_numeric($data['team_id']) || $data['team_id'] <= 0) {
            throw new \InvalidArgumentException("Invalid team ID provided");
        }
        
        // Check if team exists
        $team = $this->teamDao->getById($data['team_id']);
        if (!$team) {
            throw new \Exception("Team with ID {$data['team_id']} not found");
        }
        
        // Validate names
        if (strlen($data['first_name']) > 50) {
            throw new \InvalidArgumentException("First name cannot exceed 50 characters");
        }
        
        if (strlen($data['last_name']) > 50) {
            throw new \InvalidArgumentException("Last name cannot exceed 50 characters");
        }
        
        // Validate date_of_birth
        $dob = new \DateTime($data['date_of_birth']);
        $now = new \DateTime();
        $age = $now->diff($dob)->y;
        
        if ($age < 14 || $age > 45) {
            throw new \InvalidArgumentException("Player's age must be between 14 and 45 years");
        }
        
        // Validate nationality
        if (strlen($data['nationality']) > 50) {
            throw new \InvalidArgumentException("Nationality cannot exceed 50 characters");
        }
        
        // Validate position
        $validPositions = ['Goalkeeper', 'Left Wing', 'Right Wing', 'Left Back', 'Right Back', 'Center Back', 'Pivot'];
        if (!in_array($data['position'], $validPositions)) {
            throw new \InvalidArgumentException("Position must be one of: " . implode(', ', $validPositions));
        }
        
        // Validate jersey_number
        if (!is_numeric($data['jersey_number']) || $data['jersey_number'] < 1 || $data['jersey_number'] > 99) {
            throw new \InvalidArgumentException("Jersey number must be between 1 and 99");
        }
        
        // Check if jersey number is available in the team
        $playerId = isset($data['id']) ? $data['id'] : null;
        if (!$this->isJerseyNumberAvailable($data['team_id'], $data['jersey_number'], $playerId)) {
            throw new \InvalidArgumentException("Jersey number {$data['jersey_number']} is already taken in this team");
        }
    }
}