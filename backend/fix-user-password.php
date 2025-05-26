<?php
require_once __DIR__ . '/vendor/autoload.php';

try {
    // Load .env
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    // Connect to database
    $db = new PDO(
        "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}", 
        $_ENV['DB_USER'], 
        $_ENV['DB_PASS']
    );
    
    // Get the user
    $stmt = $db->prepare("SELECT id, username, email, password, role FROM users WHERE email = ?");
    $stmt->execute(['testuser@example.com']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "User found in database:\n";
        echo "ID: " . $user['id'] . "\n";
        echo "Username: " . $user['username'] . "\n";
        echo "Email: " . $user['email'] . "\n";
        echo "Role: " . $user['role'] . "\n";
        echo "Password hash: " . $user['password'] . "\n\n";
        
        // Test password
        $testPassword = 'TestPassword123';
        if (password_verify($testPassword, $user['password'])) {
            echo "✓ Password 'TestPassword123' is VALID\n";
        } else {
            echo "✗ Password 'TestPassword123' is INVALID\n";
            
            // Generate correct hash
            $correctHash = password_hash($testPassword, PASSWORD_DEFAULT);
            echo "\nTo fix, run this SQL:\n";
            echo "UPDATE users SET password = '$correctHash' WHERE id = {$user['id']};\n";
        }
    } else {
        echo "User not found!\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}