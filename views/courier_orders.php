<?php 
$allOrders = getJSON('orders');
$currentUser = $_SESSION['user']['username'] ?? 'guest';


$myActiveOrders = array_filter($allOrders, function($o) use ($currentUser) { 
    return $o['status'] == 'active' && isset($o['courier']) && $o['courier'] == $currentUser; 
});
$hasActiveJob = count($myActiveOrders) > 0;
$pendingOrders = array_filter($allOrders, function($o) { return $o['status'] == 'pending'; });
?>

<style>
    /* Sembunyikan Input Asli */
    .chk-hidden { display: none; }
    
    /* Style Kotak Default (Belum Centang) */
    .chk-box {
        width: 24px; height: 24px;
        border: 2px solid #d1d5db; /* gray-300 */
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.2s;
        background-color: white;
    }
    
    /* Style Icon Default (Sembunyi) */
    .chk-box i {
        color: white;
        font-size: 12px;
        transform: scale(0);
        transition: all 0.2s;
    }

    /* KETIKA DICENTANG: Ubah Kotak jadi Hijau */
    .chk-hidden:checked + .chk-box {
        background-color: #16a34a; /* green-600 */
        border-color: #16a34a;
    }

    /* KETIKA DICENTANG: Munculkan Icon */
    .chk-hidden:checked + .chk-box i {
        transform: scale(1);
    }
</style>

<div class="bg-gray-100 min-h-screen relative flex flex-col pb-20">

    <nav class="bg-green-600 text-white px-5 py-4 shadow-md sticky top-0 z-50 flex justify-between items-center">
        <div class="flex items-center">
            <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-motorcycle text-sm"></i>
            </div>
            <div>
                <h1 class="font-bold text-base leading-tight">Daftar Order</h1>
                <p class="text-[10px] opacity-90">Halo, <?= $_SESSION['user']['name'] ?? 'Kurir' ?></p>
            </div>
        </div>
        <div class="relative">
            <div class="bg-white/20 p-2 rounded-full">
                <i class="fas fa-bell"></i>
            </div>
        </div>
    </nav>

    <?php if($hasActiveJob): ?>
    <div class="px-4 mt-6">
        <div class="bg-orange-50 border border-orange-200 p-5 rounded-xl flex justify-between items-center shadow-sm">
            <div>
                <p class="text-sm font-bold text-orange-800">Tugas Sedang Berjalan!</p>
                <p class="text-[10px] text-orange-600">Selesaikan dulu sebelum mengambil baru.</p>
            </div>
            <a href="?page=courier_active" class="bg-orange-600 text-white text-xs font-bold px-4 py-3 rounded-lg shadow hover:bg-orange-700 transition flex items-center">
                Lanjut <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
    
    <?php else: ?>
    
    <div class="flex-1 px-4 pt-6">

        <div class="mb-8">
            <h3 class="font-bold text-gray-500 text-[10px] mb-3 uppercase tracking-wider ml-1">Rekomendasi Sistem</h3>
            <a href="?action=start_batch" class="block w-full bg-gradient-to-r from-green-600 to-green-500 text-white p-5 rounded-2xl shadow-lg relative overflow-hidden active:scale-95 transition-transform">
                <div class="absolute right-0 top-0 opacity-10 transform translate-x-2 -translate-y-2"><i class="fas fa-bolt text-7xl"></i></div>
                <div class="flex items-center relative z-10">
                    <div class="bg-white/20 w-12 h-12 rounded-full flex items-center justify-center mr-4"><i class="fas fa-magic text-xl text-white-300"></i></div>
                    <div>
                        <h3 class="font-bold text-base">Ambil Order Otomatis</h3>
                        <p class="text-[11px] opacity-90 mt-0.5">Sistem memilihkan 2 rute terefisien.</p>
                    </div>
                </div>
            </a>
        </div>

        <h3 class="font-bold text-gray-500 text-[10px] mb-3 uppercase tracking-wider ml-1 flex justify-between items-center">
            <span>Pilih Manual (Maks. 2)</span>
            <span class="bg-gray-200 text-gray-600 px-2 py-0.5 rounded-md"><?= count($pendingOrders) ?> Tersedia</span>
        </h3>

        <?php if(empty($pendingOrders)): ?>
            <div class="flex flex-col items-center justify-center py-12 text-gray-400 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50">
                <i class="fas fa-box-open text-4xl mb-3 opacity-40"></i>
                <p class="text-sm font-bold">Sepi orderan nih...</p>
            </div>
        <?php else: ?>
            
            <form id="manualForm" action="?action=start_manual" method="POST" class="space-y-3 pb-24">
                <?php foreach($pendingOrders as $o): ?>
                <label class="block bg-white p-4 rounded-xl border border-gray-200 shadow-sm relative cursor-pointer hover:border-green-400 transition-all select-none">
                    <div class="flex justify-between items-start">
                        <div class="flex items-start flex-1 pr-4">
                            <div class="w-10 h-10 bg-gray-100 text-gray-600 rounded-full flex items-center justify-center font-bold text-sm mr-3 border border-gray-200">
                                <?= substr($o['customer_name'], 0, 1) ?>
                            </div>
                            <div class="w-full">
                                <div class="flex justify-between items-center">
                                    <h4 class="font-bold text-sm text-gray-800"><?= $o['customer_name'] ?></h4>
                                    <span class="text-[9px] bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded border border-gray-200">PENDING</span>
                                </div>
                                <div class="flex flex-wrap gap-1.5 mt-1.5">
                                    <?php 
                                    $markets = [];
                                    foreach($o['items'] as $i) { if(!in_array($i['market'], $markets)) $markets[] = $i['market']; }
                                    foreach($markets as $m): 
                                    ?>
                                        <span class="text-[9px] border border-orange-100 text-orange-600 px-1.5 py-0.5 rounded bg-orange-50 flex items-center">
                                            <i class="fas fa-store mr-1 text-[8px] opacity-70"></i><?= $m ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-2 flex items-center pt-2 border-t border-dashed border-gray-100">
                                    <i class="fas fa-map-marker-alt mr-1.5 text-red-400"></i> <span class="truncate w-48"><?= $o['address'] ?></span>
                                </p>
                            </div>
                        </div>

                        <div>
                            <input type="checkbox" name="selected_orders[]" value="<?= $o['order_id'] ?>" 
                                class="chk-hidden" onchange="handleSelection(this)">
                            
                            <div class="chk-box">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>

                    </div>
                </label>
                <?php endforeach; ?>

                <div id="bottomBar" class="fixed bottom-24 left-1/2 transform -translate-x-1/2 w-[90%] max-w-[400px] transition-transform duration-300 translate-y-[200%] z-40">
                    <div class="bg-gray-800 text-white p-4 rounded-2xl shadow-2xl flex justify-between items-center border border-gray-700">
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase tracking-wide mb-0.5">Terpilih</p>
                            <p class="font-bold text-lg leading-none"><span id="countSelected" class="text-green-400">0</span> <span class="text-sm font-normal text-gray-300">Order</span></p>
                        </div>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-xl font-bold text-xs shadow-lg flex items-center">
                            Ambil Order <i class="fas fa-paper-plane ml-2"></i>
                        </button>
                    </div>
                </div>

            </form>
        <?php endif; ?>

    </div>
    <?php endif; ?>
</div>

<script>
    function handleSelection(checkbox) {
        const checkboxes = document.querySelectorAll('.chk-hidden');
        const bottomBar = document.getElementById('bottomBar');
        const countSpan = document.getElementById('countSelected');
        
        let checkedCount = 0;
        checkboxes.forEach(box => { if (box.checked) checkedCount++; });

        if (checkedCount > 2) {
            checkbox.checked = false; 
            alert("Maksimal hanya bisa mengambil 2 order sekaligus!");
            return;
        }

        countSpan.innerText = checkedCount;
        if (checkedCount > 0) {
            bottomBar.classList.remove('translate-y-[200%]'); 
        } else {
            bottomBar.classList.add('translate-y-[200%]'); 
        }
    }
</script>