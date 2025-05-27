<?php
namespace Ibu\Web\Routes;

class UserRoute extends BaseRoute
{
    protected function registerRoutes()
    {
   
        // LOGIN ruta
        $this->app->route('POST /api/login', function() {
            try {
                file_put_contents(__DIR__.'/login.log', "USAO U LOGIN\n", FILE_APPEND);
        
                $data = $this->getJsonBody();
                file_put_contents(__DIR__.'/login.log', "DATA: ".print_r($data, true)."\n", FILE_APPEND);
        
                $userService = $this->serviceManager->get('user');
                file_put_contents(__DIR__.'/login.log', "UZEO USERSERVICE\n", FILE_APPEND);
        
                $user = $userService->getByEmail($data['email']);
                file_put_contents(__DIR__.'/login.log', "USER: ".print_r($user, true)."\n", FILE_APPEND);
        
                if (!$user) {
                    $this->error("User not found", 401);
                    return;
                }
                if (!isset($user['password'])) {
                    $this->error("Password not found in user", 500);
                    return;
                }
                if (!password_verify($data['password'], $user['password'])) {
                    $this->error("Invalid credentials", 401);
                    return;
                }
        
                $this->success(['login' => 'OK']);
            } catch (\Throwable $e) {
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode([
                    "error" => true,
                    "message" => $e->getMessage(),
                    "trace" => $e->getTraceAsString()
                ]);
                file_put_contents(__DIR__.'/login.log', "ERROR: ".$e->getMessage()."\n", FILE_APPEND);
            }
        });
        
        // REGISTER ruta
        $this->app->route('POST /api/register', function() {
            $data = $this->getJsonBody();

            // Minimalna validacija
            if (empty($data['email']) || empty($data['password']) || empty($data['role'])) {
                $this->error("Email, password and role are required", 400);
                return;
            }

            $userService = $this->serviceManager->get('user');
            // Hash password-a
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

            // Kreiraj user-a
            try {
                $userId = $userService->register($data);
                $user = $userService->getById($userId);

                $this->success([
                    'user' => [
                        'id' => $user['id'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ]
                ], 201);
            } catch (\Exception $e) {
                $this->error($e->getMessage(), 400);
            }
        });
    }
}
