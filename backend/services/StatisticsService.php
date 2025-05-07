<?php
require_once 'BaseService.php';
require_once __DIR__ . '/../dao/StatisticsDao.php';
require_once __DIR__ . '/../dao/MatchDao.php';
require_once __DIR__ . '/../dao/PlayerDao.php';

/**
 * Service for handling Statistics operations
 */
class StatisticsService extends BaseService {
    private $matchDao;
    private $playerDao;
    
    public function __construct() {
        $this->dao = new StatisticsDao();
        $this->matchDao = new MatchDao();
        $this->playerDao = new PlayerDao();
    }

    /**
     * Get statistics by match
     * 
     * @param int $matchId Match ID to filter by
     * @return array Statistics for the specified match
     */
    public function getByMatch($matchId) {
        if (!is_numeric($matchId) || $matchId <= 0) {
            throw new InvalidArgumentException("Invalid match ID provided");
        }
        
        // Check if match exists
        $match = $this->matchDao->getById($matchId);
        if (!$match) {
            throw new Exception("Match with ID $matchId not found");
        }
        
        return $this->dao->getByMatch($matchId);
    }

    /**
     * Get statistics by player
     * 
     * @param int $playerId Player ID to filter by
     * @return array Statistics for the specified player
     */
    public function getByPlayer($playerId) {
        if (!is_numeric($playerId) || $playerId <= 0) {
            throw new InvalidArgumentException("Invalid player ID provided");
        }
        
        // Check if player exists
        $player = $this->playerDao->getById($playerId);
        if (!$player) {
            throw new Exception("Player with ID $playerId not found");
        }
        
        return $this->dao->getByPlayer($playerId);
    }

    /**
     * Get top scorers
     * 
     * @param int $limit Maximum number of players to return
     * @return array Top scorers with their statistics
     */
    public function getTopScorers($limit = 10) {
        if (!is_numeric($limit) || $limit <= 0) {
            throw new InvalidArgumentException("Limit must be a positive number");
        }
        
        return $this->dao->getTopScorers($limit);
    }

    /**
     * Calculate player efficiency rating
     * Based on goals, assists, penalties, etc.
     * 
     * @param int $playerId Player ID
     * @return float Efficiency rating
     */
    public function calculatePlayerEfficiency($playerId) {
        if (!is_numeric($playerId) || $playerId <= 0) {
            throw new InvalidArgumentException("Invalid player ID provided");
        }
        
        // Check if player exists
        $player = $this->playerDao->getById($playerId);
        if (!$player) {
            throw new Exception("Player with ID $playerId not found");
        }
        
        $statistics = $this->dao->getByPlayer($playerId);
        
        $totalGoals = 0;
        $totalAssists = 0;
        $totalPenalties = 0;
        $totalYellowCards = 0;
        $totalRedCards = 0;
        $totalSaves = 0;
        $totalPlayingTime = 0;
        
        foreach ($statistics as $stat) {
            $totalGoals += $stat['goals'];
            $totalAssists += $stat['assists'];
            $totalPenalties += $stat['penalties'];
            $totalYellowCards += $stat['yellow_cards'];
            $totalRedCards += $stat['red_cards'];
            $totalSaves += $stat['saves'];
            $totalPlayingTime += $stat['playing_time'];
        }
        
        // Avoid division by zero
        if ($totalPlayingTime == 0) {
            return 0;
        }
        
        // Calculate efficiency
        // Goals and assists are positive, penalties and cards are negative
        $efficiency = ($totalGoals * 1.0 + $totalAssists * 0.5 + $totalSaves * 0.3) / $totalPlayingTime;
        $efficiency -= ($totalPenalties * 0.3 + $totalYellowCards * 0.2 + $totalRedCards * 0.5) / $totalPlayingTime;
        
        return round($efficiency, 2);
    }

    /**
     * Validate statistics data before saving
     * 
     * @param array $data Statistics data to validate
     * @param bool $isCreating Whether this is for creating new statistics
     * @throws InvalidArgumentException If validation fails
     */
    protected function validateData($data, $isCreating = true) {
        $requiredFields = ['match_id', 'player_id'];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new InvalidArgumentException("$field is required and cannot be empty");
            }
        }
        
        // Validate match_id
        if (!is_numeric($data['match_id']) || $data['match_id'] <= 0) {
            throw new InvalidArgumentException("Invalid match ID provided");
        }
        
        // Check if match exists
        $match = $this->matchDao->getById($data['match_id']);
        if (!$match) {
            throw new Exception("Match with ID {$data['match_id']} not found");
        }
        
        // Validate player_id
        if (!is_numeric($data['player_id']) || $data['player_id'] <= 0) {
            throw new InvalidArgumentException("Invalid player ID provided");
        }
        
        // Check if player exists
        $player = $this->playerDao->getById($data['player_id']);
        if (!$player) {
            throw new Exception("Player with ID {$data['player_id']} not found");
        }
        
        // Validate numeric fields
        $numericFields = ['goals', 'assists', 'penalties', 'yellow_cards', 'red_cards', 'saves', 'playing_time'];
        foreach ($numericFields as $field) {
            if (isset($data[$field])) {
                if (!is_numeric($data[$field]) || $data[$field] < 0) {
                    throw new InvalidArgumentException("$field must be a non-negative number");
                }
            }
        }
        
        // Validate red cards (maximum 1 per player per match)
        if (isset($data['red_cards']) && $data['red_cards'] > 1) {
            throw new InvalidArgumentException("A player cannot receive more than 1 red card in a match");
        }
        
        // Validate playing time (maximum 60 minutes in handball)
        if (isset($data['playing_time']) && $data['playing_time'] > 60) {
            throw new InvalidArgumentException("Playing time cannot exceed 60 minutes");
        }
    }
}