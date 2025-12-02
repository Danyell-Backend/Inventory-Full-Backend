<?php
/**
 * Script to find and fix the corrupted file causing the error
 * Error: file_get_contents(): Read of 384855 bytes failed
 */

echo "Searching for corrupted files...\n\n";

$basePath = __DIR__;
$targetSize = 384855;
$sizeRange = [380000, 390000]; // ±5KB range

function findFilesBySize($dir, $minSize, $maxSize) {
    $files = [];
    if (!is_dir($dir)) return $files;
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $size = $file->getSize();
            if ($size >= $minSize && $size <= $maxSize) {
                $files[] = [
                    'path' => $file->getPathname(),
                    'size' => $size,
                    'readable' => is_readable($file->getPathname())
                ];
            }
        }
    }
    
    return $files;
}

// Search in common Laravel directories
$searchDirs = [
    $basePath . '/storage',
    $basePath . '/bootstrap/cache',
    $basePath . '/vendor',
];

echo "Searching for files between {$sizeRange[0]} and {$sizeRange[1]} bytes...\n\n";

$foundFiles = [];
foreach ($searchDirs as $dir) {
    if (is_dir($dir)) {
        echo "Checking: $dir\n";
        $files = findFilesBySize($dir, $sizeRange[0], $sizeRange[1]);
        $foundFiles = array_merge($foundFiles, $files);
    }
}

if (empty($foundFiles)) {
    echo "No files found in that size range.\n";
    echo "The file might be locked by OneDrive or another process.\n\n";
    echo "Trying to clear all possible cache locations...\n";
    
    // Clear all cache directories
    $cacheDirs = [
        $basePath . '/bootstrap/cache',
        $basePath . '/storage/framework/cache',
        $basePath . '/storage/framework/sessions',
        $basePath . '/storage/framework/views',
    ];
    
    foreach ($cacheDirs as $cacheDir) {
        if (is_dir($cacheDir)) {
            $files = glob($cacheDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                    echo "Deleted: $file\n";
                }
            }
        }
    }
    
    echo "\nCache cleared. Try running: php artisan serve\n";
} else {
    echo "Found " . count($foundFiles) . " file(s):\n\n";
    foreach ($foundFiles as $file) {
        echo "File: {$file['path']}\n";
        echo "Size: {$file['size']} bytes\n";
        echo "Readable: " . ($file['readable'] ? 'Yes' : 'No') . "\n";
        
        // Try to read the file
        if ($file['readable']) {
            echo "Attempting to read file...\n";
            $content = @file_get_contents($file['path']);
            if ($content === false) {
                echo "✗ Cannot read file - likely corrupted!\n";
                echo "Attempting to delete...\n";
                if (@unlink($file['path'])) {
                    echo "✓ File deleted successfully\n";
                } else {
                    echo "✗ Cannot delete file - may be locked by OneDrive or another process\n";
                    echo "Solution: Close OneDrive sync or restart your computer\n";
                }
            } else {
                echo "✓ File is readable\n";
            }
        }
        echo "\n";
    }
}

echo "Done!\n";

