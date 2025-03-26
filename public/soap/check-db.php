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

echo "Checking MySQL connection...<br>";
echo "Host: $dbHost<br>";
echo "Port: $dbPort<br>";
echo "Database: $dbName<br>";
echo "Username: $dbUser<br>";
echo "Password: " . (empty($dbPass) ? "(empty)" : "(set)") . "<br><br>";

try {
    // Buat koneksi PDO ke MySQL
    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Database connection successful!<br><br>";
    
    // Check if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        echo "Table 'users' exists.<br><br>";
        
        // Get table structure
        $stmt = $pdo->query("DESCRIBE users");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Table structure:<br>";
        echo "<pre>";
        foreach ($columns as $column) {
            echo $column['Field'] . " - " . $column['Type'] . " - " . ($column['Null'] === 'NO' ? 'NOT NULL' : 'NULL') . "<br>";
        }
        echo "</pre>";
        
        // Check if there are any users
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        echo "Number of users in the table: $count<br>";
        
        if ($count > 0) {
            $stmt = $pdo->query("SELECT id, name, email FROM users LIMIT 5");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<br>Sample users:<br>";
            echo "<pre>";
            print_r($users);
            echo "</pre>";
        }
    } else {
        echo "Table 'users' does not exist!<br>";
    }
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "<br>";
}
