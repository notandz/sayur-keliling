<?php 
$id = $_GET['id'] ?? null;
$allOrders = getJSON('orders');
$order = null;

foreach ($allOrders as $o) {
    if ($o['order_id'] === $id) {
        $order = $o;
        break;
    }
}

if (!$order || $order['customer_name'] != $_SESSION['user']['name']) {
    echo "Pesanan tidak ditemukan.";
    exit;
}
?>

<div class="p-6 pb-24">
    <header class="flex items-center mb-6 mt-4">
        <a href="?page=customer_orders" class="mr-4 text-gray-600 bg-white p-2 rounded-full shadow-sm hover:bg-gray-50"><i class="fas fa-arrow-left"></i></a>
        <h1 class="text-xl font-bold text-gray-800">Detail Pesanan</h1>
    </header>

    
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 mb-6">
        <div class="flex justify-between items-center mb-4">
            <span class="text-xs font-bold text-gray-400">ID: <?= $order['order_id'] ?></span>
            <span class="text-xs text-gray-400"><?= date('d M Y H:i', strtotime($order['created_at'] ?? 'now')) ?></span>
        </div>
        
        <?php
            $statusColor = 'bg-gray-100 text-gray-500';
            $statusLabel = 'Menunggu Konfirmasi';
            $statusIcon = 'fa-clock';
            
            if($order['status'] == 'active') {
                $statusColor = 'bg-blue-50 text-blue-600 border border-blue-100';
                $statusLabel = 'Sedang Diantar';
                $statusIcon = 'fa-motorcycle';
            } elseif($order['status'] == 'completed') {
                $statusColor = 'bg-green-50 text-green-600 border border-green-100';
                $statusLabel = 'Pesanan Selesai';
                $statusIcon = 'fa-check-circle';
            }
        ?>
        
        <div class="flex items-center <?= $statusColor ?> p-4 rounded-xl">
            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center mr-3 shadow-sm">
                <i class="fas <?= $statusIcon ?>"></i>
            </div>
            <div>
                <p class="font-bold text-sm"><?= $statusLabel ?></p>
                <?php if($order['status'] == 'active'): ?>
                    <p class="text-xs opacity-80">Kurir: <?= $order['courier'] ?? 'Admin' ?></p>
                <?php elseif($order['status'] == 'pending'): ?>
                    <p class="text-xs opacity-80">Sedang mencari kurir...</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <h2 class="font-bold text-gray-800 mb-4 text-sm">Daftar Belanjaan</h2>
    <div class="space-y-3 mb-6">
        <?php 
        $total = 0;
        foreach($order['items'] as $item): 
            $price = $item['price'] ?? 0;
            $total += $price;
        ?>
        <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-100 flex items-center">
            <div class="h-12 w-12 bg-gray-50 rounded-lg flex items-center justify-center text-xl mr-3 flex-shrink-0">
                <?= $item['image'] ?? '📦' ?>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-sm text-gray-800"><?= htmlspecialchars($item['name']) ?></h3>
                <p class="text-[10px] text-gray-500"><?= htmlspecialchars($item['market'] ?? '-') ?> • <?= htmlspecialchars($item['unit'] ?? 'pcs') ?></p>
            </div>
            <div class="font-bold text-gray-800 text-sm">Rp <?= number_format($price, 0, ',', '.') ?></div>
        </div>
        <?php endforeach; ?>
    </div>

    
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
        <h3 class="font-bold text-gray-800 mb-4 text-sm">Rincian Pembayaran</h3>
        <div class="space-y-2">
            <div class="flex justify-between text-xs text-gray-600">
                <span>Total Harga Barang</span>
                <span>Rp <?= number_format($total, 0, ',', '.') ?></span>
            </div>
            <div class="flex justify-between text-xs text-gray-600">
                <span>Ongkos Kirim</span>
                <span class="text-green-600 font-bold">Gratis</span>
            </div>
            <div class="border-t border-gray-100 my-2"></div>
            <div class="flex justify-between items-center">
                <span class="font-bold text-gray-800 text-sm">Total Bayar</span>
                <span class="font-bold text-lg text-green-600">Rp <?= number_format($total, 0, ',', '.') ?></span>
            </div>
        </div>
    </div>

    
    <div class="mt-6">
        <h3 class="font-bold text-gray-800 mb-3 text-sm">Alamat Pengiriman</h3>
        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
            <div class="flex items-start">
                <i class="fas fa-map-marker-alt text-red-500 mt-1 mr-3"></i>
                <div>
                    <p class="text-sm font-bold text-gray-800 mb-1"><?= htmlspecialchars($order['customer_name']) ?></p>
                    <p class="text-xs text-gray-600 leading-relaxed"><?= htmlspecialchars($order['address']) ?></p>
                    <p class="text-xs text-gray-500 mt-2"><i class="fas fa-phone-alt mr-1"></i> <?= htmlspecialchars($order['phone']) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
