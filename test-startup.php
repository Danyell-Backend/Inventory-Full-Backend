<?php
/**
 * Quick test to see where Laravel is hanging during startup
 */

echo "Starting Laravel bootstrap test...\n";
$startTime = microtime(true);

try {
    echo "[1] Loading autoloader...\n";
    require __DIR__.'/vendor/autoload.php';
    echo "    ✓ Autoloader loaded (" . round((microtime(true) - $startTime) * 1000) . "ms)\n";
    
    $stepTime = microtime(true);
    echo "[2] Creating application instance...\n";
    $app = require_once __DIR__.'/bootstrap/app.php';
    echo "    ✓ Application created (" . round((microtime(true) - $stepTime) * 1000) . "ms)\n";
    
    $stepTime = microtime(true);
    echo "[3] Testing database connection...\n";
    try {
        $db = $app->make(Illuminate\Database\DatabaseManager::class);
        $connection = $db->connection();
        $pdo = $connection->getPdo();
        echo "    ✓ Database connected (" . round((microtime(true) - $stepTime) * 1000) . "ms)\n";
    } catch (\Exception $e) {
        echo "    ✗ Database connection failed: " . $e->getMessage() . "\n";
        echo "    This might be causing the slow startup!\n";
        echo "    Check your .env file for DB_CONNECTION, DB_HOST, DB_DATABASE settings\n";
    }
    
    $stepTime = microtime(true);
    echo "[4] Testing route loading...\n";
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "    ✓ Kernel created (" . round((microtime(true) - $stepTime) * 1000) . "ms)\n";
    
    $totalTime = round((microtime(true) - $startTime) * 1000);
    echo "\n✓ Bootstrap completed in {$totalTime}ms\n";
    echo "If this takes more than 5 seconds, there's a problem.\n";
    
} catch (\Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

