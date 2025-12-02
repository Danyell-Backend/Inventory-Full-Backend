<?php
/**
 * Test database connection directly
 * This will show if database is causing the slow startup
 */

echo "Testing database connection...\n\n";

// Load environment variables
$envFile = __DIR__ . '/.env';
if (!file_exists($envFile)) {
    echo "ERROR: .env file not found!\n";
    exit(1);
}

$env = parse_ini_file($envFile);
$connection = $env['DB_CONNECTION'] ?? 'mysql';
$host = $env['DB_HOST'] ?? '127.0.0.1';
$port = $env['DB_PORT'] ?? '3306';
$database = $env['DB_DATABASE'] ?? '';
$username = $env['DB_USERNAME'] ?? 'root';
$password = $env['DB_PASSWORD'] ?? '';

echo "Connection settings:\n";
echo "  Type: {$connection}\n";
echo "  Host: {$host}\n";
echo "  Port: {$port}\n";
echo "  Database: {$database}\n";
echo "  Username: {$username}\n";
echo "\n";

if ($connection === 'mysql') {
    echo "Attempting MySQL connection...\n";
    $startTime = microtime(true);
    
    try {
        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 5, // 5 second timeout
        ];
        
        $pdo = new PDO($dsn, $username, $password, $options);
        $elapsed = round((microtime(true) - $startTime) * 1000);
        
        echo "✓ Connected successfully in {$elapsed}ms\n";
        echo "✓ Database connection is working\n";
        
    } catch (PDOException $e) {
        $elapsed = round((microtime(true) - $startTime) * 1000);
        echo "✗ Connection FAILED after {$elapsed}ms\n";
        echo "  Error: " . $e->getMessage() . "\n";
        echo "\n";
        echo "This is likely causing php artisan serve to hang!\n";
        echo "\n";
        echo "Solutions:\n";
        echo "1. Make sure MySQL service is running\n";
        echo "2. Check your .env file credentials\n";
        echo "3. Try: php artisan migrate:status\n";
        exit(1);
    }
} elseif ($connection === 'sqlite') {
    $dbPath = $env['DB_DATABASE'] ?? __DIR__ . '/database/database.sqlite';
    echo "Checking SQLite database...\n";
    
    if (!file_exists($dbPath)) {
        echo "✗ SQLite database file not found: {$dbPath}\n";
        echo "  Create it with: New-Item -ItemType File -Path \"{$dbPath}\" -Force\n";
        exit(1);
    }
    
    echo "✓ SQLite database file exists\n";
} else {
    echo "Unknown connection type: {$connection}\n";
    exit(1);
}

echo "\n✓ Database connection test passed!\n";
echo "If php artisan serve still hangs, the issue is elsewhere.\n";

