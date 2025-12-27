<?php 
$allMarkets = getJSON('markets'); 
$allOrders = getJSON('orders');


$currentUser = $_SESSION['user']['username'] ?? '';
$myOrders = array_filter($allOrders, function($o) use ($currentUser) {
    return $o['status'] == 'active' && isset($o['courier']) && $o['courier'] == $currentUser;
});


if (empty($myOrders)) {
    ?>
    <div class="h-full flex flex-col items-center justify-center bg-gray-50 pb-20 px-6 text-center">
        <div class="w-32 h-32 bg-green-50 rounded-full flex items-center justify-center mb-6 shadow-sm border border-green-100">
            <i class="fas fa-map-marked-alt text-5xl text-green-300"></i>
        </div>
        
        <h2 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Rute Aktif</h2>
        <p class="text-sm text-gray-500 mb-8 max-w-[260px] mx-auto leading-relaxed">
            Anda belum memilih orderan untuk diantar hari ini. Silahkan ambil orderan baru untuk memulai navigasi.
        </p>
        
        <a href="?page=courier_orders" class="bg-green-600 text-white font-bold py-3.5 px-8 rounded-xl shadow-lg hover:bg-green-700 transition transform active:scale-95 flex items-center">
            <i class="fas fa-clipboard-list mr-2"></i> Mulai Ambil Order
        </a>
    </div>
    <?php
    return; 
}


$pickupPoints = []; 
$marketsNeeded = [];


foreach ($myOrders as $o) { 
    foreach ($o['items'] as $i) { 
        if (!in_array($i['market'], $marketsNeeded)) $marketsNeeded[] = $i['market']; 
    } 
}


foreach ($marketsNeeded as $mn) {
    foreach ($allMarkets as $m) {
        if ($m['name'] == $mn) {
            $m['type'] = 'pickup'; 
            $m['tasks'] = [];
            foreach ($myOrders as $o) {
                $items = []; 
                foreach ($o['items'] as $i) { 
                    if ($i['market'] == $mn) {
                        
                        $items[] = [
                            'name' => $i['name'],
                            'pickup_status' => $i['pickup_status'] ?? 'pending'
                        ]; 
                    }
                }
                if ($items) {
                    $m['tasks'][] = [
                        'customer_name' => $o['customer_name'], 
                        'phone' => $o['phone'] ?? '', 
                        'items' => $items
                    ];
                }
            }
            $pickupPoints[] = $m; 
            break;
        }
    }
}


$deliveryPoints = [];
foreach ($myOrders as $o) {
    $o['type'] = 'delivery'; 
    $o['name'] = $o['customer_name'];
    $o['tasks'] = [[ 
        'customer_name' => 'Serahkan Barang', 
        'phone' => $o['phone'] ?? '', 
        'items' => array_map(function($i){
            return ['name' => $i['name'], 'pickup_status' => 'pending']; 
        }, $o['items']) 
    ]];
    $deliveryPoints[] = $o;
}


$routeSequence = array_merge([['name'=>'Start','lat'=>-8.6559,'lng'=>115.2190,'type'=>'start']], $pickupPoints, $deliveryPoints);
$totalSteps = count($routeSequence);
?>

<div class="relative h-full flex flex-col">
    <div id="map" class="h-[45vh] w-full z-10 border-b border-gray-300"></div>
    
    <div class="bg-gray-800 text-white px-5 py-3 flex justify-between items-center shadow-lg z-20">
        <div>
            <p class="text-[10px] text-gray-400 uppercase">Tujuan Selanjutnya</p>
            <h2 id="next-dest-name" class="font-bold text-yellow-400 truncate w-48">...</h2>
        </div>
        <button onclick="zoomToActive()" class="bg-gray-700 hover:bg-gray-600 p-2 rounded-full text-xs transition-colors">
            <i class="fas fa-crosshairs text-white"></i>
        </button>
    </div>

    <div class="flex-1 bg-gray-100 relative overflow-y-auto overflow-x-hidden pb-24 px-4 pt-8">
        <div class="relative">
            
            <?php foreach($routeSequence as $idx => $pt): if($idx==0) continue; 
                $isPickup = $pt['type']=='pickup'; 
                $color = $isPickup?'orange':'green'; 
                $icon = $isPickup?'shopping-basket':'box-open';
            ?>
            
            <div id="step-card-<?= $idx ?>" class="relative pl-12 pr-3 pb-6 pointer-events-none select-none">
                
                <?php if ($idx < $totalSteps - 1): ?>
                <div class="absolute left-4 top-6 bottom-0 border-l-2 border-dashed border-gray-300 z-0 -ml-[1px]"></div>
                <?php endif; ?>

                <div class="absolute left-4 top-0 -translate-x-1/2 bg-<?= $color ?>-100 text-<?= $color ?>-600 border-2 border-<?= $color ?>-200 w-9 h-9 rounded-full flex items-center justify-center text-sm shadow z-10 ring-4 ring-gray-100">
                    <i class="fas fa-<?= $icon ?>"></i>
                </div>
                
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden transition-all duration-300 origin-left">
                    
                    <div class="p-3 border-b border-gray-100 bg-<?= $color ?>-50/30">
                        <div>
                            <span class="text-[9px] font-bold text-<?= $color ?>-600 bg-<?= $color ?>-100 px-1.5 py-0.5 rounded uppercase"><?= $isPickup?'BELANJA':'ANTAR' ?></span>
                            <h3 class="font-bold text-gray-800 text-sm mt-1"><?= $pt['name'] ?></h3>
                            <p class="text-[10px] text-gray-500"><i class="fas fa-map-marker-alt mr-1"></i><?= $isPickup ? 'Ambil Pesanan' : $pt['address'] ?></p>
                        </div>
                    </div>
                    
                    <div class="p-3 bg-white space-y-2">
                        <?php foreach($pt['tasks'] as $t): ?>
                        <div class="bg-gray-50 p-2 rounded-lg border border-gray-100">
                            <div class="flex justify-between items-center mb-1 pb-1 border-b border-gray-200">
                                <div class="flex items-center">
                                    <i class="fas fa-user text-gray-400 text-[10px] mr-1.5"></i>
                                    <span class="text-[10px] font-bold text-gray-700 uppercase"><?= $t['customer_name'] ?></span>
                                </div>
                                <?php if(!empty($t['phone'])): ?>
                                <a href="https://wa.me/<?= $t['phone'] ?>" target="_blank" class="text-green-600 hover:text-green-700 pointer-events-auto p-1 bg-green-50 rounded-full w-6 h-6 flex items-center justify-center">
                                    <i class="fab fa-whatsapp text-xs"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                            <?php foreach($t['items'] as $it): ?>
                            <label class="flex items-center space-x-2">
                                <?php if($isPickup && isset($it['pickup_status']) && $it['pickup_status'] == 'picked_up'): ?>
                                    <i class="fas fa-check-circle text-green-500 text-xs"></i>
                                <?php else: ?>
                                    <input type="checkbox" class="h-3.5 w-3.5 text-<?= $color ?>-600 rounded" disabled>
                                <?php endif; ?>
                                <span class="text-[11px] text-gray-600 <?= ($isPickup && isset($it['pickup_status']) && $it['pickup_status'] == 'picked_up') ? 'line-through opacity-50' : '' ?>">
                                    <?= $it['name'] ?>
                                </span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                        <?php endforeach; ?>
                        
                        <?php if($isPickup): ?>
                        <div id="upload-area-<?= $idx ?>" class="hidden mt-2 pt-2 border-t border-dashed border-gray-200">
                            <label id="label-upload-<?= $idx ?>" class="flex flex-col items-center justify-center w-full h-14 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-red-50 hover:bg-red-100 pointer-events-auto transition-colors animate-pulse">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-camera text-red-400 mb-1"></i>
                                    <p class="text-[9px] text-red-500 font-bold">Wajib Foto Bukti</p>
                                </div>
                                <input type="file" class="hidden" accept="image/*" onchange="previewImage(this, <?= $idx ?>)">
                            </label>
                            <img id="preview-img-<?= $idx ?>" class="hidden mt-2 w-full h-24 object-cover rounded-lg border border-gray-200">
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="p-2 bg-gray-50 border-t border-gray-100">
                        <button id="btn-active-<?= $idx ?>" onclick="nextRoute(<?= $idx ?>)" disabled 
                            class="hidden w-full bg-gray-400 text-white text-[11px] font-bold py-2.5 rounded-lg shadow pointer-events-auto cursor-not-allowed transition-all">
                            <i class="fas fa-ban mr-1"></i> <?= $isPickup ? 'Upload Foto Dulu' : 'Konfirmasi Selesai' ?>
                        </button>
                        <button id="btn-locked-<?= $idx ?>" disabled class="w-full bg-gray-100 text-gray-400 text-[11px] font-bold py-2.5 rounded-lg flex justify-center items-center">
                            <i class="fas fa-lock mr-1.5"></i> Menunggu
                        </button>
                        <button id="btn-done-<?= $idx ?>" disabled class="hidden w-full bg-green-50 text-green-600 text-[11px] font-bold py-2.5 rounded-lg">
                            <i class="fas fa-check mr-1"></i> Selesai
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <div id="step-card-finish" class="relative pl-12 pr-3 hidden pb-10 transition-opacity duration-500">
                <div class="absolute left-4 top-0 -translate-x-1/2 bg-green-600 text-white w-9 h-9 rounded-full flex items-center justify-center text-sm shadow z-10 ring-4 ring-gray-100">
                    <i class="fas fa-check"></i>
                </div>
                <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm text-center">
                    <h3 class="font-bold text-lg text-gray-800 mb-1">🎉 Tugas Selesai!</h3>
                    <p class="text-xs text-gray-500 mb-4">Semua pengantaran batch ini telah tuntas.</p>
                    <a href="?action=finish_batch" class="block bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl text-xs font-bold shadow w-full pointer-events-auto transition-transform active:scale-95">
                        Selesaikan & Ambil Order Lagi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var map, routingControl, currentStepIndex = 0;
    var routeSequence = <?= json_encode($routeSequence) ?>;
    
    document.addEventListener('DOMContentLoaded', function() {
        map = L.map('map'); L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        renderCurrentRoute();
    });

    function renderCurrentRoute() {
        if (currentStepIndex >= routeSequence.length - 1) { finishAll(); return; }
        var start = routeSequence[currentStepIndex], end = routeSequence[currentStepIndex + 1];
        document.getElementById('next-dest-name').innerText = end.name;
        updateCardStates(currentStepIndex + 1);
        if (routingControl) map.removeControl(routingControl);
        routingControl = L.Routing.control({ 
            waypoints: [L.latLng(start.lat, start.lng), L.latLng(end.lat, end.lng)], 
            routeWhileDragging: false, showAlternatives: false, fitSelectedRoutes: true, 
            lineOptions: {styles: [{color: '#2563eb', weight: 6}]} 
        }).addTo(map);
    }

    function updateCardStates(activeIdx) {
        for (let i = 1; i < routeSequence.length; i++) {
            let cardWrapper = document.getElementById('step-card-'+i);
            if(cardWrapper){
                let innerCard = cardWrapper.querySelector('div.bg-white.border');
                let btnAct = document.getElementById('btn-active-'+i);
                let btnLock = document.getElementById('btn-locked-'+i);
                let btnDone = document.getElementById('btn-done-'+i);
                let upArea = document.getElementById('upload-area-'+i);

                cardWrapper.classList.remove('z-20'); 
                innerCard.classList.remove('shadow-md', 'border-green-500'); 
                btnAct.classList.add('hidden'); btnLock.classList.add('hidden'); btnDone.classList.add('hidden'); 
                if(upArea) upArea.classList.add('hidden');

                if(i < activeIdx) { 
                    cardWrapper.classList.add('pointer-events-none'); 
                    btnDone.classList.remove('hidden'); 
                    cardWrapper.querySelectorAll('input[type="checkbox"]').forEach(c=>c.checked=true); 
                }
                else if(i === activeIdx) { 
                    cardWrapper.classList.add('z-20'); 
                    innerCard.classList.add('shadow-md', 'border-green-500'); 
                    btnAct.classList.remove('hidden'); 
                    if(upArea) upArea.classList.remove('hidden'); 
                    let isDelivery = routeSequence[i].type == 'delivery';
                    if(isDelivery) {
                        btnAct.disabled = false;
                        btnAct.className = `w-full bg-green-600 hover:bg-green-700 text-white text-[11px] font-bold py-2.5 rounded-lg shadow pointer-events-auto transition-all`;
                        btnAct.innerHTML = 'Konfirmasi Selesai <i class="fas fa-chevron-right ml-2"></i>';
                    }
                }
                else { 
                    cardWrapper.classList.add('pointer-events-none'); 
                    btnLock.classList.remove('hidden'); 
                }
            }
        }
    }

    window.nextRoute = function(idx) { 
        if(idx !== currentStepIndex+1) return; 
        currentStepIndex++; 
        renderCurrentRoute(); 
        setTimeout(()=>{
            let nextEl = document.getElementById('step-card-'+(currentStepIndex+1));
            if(nextEl) nextEl.scrollIntoView({behavior:'smooth',block:'center'});
        }, 300); 
    };

    window.previewImage = function(inp, idx) { 
        if(inp.files[0]){ 
            let r = new FileReader(); 
            r.onload = function(e){
                let img = document.getElementById('preview-img-'+idx); img.src = e.target.result; img.classList.remove('hidden');
                let label = document.getElementById('label-upload-'+idx);
                label.classList.remove('bg-red-50', 'hover:bg-red-100', 'animate-pulse');
                label.classList.add('bg-green-50', 'border-green-300');
                label.innerHTML = '<div class="flex flex-col items-center"><i class="fas fa-check-circle text-green-500"></i><p class="text-[9px] text-green-600 font-bold">Foto Siap</p></div>';
                let btn = document.getElementById('btn-active-'+idx);
                btn.disabled = false; 
                let isPickup = routeSequence[idx].type == 'pickup';
                let activeColor = isPickup ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700';
                btn.className = `w-full ${activeColor} text-white text-[11px] font-bold py-2.5 rounded-lg shadow pointer-events-auto transition-all`;
                btn.innerHTML = 'Selesai & Lanjut <i class="fas fa-chevron-right ml-2"></i>';
            }; 
            r.readAsDataURL(inp.files[0]); 
        } 
    };

    function finishAll() { 
        if(routingControl) map.removeControl(routingControl); 
        document.getElementById('next-dest-name').innerText="SELESAI"; 
        
        let lastCardId = currentStepIndex;
        let lastCardWrapper = document.getElementById('step-card-'+lastCardId);
        if(lastCardWrapper) {
            lastCardWrapper.classList.remove('z-20');
            lastCardWrapper.classList.add('pointer-events-none', 'opacity-80', 'grayscale'); 
            let innerCard = lastCardWrapper.querySelector('div.bg-white.border');
            innerCard.classList.remove('shadow-md', 'border-green-500');
            document.getElementById('btn-active-'+lastCardId).classList.add('hidden');
            document.getElementById('btn-done-'+lastCardId).classList.remove('hidden');
        }

        let finishCard = document.getElementById('step-card-finish');
        finishCard.classList.remove('hidden');
        setTimeout(() => { finishCard.scrollIntoView({behavior:'smooth', block:'center'}); }, 100);
    }
    
    window.zoomToActive = function() {
        if (routingControl) {
            var bounds = L.latLngBounds(routingControl.getWaypoints().map(wp => wp.latLng));
            map.fitBounds(bounds);
        }
    }
</script>