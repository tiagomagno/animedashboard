<?php
try {
    // Attempt standard XAMPP/WAMP credentials (root, empty)
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Dropping database animedashboard...\n";
    // Using IF EXISTS to avoid error if it's already gone/broken
    $pdo->exec("DROP DATABASE IF EXISTS animedashboard");
    
    echo "Creating database animedashboard...\n";
    $pdo->exec("CREATE DATABASE animedashboard");
    
    echo "Database reset successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "If access denied, you might need a password or different user.\n";
    exit(1);
}
