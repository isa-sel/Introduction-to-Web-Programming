<?php
namespace Ibu\Web\Routes;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthRoute extends BaseRoute {
    
    public function __construct($app, $serviceManager) {
        error_log("AuthRoute constructor called!"); // DEBUG
        parent::__construct($app, $serviceManager);
        error_log("AuthRoute constructor finished!"); // DEBUG
    }
    
    protected function registerRoutes() {
        error_log("AuthRoute registerRoutes() called!"); // DEBUG
        
        // Register
        $this->app->route('POST /api/register', function() {
            error_log("Register route hit!"); // DEBUG
            
            try {
                $data = $this->getJsonBody();
                error_log("Register data: " . json_encode($data)); // DEBUG

                // Basic validation
                $this->validateRequired($data, ['username', 'email', 'password', 'role']);
                if (!in_array($data['role'], ['admin', 'user'])) {
                    $this->error('Invalid role', 400);
                    return;
                }

                $userService = $this->serviceManager->get('user');
                $hashed = password_hash($data['password'], PASSWORD_DEFAULT);
                $data['password'] = $hashed;
                
                // This will throw an exception if user exists
                $userId = $userService->register($data);

                $user = $userService->getById($userId);
                unset($user['password']); // Never send password back

                // Generate JWT token
                $payload = [
                    "sub" => $user['id'],
                    "username" => $user['username'],
                    "role" => $user['role'],
                    "exp" => time() + 60 * 60 * 24 // 24 hours
                ];
                
                $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
                
                $response = [
                    "user" => $user,
                    "token" => $jwt
                ];

                $this->success($response, 201);
                error_log("Register completed successfully!"); // DEBUG
                
            } catch (\Exception $e) {
                error_log("Register error: " . $e->getMessage()); // DEBUG
                $this->error($e->getMessage(), 400);
                return;
            }
        });

        // Login
        $this->app->route('POST /api/login', function() {
            error_log("Login route hit!"); // DEBUG
            
            try {
                $data = $this->getJsonBody();
                error_log("Login data: " . json_encode($data)); // DEBUG
                
                $this->validateRequired($data, ['email', 'password']);
                error_log("Validation passed"); // DEBUG

                $userService = $this->serviceManager->get('user');
                error_log("Got user service"); // DEBUG
                
                $user = $userService->getByEmail($data['email']);
                error_log("User found: " . ($user ? "YES" : "NO")); // DEBUG

                if (!$user) {
                    error_log("User not found"); // DEBUG
                    $this->error('Invalid email or password', 401);
                    return;
                }

                if (!password_verify($data['password'], $user['password'])) {
                    error_log("Password verification failed"); // DEBUG
                    $this->error('Invalid email or password', 401);
                    return;
                }

                error_log("Password verified, creating JWT"); // DEBUG
                
                // JWT payload
                $payload = [
                    "sub" => $user['id'],
                    "username" => $user['username'],
                    "role" => $user['role'],
                    "exp" => time() + 60 * 60 * 24 // 24 hours
                ];
                
                $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
                error_log("JWT created successfully"); // DEBUG

                unset($user['password']);
                
                $response = [
                    "user" => $user,
                    "token" => $jwt
                ];
                
                error_log("About to send response: " . json_encode($response)); // DEBUG
                
                $this->success($response);
                
                error_log("Login response sent successfully!"); // DEBUG
                
            } catch (\Exception $e) {
                error_log("Login error: " . $e->getMessage()); // DEBUG
                $this->error('Login failed: ' . $e->getMessage(), 500);
            }
        });
        
        error_log("AuthRoute routes registered successfully!"); // DEBUG
    }
}