<?php 
$markets = getJSON('markets'); 
$products = getJSON('products');
$index = $_GET['index'] ?? null;

if ($index === null || !isset($products[$index]) || $products[$index]['vendor'] != $_SESSION['user']['username']) {
    echo "Produk tidak ditemukan atau Anda tidak memiliki akses.";
    exit;
}

$item = $products[$index];
?>

<div class="p-6 pt-10">
    <h1 class="text-2xl font-bold text-gray-800 mb-1">Edit Produk</h1>
    <p class="text-gray-500 text-sm mb-6">Perbarui informasi barang daganganmu.</p>

    <form method="POST" action="?action=edit_product" class="space-y-4">
        <input type="hidden" name="index" value="<?= $index ?>">
        
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Nama Barang</label>
            <input type="text" name="name" value="<?= htmlspecialchars($item['name']) ?>" required class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-green-500 focus:outline-none">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Harga (Rp)</label>
                <input type="number" name="price" value="<?= $item['price'] ?>" required class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-green-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Satuan</label>
                <select name="unit" class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm">
                    <option <?= $item['unit'] == 'Ikat' ? 'selected' : '' ?>>Ikat</option>
                    <option <?= $item['unit'] == 'Kg' ? 'selected' : '' ?>>Kg</option>
                    <option <?= $item['unit'] == 'Ons' ? 'selected' : '' ?>>Ons</option>
                    <option <?= $item['unit'] == 'Pcs' ? 'selected' : '' ?>>Pcs</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Lokasi Pickup (Pasar)</label>
            <select name="market" class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm">
                <?php foreach($markets as $m): ?>
                    <option value="<?= $m['name'] ?>" <?= $item['market'] == $m['name'] ? 'selected' : '' ?>><?= $m['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="w-full bg-green-600 text-white py-4 rounded-xl font-bold shadow-lg hover:bg-green-700 transition mt-4">
            Simpan Perubahan
        </button>
        
        <a href="?page=vendor_home" class="block text-center text-gray-500 text-sm mt-4 hover:text-gray-700">Batal</a>
    </form>
</div>
