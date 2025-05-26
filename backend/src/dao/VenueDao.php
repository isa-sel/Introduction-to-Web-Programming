<?php
namespace Ibu\Web\Dao;

class VenueDao extends BaseDao {
   public function __construct() {
       parent::__construct("venues");
   }

   public function getByLocation($location) {
       $stmt = $this->connection->prepare("SELECT * FROM venues WHERE location = :location");
       $stmt->bindParam(':location', $location);
       $stmt->execute();
       return $stmt->fetchAll();
   }

   public function getByMinCapacity($capacity) {
       $stmt = $this->connection->prepare("SELECT * FROM venues WHERE capacity >= :capacity");
       $stmt->bindParam(':capacity', $capacity);
       $stmt->execute();
       return $stmt->fetchAll();
   }
}
?>