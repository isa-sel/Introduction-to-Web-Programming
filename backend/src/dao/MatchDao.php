<?php
namespace Ibu\Web\Dao;

use PDO;

class MatchDao extends BaseDao {
   public function __construct() {
       parent::__construct("matches");
   }

   public function getByTeam($team_id) {
       $stmt = $this->connection->prepare("
           SELECT * FROM matches 
           WHERE home_team_id = :team_id OR away_team_id = :team_id
           ORDER BY match_time
       ");
       $stmt->bindParam(':team_id', $team_id);
       $stmt->execute();
       return $stmt->fetchAll();
   }

   public function getByVenue($venue_id) {
       $stmt = $this->connection->prepare("
           SELECT * FROM matches 
           WHERE venue_id = :venue_id
           ORDER BY match_time
       ");
       $stmt->bindParam(':venue_id', $venue_id);
       $stmt->execute();
       return $stmt->fetchAll();
   }

   public function getByStatus($status) {
       $stmt = $this->connection->prepare("
           SELECT * FROM matches 
           WHERE status = :status
           ORDER BY match_time
       ");
       $stmt->bindParam(':status', $status);
       $stmt->execute();
       return $stmt->fetchAll();
   }

   public function getUpcoming($limit = 10) {
       $stmt = $this->connection->prepare("
           SELECT * FROM matches 
           WHERE match_time > NOW() AND status = 'scheduled'
           ORDER BY match_time
           LIMIT :limit
       ");
       $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
       $stmt->execute();
       return $stmt->fetchAll();
   }

   public function updateResult($id, $home_score, $away_score, $status = 'completed') {
       $stmt = $this->connection->prepare("
           UPDATE matches
           SET home_score = :home_score,
               away_score = :away_score,
               status = :status
           WHERE id = :id
       ");
       
       $stmt->bindParam(':id', $id);
       $stmt->bindParam(':home_score', $home_score);
       $stmt->bindParam(':away_score', $away_score);
       $stmt->bindParam(':status', $status);
       
       return $stmt->execute();
   }
}
?>