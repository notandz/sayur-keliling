<?php 
$allOrders = getJSON('orders');
$myOrders = [];


foreach ($allOrders as $o) {
    $vendorItems = [];
    foreach ($o['items'] as $i) {
        if (isset($i['vendor']) && $i['vendor'] == $_SESSION['user']['username']) {
            $vendorItems[] = $i;
        }
    }
    
    if (!empty($vendorItems)) {
        $o['items'] = $vendorItems; 
        $myOrders[] = $o;
    }
}


usort($myOrders, function($a, $b) {
    return strcmp($b['order_id'], $a['order_id']);
});
?>

<div class="p-6 pb-24">
    <header class="flex justify-between items-center mb-6 mt-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pesanan Masuk</h1>
            <p class="text-gray-500 text-sm">Siapkan barang untuk kurir.</p>
        </div>
    </header>

    <?php if(isset($_GET['status']) && $_GET['status'] == 'confirmed'): ?>
        <div class="bg-green-100 text-green-700 p-3 rounded-xl text-sm mb-6 border border-green-200">
            ✅ Barang berhasil diserahkan ke kurir!
        </div>
    <?php endif; ?>

    <?php if(empty($myOrders)): ?>
        <div class="text-center py-10 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-clipboard-list text-gray-400 text-2xl"></i>
            </div>
            <p class="text-gray-500 text-sm font-medium">Belum ada pesanan.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach($myOrders as $order): ?>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <span class="text-xs font-bold text-gray-400 block mb-1"><?= $order['order_id'] ?></span>
                        <span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-1 rounded-full">
                            <?= count($order['items']) ?> Barang
                        </span>
                    </div>
                    <?php
                        $statusColor = 'bg-gray-100 text-gray-500';
                        $statusLabel = 'Menunggu';
                        
                        
                        $allPickedUp = true;
                        foreach($order['items'] as $i) {
                            if (!isset($i['pickup_status']) || $i['pickup_status'] != 'picked_up') {
                                $allPickedUp = false;
                                break;
                            }
                        }

                        if ($allPickedUp) {
                            $statusColor = 'bg-green-100 text-green-600';
                            $statusLabel = 'Sudah Diambil';
                        } elseif ($order['status'] == 'active') {
                            $statusColor = 'bg-blue-100 text-blue-600';
                            $statusLabel = 'Kurir Menjemput';
                        }
                    ?>
                    <span class="text-xs font-bold px-3 py-1 rounded-full <?= $statusColor ?>">
                        <?= $statusLabel ?>
                    </span>
                </div>

                <?php if($order['status'] == 'active' && !$allPickedUp): ?>
                <div class="mt-2 mb-4 bg-blue-50 p-3 rounded-xl flex items-center">
                    <div class="w-8 h-8 bg-blue-200 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-motorcycle text-blue-600 text-xs"></i>
                    </div>
                    <div>
                        <p class="text-xs text-blue-800 font-bold">Kurir: <?= $order['courier'] ?? 'Admin' ?></p>
                        <p class="text-[10px] text-blue-600">Segera siapkan barang!</p>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="border-t border-gray-100 my-3 pt-3 space-y-2">
                    <?php foreach($order['items'] as $item): ?>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="text-lg mr-2"><?= $item['image'] ?? '📦' ?></span>
                            <div>
                                <p class="text-sm font-bold text-gray-800"><?= htmlspecialchars($item['name']) ?></p>
                                <p class="text-[10px] text-gray-500"><?= htmlspecialchars($item['unit']) ?></p>
                            </div>
                        </div>
                        <?php if(isset($item['pickup_status']) && $item['pickup_status'] == 'picked_up'): ?>
                            <span class="text-green-600 text-xs font-bold"><i class="fas fa-check"></i></span>
                        <?php else: ?>
                            <span class="text-gray-400 text-xs">Siapkan</span>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if($order['status'] == 'active' && !$allPickedUp): ?>
                    <a href="?action=vendor_confirm_pickup&order_id=<?= $order['order_id'] ?>" onclick="return confirm('Pastikan barang sudah diserahkan ke kurir?')" class="block w-full bg-green-600 hover:bg-green-700 text-white text-center py-3 rounded-xl text-xs font-bold mt-4 shadow transition">
                        <i class="fas fa-hand-holding-box mr-2"></i> Konfirmasi Serah Terima
                    </a>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
