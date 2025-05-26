<?php
namespace Ibu\Web\Config;

use PDO;
use PDOException;

class Database {
    private static $connection = null;

    private static $host = 'localhost';
    private static $dbname = 'handball_league_management'; // PROMIJENI na ime svoje baze
    private static $username = 'root';
    private static $password = '';
    
    public static function connect() {
        if (self::$connection === null) {
            try {
                self::$connection = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$dbname . ";charset=utf8",
                    self::$username,
                    self::$password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return self::$connection;
    }
}