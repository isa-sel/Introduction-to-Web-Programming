<?php
namespace Ibu\Web\Dao;

class UserDao extends BaseDao {
   public function __construct() {
       parent::__construct("users");
   }

   public function getByUsername($username) {
       $stmt = $this->connection->prepare("SELECT * FROM users WHERE username = :username");
       $stmt->bindParam(':username', $username);
       $stmt->execute();
       return $stmt->fetch();
   }

   public function getByEmail($email) {
       $stmt = $this->connection->prepare("SELECT * FROM users WHERE email = :email");
       $stmt->bindParam(':email', $email);
       $stmt->execute();
       return $stmt->fetch();
   }

   public function getByRole($role) {
       $stmt = $this->connection->prepare("SELECT * FROM users WHERE role = :role");
       $stmt->bindParam(':role', $role);
       $stmt->execute();
       return $stmt->fetchAll();
   }

   public function updateLastLogin($id) {
       $stmt = $this->connection->prepare("
           UPDATE users
           SET last_login = CURRENT_TIMESTAMP
           WHERE id = :id
       ");
       
       $stmt->bindParam(':id', $id);
       return $stmt->execute();
   }
}
?>