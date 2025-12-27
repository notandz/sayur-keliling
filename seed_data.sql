-- Script untuk mengisi ulang database dengan data dari file JSON
-- Jalankan script ini di SQL Editor Supabase

-- 1. Bersihkan data lama (Opsional, agar tidak duplikat)
TRUNCATE TABLE orders, products, markets, users RESTART IDENTITY;

-- 2. Insert Users
INSERT INTO users (username, password, role, name) VALUES
('admin', '$2a$12$YroXmbwgU1IOGR/5IF5C5OfdpNNYUndRC1brQzqhkqc1koz7Kdsky', 'courier', 'Bli Kurir'),
('siti', '$2a$12$YroXmbwgU1IOGR/5IF5C5OfdpNNYUndRC1brQzqhkqc1koz7Kdsky', 'vendor', 'Bu Siti'),
('budi', '$2a$12$YroXmbwgU1IOGR/5IF5C5OfdpNNYUndRC1brQzqhkqc1koz7Kdsky', 'customer', 'Pak Budi');

-- 3. Insert Markets
INSERT INTO markets (id, name, lat, lng) VALUES
(1, 'Pasar Badung', -8.654209, 115.216667),
(2, 'Pasar Kreneng', -8.6495, 115.2285),
(3, 'Pasar Sanglah', -8.6725, 115.2128);

-- 4. Insert Products
INSERT INTO products (name, price, unit, market, image, vendor) VALUES
('Brokoli Segar', 12000, 'Kg', 'Pasar Badung', '🥦', 'siti'),
('Wortel Impor', 15000, 'Kg', 'Pasar Badung', '🥕', 'siti'),
('Daging Ayam', 35000, 'Kg', 'Pasar Kreneng', '🍗', 'siti'),
('Telur Ayam', 2000, 'Pcs', 'Pasar Kreneng', '🥚', 'siti'),
('Ikan Tongkol', 25000, 'Kg', 'Pasar Sanglah', '🐟', 'siti'),
('Cabai Rawit', 5000, 'Ons', 'Pasar Sanglah', '🌶️', 'siti');

-- 5. Insert Orders
INSERT INTO orders (order_id, customer_name, phone, address, status, lat, lng, items, created_at, courier) VALUES
(
    'ORD-1703401001', 
    'Pak Budi', 
    '081234567890', 
    'Jl. Nangka Selatan No. 10', 
    'pending', 
    -8.6397, 
    115.2238, 
    '[
        {"name": "Brokoli Segar", "price": 12000, "unit": "Kg", "market": "Pasar Badung", "image": "🥦", "vendor": "siti"},
        {"name": "Daging Ayam", "price": 35000, "unit": "Kg", "market": "Pasar Kreneng", "image": "🍗", "vendor": "siti"}
    ]', 
    '2025-12-24 08:00:00', 
    NULL
),
(
    'ORD-1703401002', 
    'Pak Budi', 
    '081234567890', 
    'Jl. Teuku Umar No. 55', 
    'completed', 
    -8.6700, 
    115.2100, 
    '[
        {"name": "Ikan Tongkol", "price": 25000, "unit": "Kg", "market": "Pasar Sanglah", "image": "🐟", "vendor": "siti", "pickup_status": "picked_up"},
        {"name": "Cabai Rawit", "price": 5000, "unit": "Ons", "market": "Pasar Sanglah", "image": "🌶️", "vendor": "siti", "pickup_status": "picked_up"}
    ]', 
    '2025-12-24 09:30:00', 
    'admin'
),
(
    'ORD-1703401003', 
    'Pak Budi', 
    '081234567890', 
    'Jl. Diponegoro No. 12', 
    'completed', 
    -8.6600, 
    115.2200, 
    '[
        {"name": "Telur Ayam", "price": 20000, "unit": "10 Pcs", "market": "Pasar Kreneng", "image": "🥚", "vendor": "siti", "pickup_status": "picked_up"}
    ]', 
    '2025-12-23 14:15:00', 
    'admin'
);
