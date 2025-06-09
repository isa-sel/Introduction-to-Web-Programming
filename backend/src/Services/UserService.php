<?php
namespace Ibu\Web\Services;

use Ibu\Web\Dao\UserDao;
class UserService {
    private $dao;

    public function __construct() {
        $this->dao = new UserDao();
    }

    public function getByUsername($username) {
        return $this->dao->getByUsername($username);
    }

    public function getByEmail($email) {
        return $this->dao->getByEmail($email);
    }

    // REGISTRACIJA KORISNIKA
    public function register($data) {
        // Provjera duplikata (email ili username)
        if ($this->dao->getByEmail($data['email'])) {
            throw new \Exception("Korisnik sa ovim emailom već postoji.");
        }
        if ($this->dao->getByUsername($data['username'])) {
            throw new \Exception("Korisnik sa ovim username-om već postoji.");
        }
        
        // DON'T hash password here - it's already hashed in AuthRoute
        // REMOVED: $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Dodaj korisnika (poziva create iz BaseDao)
        return $this->dao->insert($data);
    }

    // LOGIN
    public function login($email_or_username, $password) {
        // Pokušaj prvo po emailu, pa po username-u
        $user = $this->dao->getByEmail($email_or_username);
        if (!$user) {
            $user = $this->dao->getByUsername($email_or_username);
        }
        if (!$user) {
            throw new \Exception("Korisnik nije pronađen.");
        }
        if (!password_verify($password, $user['password'])) {
            throw new \Exception("Pogrešna lozinka.");
        }
        // Update last_login (nije obavezno, ali je zgodno)
        $this->dao->updateLastLogin($user['id']);
        // Vraćaš korisnika (ili generišeš JWT u ruti/kontroleru)
        return $user;
    }

    // CRUD – koristiš nasljeđeno iz BaseService ako ga imaš, ili proslijedi UserDao
    public function getAll() {
        return $this->dao->getAll();
    }
    public function getById($id) {
        return $this->dao->getById($id);
    }
    public function update($id, $data) {
        // Možeš ovdje onemogućiti promjenu emaila/username-a ako želiš
        return $this->dao->update($id, $data);
    }
    public function delete($id) {
        return $this->dao->delete($id);
    }

    // Dohvati sve korisnike po roli
    public function getByRole($role) {
        return $this->dao->getByRole($role);
    }
}