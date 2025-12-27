<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pasar Keliling App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>

    <style>
        .hide-scroll::-webkit-scrollbar { display: none; }
        .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
        .leaflet-routing-container { display: none !important; }
        
        /* FIX NAVBAR ITEM */
        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            width: 100%;
            color: #9ca3af; /* Gray-400 */
            transition: all 0.2s;
        }
        .nav-item.active {
            color: #16a34a; /* Green-600 */
            background-color: #f0fdf4; /* Green-50 subtle bg */
        }
        .nav-item i { font-size: 20px; margin-bottom: 4px; }
        .nav-item span { font-size: 10px; font-weight: 600; }
    </style>
</head>
<body class="bg-gray-900 font-sans min-h-screen flex justify-center">

    <div class="w-full max-w-[480px] bg-gray-50 min-h-screen shadow-2xl relative flex flex-col">
        
        <main class="flex-1 overflow-y-auto hide-scroll pb-24 relative">
            <?php include $childView; ?>
        </main>

        <?php if(isset($_SESSION['user'])): $role = $_SESSION['user']['role']; ?>
        
        <div class="fixed bottom-0 w-full max-w-[480px] bg-white border-t border-gray-200 h-[70px] z-[1000] shadow-[0_-5px_15px_rgba(0,0,0,0.05)]">
            
            <?php if ($role == 'courier'): ?>
                <div class="grid grid-cols-4 h-full">
                    <a href="?page=courier_orders" class="nav-item <?= $page=='courier_orders'?'active':'' ?>">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Order</span>
                    </a>
                    
                    <a href="?page=courier_active" class="nav-item <?= $page=='courier_active'?'active':'' ?>">
                        <i class="fas fa-map-marked-alt"></i>
                        <span>Jalan</span>
                    </a>
                    
                    <a href="?page=courier_history" class="nav-item <?= $page=='courier_history'?'active':'' ?>">
                        <i class="fas fa-history"></i>
                        <span>Riwayat</span>
                    </a>
                    
                    <a href="?page=courier_profile" class="nav-item <?= $page=='courier_profile'?'active':'' ?>">
                        <i class="fas fa-user"></i>
                        <span>Profil</span>
                    </a>
                </div>

            <?php elseif ($role == 'vendor'): ?>
                <div class="grid grid-cols-3 h-full">
                    <a href="?page=vendor_home" class="nav-item <?= in_array($page, ['vendor_home', 'vendor_add', 'vendor_edit']) ? 'active' : '' ?>">
                        <i class="fas fa-store"></i>
                        <span>Dagangan</span>
                    </a>
                    <a href="?page=vendor_orders" class="nav-item <?= $page=='vendor_orders'?'active':'' ?>">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Pesanan</span>
                    </a>
                    <a href="?page=profile" class="nav-item <?= $page=='profile'?'active':'' ?>">
                        <i class="fas fa-user"></i>
                        <span>Akun</span>
                    </a>
                </div>

            <?php else: ?>
                <div class="grid grid-cols-3 h-full">
                    <a href="?page=customer_home" class="nav-item <?= in_array($page, ['customer_home', 'cart']) ? 'active' : '' ?>">
                        <i class="fas fa-shopping-basket"></i>
                        <span>Belanja</span>
                    </a>
                    <a href="?page=customer_orders" class="nav-item <?= in_array($page, ['customer_orders', 'customer_order_detail']) ? 'active' : '' ?>">
                        <i class="fas fa-receipt"></i>
                        <span>Pesanan</span>
                    </a>
                    <a href="?page=profile" class="nav-item <?= $page=='profile'?'active':'' ?>">
                        <i class="fas fa-user"></i>
                        <span>Akun</span>
                    </a>
                </div>
            <?php endif; ?>

        </div>
        <?php endif; ?>

    </div>
</body>
</html>