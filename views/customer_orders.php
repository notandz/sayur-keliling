<?php 
$allOrders = getJSON('orders');
$myOrders = array_filter($allOrders, function($o) {
    return isset($o['customer_name']) && $o['customer_name'] == $_SESSION['user']['name'];
});

usort($myOrders, function($a, $b) {
    return strcmp($b['order_id'], $a['order_id']); 
});
?>

<div class="p-6 pb-24">
    <h1 class="text-2xl font-bold text-gray-800 mb-6 mt-4">Pesanan Saya</h1>

    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="bg-green-100 text-green-700 p-4 rounded-xl text-sm mb-6 border border-green-200 flex items-start">
            <i class="fas fa-check-circle mt-1 mr-3 text-lg"></i>
            <div>
                <span class="font-bold block">Pesanan Berhasil!</span>
                Mohon tunggu, kurir kami akan segera mencarikan barangmu.
            </div>
        </div>
    <?php endif; ?>

    <?php if(empty($myOrders)): ?>
        <div class="text-center py-10 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-receipt text-gray-400 text-2xl"></i>
            </div>
            <p class="text-gray-500 text-sm font-medium">Belum ada pesanan.</p>
            <a href="?page=customer_home" class="text-green-600 text-xs font-bold mt-2 inline-block">Belanja Sekarang</a>
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
                        if($order['status'] == 'active') {
                            $statusColor = 'bg-blue-100 text-blue-600';
                            $statusLabel = 'Sedang Diantar';
                        } elseif($order['status'] == 'completed') {
                            $statusColor = 'bg-green-100 text-green-600';
                            $statusLabel = 'Selesai';
                        }
                    ?>
                    <span class="text-xs font-bold px-3 py-1 rounded-full <?= $statusColor ?>">
                        <?= $statusLabel ?>
                    </span>
                </div>
                
                <div class="border-t border-gray-100 my-3 pt-3">
                    <p class="text-xs text-gray-500 mb-1">Total Belanja</p>
                    <?php 
                        $total = 0;
                        foreach($order['items'] as $i) $total += ($i['price'] ?? 0);
                    ?>
                    <p class="font-bold text-gray-800">Rp <?= number_format($total, 0, ',', '.') ?></p>
                </div>

                <?php if($order['status'] == 'active'): ?>
                <div class="mt-3 bg-blue-50 p-3 rounded-xl flex items-center">
                    <div class="w-8 h-8 bg-blue-200 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-motorcycle text-blue-600 text-xs"></i>
                    </div>
                    <div>
                        <p class="text-xs text-blue-800 font-bold">Kurir sedang jalan!</p>
                        <p class="text-[10px] text-blue-600">Pesananmu sedang diantar oleh <?= $order['courier'] ?? 'Kurir' ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <a href="?page=customer_order_detail&id=<?= $order['order_id'] ?>" class="block w-full bg-gray-50 text-gray-600 text-center py-3 rounded-xl text-xs font-bold mt-4 hover:bg-gray-100 transition">
                    Lihat Detail Pesanan
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
