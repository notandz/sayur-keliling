<?php
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo '<script>window.location="?page=customer_home";</script>';
    exit;
}

$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'];
}
?>

<div class="p-6 pb-24">
    <header class="flex items-center mb-6">
        <a href="?page=customer_home" class="mr-4 text-gray-600"><i class="fas fa-arrow-left"></i></a>
        <h1 class="text-xl font-bold text-gray-800">Keranjang Belanja</h1>
    </header>

    <div class="space-y-4 mb-8">
        <?php foreach($_SESSION['cart'] as $index => $item): ?>
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center">
            <div class="h-16 w-16 bg-gray-50 rounded-xl flex items-center justify-center text-2xl mr-4 flex-shrink-0">
                <?= $item['image'] ?>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-gray-800"><?= htmlspecialchars($item['name']) ?></h3>
                <p class="text-xs text-gray-500"><?= htmlspecialchars($item['market']) ?></p>
                <div class="font-bold text-green-600 text-sm mt-1">Rp <?= number_format($item['price'],0,',','.') ?></div>
            </div>
            <a href="?action=remove_from_cart&index=<?= $index ?>" class="text-red-400 hover:text-red-600 p-2">
                <i class="fas fa-trash"></i>
            </a>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 mb-8">
        <div class="flex justify-between items-center mb-2">
            <span class="text-gray-600 text-sm">Total Belanja</span>
            <span class="font-bold text-gray-800">Rp <?= number_format($total,0,',','.') ?></span>
        </div>
        <div class="flex justify-between items-center mb-2">
            <span class="text-gray-600 text-sm">Ongkos Kirim</span>
            <span class="font-bold text-green-600">Gratis</span>
        </div>
        <div class="border-t border-gray-100 my-3"></div>
        <div class="flex justify-between items-center">
            <span class="font-bold text-gray-800">Total Bayar</span>
            <span class="font-bold text-xl text-green-600">Rp <?= number_format($total,0,',','.') ?></span>
        </div>
    </div>

    <?php if(!isset($_SESSION['user'])): ?>
        <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-100 text-center mb-6">
            <p class="text-yellow-800 text-sm mb-2">Silahkan login untuk melanjutkan pesanan.</p>
            <a href="?page=login" class="inline-block bg-yellow-500 text-white px-6 py-2 rounded-lg text-sm font-bold">Login Sekarang</a>
        </div>
    <?php else: ?>
        <form action="?action=checkout" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Alamat Pengiriman</label>
                <textarea name="address" required class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-green-500 focus:outline-none h-24" placeholder="Contoh: Jl. Mawar No. 12, Denpasar"></textarea>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nomor WhatsApp</label>
                <input type="tel" name="phone" required class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-green-500 focus:outline-none" placeholder="08123456789">
            </div>
            
            <button type="submit" class="w-full bg-green-600 text-white py-4 rounded-xl font-bold shadow-lg hover:bg-green-700 transition mt-4">
                Pesan Sekarang
            </button>
        </form>
    <?php endif; ?>
</div>
