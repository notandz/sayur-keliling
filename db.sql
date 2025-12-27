-- Jalankan script ini di SQL Editor Supabase Anda

-- Tabel Users
CREATE TABLE users (
    username TEXT PRIMARY KEY,
    password TEXT NOT NULL,
    role TEXT NOT NULL,
    name TEXT NOT NULL
);

-- Tabel Markets
CREATE TABLE markets (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL,
    lat FLOAT NOT NULL,
    lng FLOAT NOT NULL
);

-- Tabel Products
CREATE TABLE products (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL,
    price INTEGER NOT NULL,
    unit TEXT NOT NULL,
    market TEXT NOT NULL,
    image TEXT,
    vendor TEXT NOT NULL
);

-- Tabel Orders
CREATE TABLE orders (
    order_id TEXT PRIMARY KEY,
    customer_name TEXT NOT NULL,
    phone TEXT NOT NULL,
    address TEXT NOT NULL,
    status TEXT NOT NULL,
    lat FLOAT NOT NULL,
    lng FLOAT NOT NULL,
    items JSONB NOT NULL,
    created_at TIMESTAMP DEFAULT NOW(),
    courier TEXT
);

-- Insert Data Awal (Opsional, sesuaikan dengan data JSON Anda)
INSERT INTO users (username, password, role, name) VALUES
('admin', '$2a$12$YroXmbwgU1IOGR/5IF5C5OfdpNNYUndRC1brQzqhkqc1koz7Kdsky', 'courier', 'Bli Kurir'),
('siti', '$2a$12$YroXmbwgU1IOGR/5IF5C5OfdpNNYUndRC1brQzqhkqc1koz7Kdsky', 'vendor', 'Bu Siti'),
('budi', '$2a$12$YroXmbwgU1IOGR/5IF5C5OfdpNNYUndRC1brQzqhkqc1koz7Kdsky', 'customer', 'Pak Budi');

INSERT INTO markets (name, lat, lng) VALUES
('Pasar Badung', -8.654209, 115.216667),
('Pasar Kreneng', -8.6495, 115.2285),
('Pasar Sanglah', -8.6725, 115.2128);

-- Data produk contoh
INSERT INTO products (name, price, unit, market, image, vendor) VALUES
('Brokoli Segar', 12000, 'Kg', 'Pasar Badung', '🥦', 'siti'),
('Wortel Impor', 15000, 'Kg', 'Pasar Badung', '🥕', 'siti'),
('Daging Ayam', 35000, 'Kg', 'Pasar Badung', '🍗', 'siti');
