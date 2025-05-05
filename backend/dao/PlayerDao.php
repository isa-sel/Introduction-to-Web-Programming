<?php
require_once 'BaseDao.php';

class PlayerDao extends BaseDao {
   public function __construct() {
       parent::__construct("players");
   }

   public function getByTeam($team_id) {
       $stmt = $this->connection->prepare("SELECT * FROM players WHERE team_id = :team_id");
       $stmt->bindParam(':team_id', $team_id);
       $stmt->execute();
       return $stmt->fetchAll();
   }

   public function getByPosition($position) {
       $stmt = $this->connection->prepare("SELECT * FROM players WHERE position = :position");
       $stmt->bindParam(':position', $position);
       $stmt->execute();
       return $stmt->fetchAll();
   }

   public function getByNationality($nationality) {
       $stmt = $this->connection->prepare("SELECT * FROM players WHERE nationality = :nationality");
       $stmt->bindParam(':nationality', $nationality);
       $stmt->execute();
       return $stmt->fetchAll();
   }
}
?>