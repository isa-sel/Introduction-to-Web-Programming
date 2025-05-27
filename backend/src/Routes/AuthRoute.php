<?php
namespace Ibu\Web\Routes;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthRoute extends BaseRoute {
    protected function registerRoutes() {
        // Register
        $this->app->route('POST /api/register', function() {
            $data = $this->getJsonBody();

            // Basic validation
            $this->validateRequired($data, ['username', 'email', 'password', 'role']);
            if (!in_array($data['role'], ['admin', 'user'])) {
                $this->error('Invalid role', 400);
            }


            $userService = $this->serviceManager->get('user');
            $data['password'] = $hashed;
            $userId = $userService->register($data);

            $user = $userService->getById($userId);
            unset($user['password']); // Nikad ne šalji password nazad

            $this->success($user, 201);
        });

        // Login
        $this->app->route('POST /api/login', function() {
            $data = $this->getJsonBody();
            $this->validateRequired($data, ['email', 'password']);

            $userService = $this->serviceManager->get('user');
            $user = $userService->getByEmail($data['email']);

            if (!$user || !password_verify($data['password'], $user['password'])) {
                $this->error('Invalid email or password', 401);
            }

            // JWT payload
            $payload = [
                "sub" => $user['id'],
                "username" => $user['username'],
                "role" => $user['role'],
                "exp" => time() + 60 * 60 * 24 // 24 sata
            ];
            $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

            unset($user['password']);
            $this->success([
                "user" => $user,
                "token" => $jwt
            ]);
        });
    }
}