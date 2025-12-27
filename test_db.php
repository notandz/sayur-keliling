<?php
// test_db.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Connection Test</h1>";

// 1. Check Extensions
echo "<h2>1. Checking PHP Extensions</h2>";
if (!extension_loaded('pdo')) {
    die("<p style='color:red'>❌ Error: PDO extension is not enabled.</p>");
}
if (!extension_loaded('pdo_pgsql')) {
    echo "<p style='color:red'>❌ Error: pdo_pgsql extension is not enabled.</p>";
    echo "<p>Please enable <code>extension=pdo_pgsql</code> in your php.ini file.</p>";
    echo "<p>Current php.ini: " . php_ini_loaded_file() . "</p>";
} else {
    echo "<p style='color:green'>✅ pdo_pgsql extension is enabled.</p>";
}

// 2. Load .env
echo "<h2>2. Loading .env</h2>";
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            putenv(sprintf('%s=%s', trim($name), trim($value)));
            echo "Loaded: $name<br>";
        }
    }
} else {
    echo "<p style='color:red'>❌ .env file not found.</p>";
}

// 3. Parse Connection String
echo "<h2>3. Parsing Connection Details</h2>";
$databaseUrl = getenv('DATABASE_URL');
if ($databaseUrl) {
    $url = parse_url($databaseUrl);
    $host = $url['host'] ?? 'Not found';
    $port = $url['port'] ?? 5432;
    $user = $url['user'] ?? 'Not found';
    $db   = ltrim($url['path'] ?? '', '/');
    
    echo "Host: $host<br>";
    echo "Port: $port<br>";
    echo "User: $user<br>";
    echo "Database: $db<br>";
} else {
    echo "<p style='color:red'>❌ DATABASE_URL not found in environment.</p>";
}

// 4. Attempt Connection
echo "<h2>4. Attempting Connection</h2>";
try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$db";
    $pdo = new PDO($dsn, $user, $url['pass'] ?? '');
    echo "<p style='color:green'>✅ Connection Successful!</p>";
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Connection Failed: " . $e->getMessage() . "</p>";
}
?>