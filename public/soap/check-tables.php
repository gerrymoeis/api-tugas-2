<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Baca konfigurasi dari file .env
$envFile = __DIR__ . '/../../.env';
$env = [];

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0 || empty(trim($line))) {
            continue;
        }
        
        // Pisahkan key dan value, dan bersihkan komentar jika ada
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Hapus komentar pada value jika ada
            if (strpos($value, '#') !== false) {
                $value = trim(explode('#', $value)[0]);
            }
            
            $env[$key] = $value;
        }
    }
}

// Gunakan konfigurasi dari .env atau default
$dbHost = $env['DB_HOST'] ?? '127.0.0.1';
$dbPort = $env['DB_PORT'] ?? '3306';
$dbName = $env['DB_DATABASE'] ?? 'contact_api';
$dbUser = $env['DB_USERNAME'] ?? 'root';
$dbPass = $env['DB_PASSWORD'] ?? '';

echo "Checking database tables...<br><br>";

try {
    // Buat koneksi PDO ke MySQL
    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // List all tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Tables in database:<br>";
    echo "<pre>";
    print_r($tables);
    echo "</pre><br>";
    
    // Check if contacts table exists
    if (in_array('contacts', $tables)) {
        echo "Table 'contacts' exists.<br><br>";
        
        // Get table structure
        $stmt = $pdo->query("DESCRIBE contacts");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Contacts table structure:<br>";
        echo "<pre>";
        foreach ($columns as $column) {
            echo $column['Field'] . " - " . $column['Type'] . " - " . ($column['Null'] === 'NO' ? 'NOT NULL' : 'NULL') . "<br>";
        }
        echo "</pre><br>";
    } else {
        echo "Table 'contacts' does not exist!<br><br>";
    }
    
    // Check if addresses table exists
    if (in_array('addresses', $tables)) {
        echo "Table 'addresses' exists.<br><br>";
        
        // Get table structure
        $stmt = $pdo->query("DESCRIBE addresses");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Addresses table structure:<br>";
        echo "<pre>";
        foreach ($columns as $column) {
            echo $column['Field'] . " - " . $column['Type'] . " - " . ($column['Null'] === 'NO' ? 'NOT NULL' : 'NULL') . "<br>";
        }
        echo "</pre><br>";
    } else {
        echo "Table 'addresses' does not exist!<br><br>";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "<br>";
}
