<?php $markets = getJSON('markets'); ?>

<div class="p-6 pt-10">
    <h1 class="text-2xl font-bold text-gray-800 mb-1">Mulai Jualan</h1>
    <p class="text-gray-500 text-sm mb-6">Barangmu akan dijemput oleh admin pasar keliling.</p>

    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="bg-green-100 text-green-700 p-3 rounded-xl text-sm mb-4 border border-green-200">
            ✅ Barang berhasil ditambahkan ke pasar!
        </div>
    <?php endif; ?>

    <form method="POST" action="?action=add_product" class="space-y-4">
        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Nama Barang</label>
            <input type="text" name="name" required class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-green-500 focus:outline-none">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Harga (Rp)</label>
                <input type="number" name="price" required class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-green-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Satuan</label>
                <select name="unit" class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm">
                    <option>Ikat</option>
                    <option>Kg</option>
                    <option>Ons</option>
                    <option>Pcs</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Lokasi Pickup (Pasar)</label>
            <select name="market" class="w-full bg-white border border-gray-300 rounded-xl px-4 py-3 text-sm">
                <?php foreach($markets as $m): ?>
                    <option value="<?= $m['name'] ?>"><?= $m['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="w-full bg-green-600 text-white py-4 rounded-xl font-bold shadow-lg hover:bg-green-700 transition mt-4">
            Simpan Barang
        </button>
    </form>
</div>