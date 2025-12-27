<?php
session_start();
require_once 'functions.php';


if (isset($_GET['action']) && $_GET['action'] == 'login') {
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    $users = getJSON('users');
    foreach ($users as $u) {
        if ($u['username'] === $username && password_verify($password, $u['password'])) {
            $_SESSION['user'] = $u;
            if ($u['role'] == 'courier') {
                header('Location: ?page=courier_orders');
            } elseif ($u['role'] == 'vendor') {
                header('Location: ?page=vendor_home');
            } else {
                header('Location: ?page=customer_home');
            }
            exit;
        }
    }
    header('Location: ?page=login&error=1'); exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header('Location: ?page=login'); exit;
}


if (isset($_GET['action']) && $_GET['action'] == 'start_batch') {
    $orders = getJSON('orders');
    
    
    $pendingOrders = array_filter($orders, function($o) { return $o['status'] == 'pending'; });
    
    if (empty($pendingOrders)) {
        header('Location: ?page=courier_orders&msg=empty'); exit;
    }

    
    $batch = [];
    
    
    $firstOrder = array_shift($pendingOrders);
    $batch[] = $firstOrder['order_id'];
    $marketFirst = array_column($firstOrder['items'], 'market'); 

    
    $secondOrderId = null;
    foreach ($pendingOrders as $candidate) {
        $marketCandidate = array_column($candidate['items'], 'market');
        
        if (!empty(array_intersect($marketFirst, $marketCandidate))) {
            $secondOrderId = $candidate['order_id'];
            break; 
        }
    }

    
    if (!$secondOrderId && !empty($pendingOrders)) {
        $next = array_shift($pendingOrders);
        $secondOrderId = $next['order_id'];
    }

    if ($secondOrderId) $batch[] = $secondOrderId;

    
    foreach ($orders as &$o) {
        if (in_array($o['order_id'], $batch)) {
            $o['status'] = 'active';
            $o['courier'] = $_SESSION['user']['username']; 
        }
    }
    saveJSON('orders', $orders);

    
    header('Location: ?page=courier_active');
    exit;
}


if (isset($_GET['action']) && $_GET['action'] == 'finish_batch') {
    $orders = getJSON('orders');
    foreach ($orders as &$o) {
        
        if ($o['status'] == 'active' && isset($o['courier']) && $o['courier'] == $_SESSION['user']['username']) {
            $o['status'] = 'completed';
        }
    }
    saveJSON('orders', $orders);
    header('Location: ?page=courier_history');
    exit;
}


if (isset($_GET['action']) && $_GET['action'] == 'vendor_confirm_pickup') {
    $orderId = $_GET['order_id'];
    $orders = getJSON('orders');
    $currentUser = $_SESSION['user']['username'];

    foreach ($orders as &$o) {
        if ($o['order_id'] == $orderId) {
            foreach ($o['items'] as &$item) {
                
                if (isset($item['vendor']) && $item['vendor'] == $currentUser) {
                    $item['pickup_status'] = 'picked_up';
                }
            }
        }
    }
    saveJSON('orders', $orders);
    header('Location: ?page=vendor_orders&status=confirmed');
    exit;
}


if (isset($_GET['action']) && $_GET['action'] == 'add_product') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ?page=vendor_add'); exit;
    }

    $products = getJSON('products');
    $newProduct = [
        'name' => $_POST['name'],
        'price' => (int)$_POST['price'],
        'unit' => $_POST['unit'],
        'market' => $_POST['market'],
        'image' => '🥬', 
        'vendor' => $_SESSION['user']['username']
    ];

    $products[] = $newProduct;
    saveJSON('products', $products);

    header('Location: ?page=vendor_home&status=success');
    exit;
}


if (isset($_GET['action']) && $_GET['action'] == 'edit_product') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ?page=vendor_home'); exit;
    }

    $id = $_POST['index']; 
    $products = getJSON('products');

    if (isset($products[$id]) && $products[$id]['vendor'] == $_SESSION['user']['username']) {
        $products[$id]['name'] = $_POST['name'];
        $products[$id]['price'] = (int)$_POST['price'];
        $products[$id]['unit'] = $_POST['unit'];
        $products[$id]['market'] = $_POST['market'];
        saveJSON('products', $products);
    }

    header('Location: ?page=vendor_home&status=updated');
    exit;
}


if (isset($_GET['action']) && $_GET['action'] == 'delete_product') {
    $id = $_GET['index'];
    $products = getJSON('products');

    if (isset($products[$id]) && $products[$id]['vendor'] == $_SESSION['user']['username']) {
        array_splice($products, $id, 1); 
        saveJSON('products', $products);
    }

    header('Location: ?page=vendor_home&status=deleted');
    exit;
}




if (isset($_GET['action']) && $_GET['action'] == 'add_to_cart') {
    $index = $_GET['index'];
    $products = getJSON('products');
    
    if (isset($products[$index])) {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        $_SESSION['cart'][] = $products[$index];
    }
    
    header('Location: ?page=customer_home');
    exit;
}


if (isset($_GET['action']) && $_GET['action'] == 'remove_from_cart') {
    $index = $_GET['index'];
    if (isset($_SESSION['cart'][$index])) {
        array_splice($_SESSION['cart'], $index, 1);
    }
    header('Location: ?page=cart');
    exit;
}


if (isset($_GET['action']) && $_GET['action'] == 'checkout') {
    if (empty($_SESSION['cart']) || !isset($_SESSION['user'])) {
        header('Location: ?page=customer_home'); exit;
    }

    $orders = getJSON('orders');
    $newOrder = [
        'order_id' => 'ORD-' . time(),
        'customer_name' => $_SESSION['user']['name'],
        'phone' => $_POST['phone'],
        'address' => $_POST['address'],
        'status' => 'pending',
        'lat' => -8.6500, 
        'lng' => 115.2167,
        'items' => $_SESSION['cart'],
        'created_at' => date('Y-m-d H:i:s')
    ];

    $orders[] = $newOrder;
    saveJSON('orders', $orders);
    
    unset($_SESSION['cart']); 
    header('Location: ?page=customer_orders&status=success');
    exit;
}




if (isset($_GET['action']) && $_GET['action'] == 'start_manual') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['selected_orders'])) {
        header('Location: ?page=courier_orders'); exit;
    }
    
    $selectedIds = $_POST['selected_orders'];
    $orders = getJSON('orders');
    
    
    if (count($selectedIds) > 2) { header('Location: ?page=courier_orders'); exit; }
    
    foreach ($orders as &$o) {
        if (in_array($o['order_id'], $selectedIds) && $o['status'] == 'pending') {
            $o['status'] = 'active';
            $o['courier'] = $_SESSION['user']['username'];
        }
    }
    saveJSON('orders', $orders);
    header('Location: ?page=courier_active'); exit;
}


$page = $_GET['page'] ?? 'customer_home';


$guestPages = ['customer_home', 'login'];
if (!in_array($page, $guestPages) && !isset($_SESSION['user'])) {
    header('Location: ?page=login');
    exit;
}

$viewFile = "views/{$page}.php";
if (file_exists($viewFile)) {
    $childView = $viewFile;
    include 'layout.php';
} else {
    echo "Halaman tidak ditemukan.";
}
?>