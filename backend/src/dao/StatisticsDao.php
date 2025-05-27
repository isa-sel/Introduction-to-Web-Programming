<?php
namespace Ibu\Web\Dao;

use PDO;

class StatisticsDao extends BaseDao {
   public function __construct() {
       parent::__construct("statistics");
   }

   public function getByMatch($match_id) {
       $stmt = $this->connection->prepare("
           SELECT s.*, p.first_name, p.last_name
           FROM statistics s
           JOIN players p ON s.player_id = p.id
           WHERE s.match_id = :match_id
       ");
       $stmt->bindParam(':match_id', $match_id);
       $stmt->execute();
       return $stmt->fetchAll();
   }

   public function getByPlayer($player_id) {
       $stmt = $this->connection->prepare("
           SELECT s.*, m.match_time, m.home_team_id, m.away_team_id, 
                  m.home_score, m.away_score
           FROM statistics s
           JOIN matches m ON s.match_id = m.id
           WHERE s.player_id = :player_id
           ORDER BY m.match_time DESC
       ");
       $stmt->bindParam(':player_id', $player_id);
       $stmt->execute();
       return $stmt->fetchAll();
   }

   public function getTopScorers($limit = 10) {
       $stmt = $this->connection->prepare("
           SELECT p.id, p.first_name, p.last_name, p.team_id, t.name as team_name,
                  SUM(s.goals) as total_goals
           FROM statistics s
           JOIN players p ON s.player_id = p.id
           JOIN teams t ON p.team_id = t.id
           GROUP BY p.id
           ORDER BY total_goals DESC
           LIMIT :limit
       ");
       $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
       $stmt->execute();
       return $stmt->fetchAll();
   }
}
?>