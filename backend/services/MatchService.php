<?php
require_once 'BaseService.php';
require_once __DIR__ . '/../dao/MatchDao.php';
require_once __DIR__ . '/../dao/TeamDao.php';
require_once __DIR__ . '/../dao/VenueDao.php';

/**
 * Service for handling Match operations
 */
class MatchService extends BaseService {
    private $teamDao;
    private $venueDao;
    
    public function __construct() {
        $this->dao = new MatchDao();
        $this->teamDao = new TeamDao();
        $this->venueDao = new VenueDao();
    }

    /**
     * Get matches by team
     * 
     * @param int $teamId Team ID to filter by
     * @return array Matches involving the specified team
     */
    public function getByTeam($teamId) {
        if (!is_numeric($teamId) || $teamId <= 0) {
            throw new InvalidArgumentException("Invalid team ID provided");
        }
        
        // Check if team exists
        $team = $this->teamDao->getById($teamId);
        if (!$team) {
            throw new Exception("Team with ID $teamId not found");
        }
        
        return $this->dao->getByTeam($teamId);
    }

    /**
     * Get matches by venue
     * 
     * @param int $venueId Venue ID to filter by
     * @return array Matches at the specified venue
     */
    public function getByVenue($venueId) {
        if (!is_numeric($venueId) || $venueId <= 0) {
            throw new InvalidArgumentException("Invalid venue ID provided");
        }
        
        // Check if venue exists
        $venue = $this->venueDao->getById($venueId);
        if (!$venue) {
            throw new Exception("Venue with ID $venueId not found");
        }
        
        return $this->dao->getByVenue($venueId);
    }

    /**
     * Get matches by status
     * 
     * @param string $status Status to filter by
     * @return array Matches with the specified status
     */
    public function getByStatus($status) {
        $validStatuses = ['scheduled', 'in_progress', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            throw new InvalidArgumentException("Status must be one of: " . implode(', ', $validStatuses));
        }
        
        return $this->dao->getByStatus($status);
    }

    /**
     * Get upcoming matches
     * 
     * @param int $limit Maximum number of matches to return
     * @return array Upcoming matches
     */
    public function getUpcomingMatches($limit = 10) {
        if (!is_numeric($limit) || $limit <= 0) {
            throw new InvalidArgumentException("Limit must be a positive number");
        }
        
        return $this->dao->getUpcoming($limit);
    }

    /**
     * Update match result
     * 
     * @param int $id Match ID
     * @param int $homeScore Home team score
     * @param int $awayScore Away team score
     * @param string $status New match status
     * @return bool Result of the update operation
     */
    public function updateResult($id, $homeScore, $awayScore, $status = 'completed') {
        if (!is_numeric($id) || $id <= 0) {
            throw new InvalidArgumentException("Invalid match ID provided");
        }
        
        if (!is_numeric($homeScore) || $homeScore < 0) {
            throw new InvalidArgumentException("Home score must be a non-negative number");
        }
        
        if (!is_numeric($awayScore) || $awayScore < 0) {
            throw new InvalidArgumentException("Away score must be a non-negative number");
        }
        
        $validStatuses = ['in_progress', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            throw new InvalidArgumentException("Status must be one of: " . implode(', ', $validStatuses));
        }
        
        // Check if match exists
        $match = $this->dao->getById($id);
        if (!$match) {
            throw new Exception("Match with ID $id not found");
        }
        
        return $this->dao->updateResult($id, $homeScore, $awayScore, $status);
    }

    /**
     * Check if two teams can play at the same time
     * (prevents a team from being scheduled for two matches at the same time)
     * 
     * @param int $homeTeamId Home team ID
     * @param int $awayTeamId Away team ID
     * @param string $matchTime Match time
     * @param int|null $excludeMatchId Match ID to exclude from check (for updates)
     * @return bool True if schedule is valid
     */
    public function isScheduleValid($homeTeamId, $awayTeamId, $matchTime, $excludeMatchId = null) {
        // Get all matches for both teams
        $homeTeamMatches = $this->dao->getByTeam($homeTeamId);
        $awayTeamMatches = $this->dao->getByTeam($awayTeamId);
        
        // Convert match time to DateTime object
        $matchDateTime = new DateTime($matchTime);
        
        // Check home team matches
        foreach ($homeTeamMatches as $match) {
            if ($match['id'] == $excludeMatchId) {
                continue;
            }
            
            $existingMatchTime = new DateTime($match['match_time']);
            $timeDiff = abs($matchDateTime->getTimestamp() - $existingMatchTime->getTimestamp());
            
            // If matches are less than 4 hours apart, consider it a conflict
            if ($timeDiff < 4 * 3600) {
                return false;
            }
        }
        
        // Check away team matches
        foreach ($awayTeamMatches as $match) {
            if ($match['id'] == $excludeMatchId) {
                continue;
            }
            
            $existingMatchTime = new DateTime($match['match_time']);
            $timeDiff = abs($matchDateTime->getTimestamp() - $existingMatchTime->getTimestamp());
            
            // If matches are less than 4 hours apart, consider it a conflict
            if ($timeDiff < 4 * 3600) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Validate match data before saving
     * 
     * @param array $data Match data to validate
     * @param bool $isCreating Whether this is for creating a new match
     * @throws InvalidArgumentException If validation fails
     */
    protected function validateData($data, $isCreating = true) {
        $requiredFields = ['home_team_id', 'away_team_id', 'venue_id', 'match_time'];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new InvalidArgumentException("$field is required and cannot be empty");
            }
        }
        
        // Validate team IDs
        if (!is_numeric($data['home_team_id']) || $data['home_team_id'] <= 0) {
            throw new InvalidArgumentException("Invalid home team ID provided");
        }
        
        if (!is_numeric($data['away_team_id']) || $data['away_team_id'] <= 0) {
            throw new InvalidArgumentException("Invalid away team ID provided");
        }
        
        // Check that home and away teams are different
        if ($data['home_team_id'] == $data['away_team_id']) {
            throw new InvalidArgumentException("Home and away teams must be different");
        }
        
        // Check if teams exist
        $homeTeam = $this->teamDao->getById($data['home_team_id']);
        if (!$homeTeam) {
            throw new Exception("Team with ID {$data['home_team_id']} not found");
        }
        
        $awayTeam = $this->teamDao->getById($data['away_team_id']);
        if (!$awayTeam) {
            throw new Exception("Team with ID {$data['away_team_id']} not found");
        }
        
        // Validate venue ID
        if (!is_numeric($data['venue_id']) || $data['venue_id'] <= 0) {
            throw new InvalidArgumentException("Invalid venue ID provided");
        }
        
        // Check if venue exists
        $venue = $this->venueDao->getById($data['venue_id']);
        if (!$venue) {
            throw new Exception("Venue with ID {$data['venue_id']} not found");
        }
        
        // Validate match_time
        $matchTime = new DateTime($data['match_time']);
        $now = new DateTime();
        if ($isCreating && $matchTime < $now) {
            throw new InvalidArgumentException("Match time cannot be in the past");
        }
        
        // Validate status if provided
        if (isset($data['status']) && !empty($data['status'])) {
            $validStatuses = ['scheduled', 'in_progress', 'completed', 'cancelled'];
            if (!in_array($data['status'], $validStatuses)) {
                throw new InvalidArgumentException("Status must be one of: " . implode(', ', $validStatuses));
            }
        }
        
        // Validate schedule (no team conflicts)
        $matchId = isset($data['id']) ? $data['id'] : null;
        if (!$this->isScheduleValid($data['home_team_id'], $data['away_team_id'], $data['match_time'], $matchId)) {
            throw new InvalidArgumentException("Match scheduling conflict detected. Teams already have matches scheduled around this time.");
        }
    }
}