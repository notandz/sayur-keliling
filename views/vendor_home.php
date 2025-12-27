<?php 
$products = getJSON('products'); 
$myProducts = array_filter($products, function($p) {
    return isset($p['vendor']) && $p['vendor'] == $_SESSION['user']['username'];
});
?>

<div class="p-6 pb-24">
    <header class="flex justify-between items-center mb-6 mt-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Halo, <?= htmlspecialchars($_SESSION['user']['name']) ?>! 👋</h1>
            <p class="text-gray-500 text-sm">Kelola daganganmu disini.</p>
        </div>
    </header>

    
    <div class="grid grid-cols-2 gap-4 mb-8">
        <div class="bg-green-50 p-4 rounded-2xl border border-green-100">
            <div class="text-green-600 text-2xl font-bold mb-1"><?= count($myProducts) ?></div>
            <div class="text-gray-600 text-xs font-medium">Produk Aktif</div>
        </div>
        <div class="bg-blue-50 p-4 rounded-2xl border border-blue-100">
            <div class="text-blue-600 text-2xl font-bold mb-1">0</div>
            <div class="text-gray-600 text-xs font-medium">Terjual Hari Ini</div>
        </div>
    </div>

    <div class="flex justify-between items-end mb-4">
        <h2 class="font-bold text-gray-800 text-lg">Daftar Produk</h2>
        <a href="?page=vendor_add" class="bg-green-600 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-lg hover:bg-green-700 transition flex items-center">
            <i class="fas fa-plus mr-2"></i> Tambah
        </a>
    </div>

    <?php if(empty($myProducts)): ?>
        <div class="text-center py-10 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-box-open text-gray-400 text-2xl"></i>
            </div>
            <p class="text-gray-500 text-sm font-medium">Belum ada produk.</p>
            <p class="text-gray-400 text-xs mt-1">Mulai jualan sekarang!</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach($myProducts as $index => $item): ?>
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center">
                <div class="h-16 w-16 bg-gray-50 rounded-xl flex items-center justify-center text-2xl mr-4 flex-shrink-0">
                    <?= $item['image'] ?>
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-start">
                        <h3 class="font-bold text-gray-800"><?= htmlspecialchars($item['name']) ?></h3>
                        <div class="flex space-x-2">
                            <a href="?page=vendor_edit&index=<?= $index ?>" class="text-blue-500 hover:text-blue-700">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?action=delete_product&index=<?= $index ?>" onclick="return confirm('Hapus produk ini?')" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1"><?= htmlspecialchars($item['unit']) ?> • <span class="bg-gray-100 px-2 py-0.5 rounded-full"><?= htmlspecialchars($item['market']) ?></span></p>
                    <div class="font-bold text-green-600 text-sm mt-1">Rp <?= number_format($item['price'],0,',','.') ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
