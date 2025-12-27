<?php
// functions.php

// Simple .env loader for local development
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}

function getDB() {
    static $pdo;
    if (!$pdo) {
        // Ambil kredensial dari Environment Variables
        $databaseUrl = getenv('DATABASE_URL');
        
        if ($databaseUrl) {
            // Parse DATABASE_URL jika tersedia (format: postgresql://user:pass@host:port/dbname)
            $url = parse_url($databaseUrl);
            $host = $url['host'];
            $port = $url['port'] ?? 5432;
            $user = $url['user'];
            $pass = $url['pass'];
            $db   = ltrim($url['path'], '/');
        } else {
            // Fallback ke variabel terpisah (SUPABASE_*)
            $host = getenv('SUPABASE_HOST');
            $db   = getenv('SUPABASE_DB');
            $user = getenv('SUPABASE_USER');
            $pass = getenv('SUPABASE_PASSWORD');
            $port = getenv('SUPABASE_PORT') ?: 5432;
        }
        
        if (!$host) {
            // Fallback untuk local development jika env belum diset
            // Ganti ini dengan kredensial Supabase Anda jika menjalankan di XAMPP lokal
            die("Error: Environment variables untuk database belum diset.");
        }

        $dsn = "pgsql:host=$host;port=$port;dbname=$db";
        
        try {
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            die("Koneksi Database Gagal: " . $e->getMessage());
        }
    }
    return $pdo;
}

function getJSON($filename) {
    $pdo = getDB();
    
    if ($filename == 'users') {
        $stmt = $pdo->query("SELECT * FROM users");
        return $stmt->fetchAll();
    }
    
    if ($filename == 'markets') {
        $stmt = $pdo->query("SELECT * FROM markets");
        return $stmt->fetchAll();
    }
    
    if ($filename == 'products') {
        // Order by ID untuk menjaga urutan konsisten
        $stmt = $pdo->query("SELECT * FROM products ORDER BY id ASC");
        return $stmt->fetchAll();
    }
    
    if ($filename == 'orders') {
        $stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
        $orders = $stmt->fetchAll();
        // Decode kolom items (JSONB) kembali menjadi array PHP
        foreach ($orders as &$o) {
            $o['items'] = json_decode($o['items'], true);
            // Pastikan lat/lng bertipe float
            $o['lat'] = (float)$o['lat'];
            $o['lng'] = (float)$o['lng'];
        }
        return $orders;
    }
    
    return [];
}

function saveJSON($filename, $data) {
    $pdo = getDB();
    
    // PERINGATAN: Metode ini menghapus semua data dan menulis ulang (Truncate & Insert).
    // Ini dilakukan untuk menjaga kompatibilitas dengan logika aplikasi yang berbasis file JSON.
    // Untuk aplikasi produksi skala besar, sebaiknya ubah logika menjadi INSERT/UPDATE spesifik.

    if ($filename == 'products') {
        $pdo->beginTransaction();
        try {
            $pdo->exec("DELETE FROM products"); 
            // Reset sequence ID agar kembali ke 1 (opsional, tapi bagus untuk konsistensi array index)
            // $pdo->exec("ALTER SEQUENCE products_id_seq RESTART WITH 1"); 

            $stmt = $pdo->prepare("INSERT INTO products (name, price, unit, market, image, vendor) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($data as $row) {
                $stmt->execute([
                    $row['name'], 
                    $row['price'], 
                    $row['unit'], 
                    $row['market'], 
                    $row['image'], 
                    $row['vendor']
                ]);
            }
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
    
    if ($filename == 'orders') {
        $pdo->beginTransaction();
        try {
            $pdo->exec("DELETE FROM orders");
            $stmt = $pdo->prepare("INSERT INTO orders (order_id, customer_name, phone, address, status, lat, lng, items, created_at, courier) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($data as $row) {
                $stmt->execute([
                    $row['order_id'],
                    $row['customer_name'],
                    $row['phone'],
                    $row['address'],
                    $row['status'],
                    $row['lat'],
                    $row['lng'],
                    json_encode($row['items']), // Encode array items ke JSONB
                    $row['created_at'] ?? date('Y-m-d H:i:s'),
                    $row['courier'] ?? null
                ]);
            }
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
?>