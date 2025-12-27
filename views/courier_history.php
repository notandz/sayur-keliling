<?php 
$allOrders = getJSON('orders');
$currentUser = $_SESSION['user']['username'] ?? '';
$flatRate = 15000; 


$historyOrders = array_filter($allOrders, function($o) use ($currentUser) {
    
    return isset($o['status']) && $o['status'] == 'completed' 
           && isset($o['courier']) && $o['courier'] == $currentUser;
});


$totalEarnings = count($historyOrders) * $flatRate;
$totalOrders = count($historyOrders);
?>

<div class="bg-gray-100 min-h-screen pb-24">
    
    <div class="bg-green-600 p-6 rounded-b-3xl shadow-lg mb-6 text-white relative overflow-hidden">
        <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute -left-6 bottom-0 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>

        <div class="relative z-10">
            <h1 class="font-bold text-lg mb-1">Dompet Saya</h1>
            <p class="text-[11px] opacity-80 mb-4">Ringkasan pendapatan hari ini</p>
            
            <div class="flex space-x-3">
                <div class="bg-white/20 backdrop-blur-sm border border-white/20 rounded-2xl p-4 flex-1">
                    <div class="flex items-center mb-1 opacity-80">
                        <i class="fas fa-wallet text-xs mr-2"></i>
                        <span class="text-[10px] uppercase tracking-wide">Total Uang</span>
                    </div>
                    <p class="font-bold text-2xl">Rp <?= number_format($totalEarnings, 0, ',', '.') ?></p>
                </div>
                
                <div class="bg-white/20 backdrop-blur-sm border border-white/20 rounded-2xl p-4 w-1/3 flex flex-col justify-center items-center">
                    <p class="font-bold text-2xl"><?= $totalOrders ?></p>
                    <p class="text-[10px] opacity-80 uppercase">Order Selesai</p>
                </div>
            </div>
        </div>
    </div>

    <div class="px-5">
        <h3 class="font-bold text-gray-700 text-xs mb-3 uppercase tracking-wide ml-1">Riwayat Pengantaran</h3>

        <?php if(empty($historyOrders)): ?>
            <div class="flex flex-col items-center justify-center py-10 text-gray-400 opacity-60">
                <i class="fas fa-history text-4xl mb-2"></i>
                <p class="text-xs">Belum ada riwayat pengantaran.</p>
            </div>
        <?php else: ?>
            
            <div class="space-y-3">
                <?php foreach($historyOrders as $o): 
                    
                    $markets = [];
                    foreach($o['items'] as $i) { if(!in_array($i['market'], $markets)) $markets[] = $i['market']; }
                    $marketString = implode(", ", $markets);
                ?>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center relative overflow-hidden group">
                    
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-green-500"></div>

                    <div>
                        <div class="flex items-center mb-1">
                            <span class="text-[9px] bg-green-100 text-green-700 px-1.5 py-0.5 rounded font-bold mr-2">SELESAI</span>
                            <span class="text-[10px] text-gray-400">#<?= $o['order_id'] ?></span>
                        </div>
                        <h4 class="font-bold text-sm text-gray-800"><?= $o['customer_name'] ?></h4>
                        <div class="mt-1 text-[10px] text-gray-500 flex flex-col gap-0.5">
                            <p class="flex items-center"><i class="fas fa-store w-4 text-orange-400"></i> <?= $marketString ?></p>
                            <p class="flex items-center"><i class="fas fa-map-marker-alt w-4 text-blue-400"></i> <?= $o['address'] ?></p>
                        </div>
                    </div>

                    <div class="text-right">
                        <p class="text-[10px] text-gray-400 mb-0.5">Ongkir Flat</p>
                        <p class="font-bold text-green-600 text-base">+Rp <?= number_format($flatRate, 0, ',', '.') ?></p>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>
    </div>
</div>