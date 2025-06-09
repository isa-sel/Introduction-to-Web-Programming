<?php
require_once __DIR__ . '/vendor/autoload.php';

// Load .env
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    echo "✓ .env loaded successfully\n";
} catch (Exception $e) {
    echo "✗ Error loading .env: " . $e->getMessage() . "\n";
}

// Test user looooookup
try {
    $userService = new \Ibu\Web\Services\UserService();
    echo "✓ UserService created\n";
    
    $email = 'testuser@example.com';
    $password = 'TestPassword123';
    
    echo "\nTesting login for: $email\n";
    
    // Try to get user by email
    $user = $userService->getByEmail($email);
    
    if ($user) {
        echo "✓ User found!\n";
        echo "  - ID: " . $user['id'] . "\n";
        echo "  - Username: " . $user['username'] . "\n";
        echo "  - Email: " . $user['email'] . "\n";
        echo "  - Role: " . $user['role'] . "\n";
        
        // Test password
        if (password_verify($password, $user['password'])) {
            echo "✓ Password is correct!\n";
            
            // Generate JWT token
            $payload = [
                "sub" => $user['id'],
                "username" => $user['username'],
                "role" => $user['role'],
                "exp" => time() + 60 * 60 * 24 // 24 hours
            ];
            
            $jwt = \Firebase\JWT\JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
            echo "\nGenerated token:\n" . $jwt . "\n";
        } else {
            echo "✗ Password is incorrect!\n";
        }
    } else {
        echo "✗ User not found!\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}