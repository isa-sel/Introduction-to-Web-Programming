<?php
namespace Ibu\Web\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Flight;

class AuthMiddleware {
    
    /**
     * Verify JWT token and set user info in Flight
     */
    public static function authenticate() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';
        
        if (empty($authHeader)) {
            Flight::json(['status' => 'error', 'message' => 'No authorization header'], 401);
            return false;
        }
        
        // Extract token from "Bearer TOKEN"
        $token = str_replace('Bearer ', '', $authHeader);
        
        try {
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
            
            // Store user info in Flight for use in routes
            Flight::set('user', [
                'id' => $decoded->sub,
                'username' => $decoded->username,
                'role' => $decoded->role
            ]);
            
            return true;
        } catch (\Exception $e) {
            Flight::json(['status' => 'error', 'message' => 'Invalid token'], 401);
            return false;
        }
    }
    
    /**
     * Check if user has required role
     */
    public static function requireRole($role) {
        return function() use ($role) {
            if (!self::authenticate()) {
                return false;
            }
            
            $user = Flight::get('user');
            if ($user['role'] !== $role) {
                Flight::json(['status' => 'error', 'message' => 'Insufficient permissions'], 403);
                return false;
            }
            
            return true;
        };
    }
    
    /**
     * Check if user is admin
     */
    public static function requireAdmin() {
        return self::requireRole('admin');
    }
    
    /**
     * Check if user is authenticated (any role)
     */
    public static function requireAuth() {
        return function() {
            return self::authenticate();
        };
    }
}