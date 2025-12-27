<?php 
$products = getJSON('products'); 
?>

<header class="bg-green-600 p-6 text-white rounded-b-3xl shadow-lg sticky top-0 z-30">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold">Pasar Keliling 🛵</h1>
        <?php if(!isset($_SESSION['user'])): ?>
            <a href="?page=login" class="bg-white text-green-600 px-4 py-2 rounded-lg text-xs font-bold shadow-sm hover:bg-green-50 transition">
                Login
            </a>
        <?php else: ?>
            <div class="flex items-center gap-2">
                <span class="text-xs font-medium bg-green-700 px-3 py-1 rounded-full">
                    <?= htmlspecialchars($_SESSION['user']['name']) ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
    <div class="relative">
        <input type="text" placeholder="Mau masak apa hari ini?" class="w-full py-3 pl-10 pr-4 rounded-xl text-sm text-gray-800 focus:outline-none">
        <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
    </div>
</header>

<div class="px-5 mt-6">
    <h2 class="font-bold text-gray-800 mb-4">Produk Tersedia</h2>
    
    <?php if(empty($products)): ?>
        <p class="text-center text-gray-400 text-sm mt-10">Belum ada pedagang yang jualan :(</p>
    <?php else: ?>
        <div class="grid grid-cols-2 gap-4 pb-20">
            <?php foreach($products as $index => $item): ?>
            <div class="bg-white p-3 rounded-2xl shadow-sm border border-gray-100 relative">
                <span class="absolute top-2 left-2 bg-green-100 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded-full">
                    <?= htmlspecialchars($item['market']) ?>
                </span>
                <div class="h-24 bg-gray-50 rounded-xl mb-2 flex items-center justify-center text-4xl">
                    <?= $item['image'] ?>
                </div>
                <h3 class="font-bold text-sm text-gray-800"><?= htmlspecialchars($item['name']) ?></h3>
                <p class="text-xs text-gray-500 mb-2"><?= htmlspecialchars($item['unit']) ?></p>
                <div class="flex justify-between items-center">
                    <span class="font-bold text-green-600 text-sm">Rp <?= number_format($item['price'],0,',','.') ?></span>
                    <a href="?action=add_to_cart&index=<?= $index ?>" class="bg-green-600 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs hover:bg-green-700 transition transform active:scale-90">
                        <i class="fas fa-plus"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
    <div class="fixed bottom-24 left-0 w-full px-6 flex justify-center z-40 pointer-events-none">
        <a href="?page=cart" class="bg-green-800 text-white px-6 py-3 rounded-full shadow-xl flex items-center gap-3 pointer-events-auto hover:bg-green-900 transition transform hover:-translate-y-1">
            <div class="bg-white text-green-800 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">
                <?= count($_SESSION['cart']) ?>
            </div>
            <span class="font-bold text-sm">Lihat Keranjang</span>
            <i class="fas fa-arrow-right text-xs"></i>
        </a>
    </div>
    <?php endif; ?>
</div>