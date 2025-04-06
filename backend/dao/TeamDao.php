<?php
require_once 'BaseDao.php';

class TeamDao extends BaseDao {
   public function __construct() {
       parent::__construct("teams");
   }
   

   public function getByCategory($category) {
       $stmt = $this->connection->prepare("SELECT * FROM teams WHERE category = :category");
       $stmt->bindParam(':category', $category);
       $stmt->execute();
       return $stmt->fetchAll();
   }

   public function getByLocation($location) {
       $stmt = $this->connection->prepare("SELECT * FROM teams WHERE location = :location");
       $stmt->bindParam(':location', $location);
       $stmt->execute();
       return $stmt->fetchAll();
   }
}
?>