<?php 

$allMarkets = getJSON('markets');
$orders = getJSON('orders');


$pickupPoints = [];
$marketsNeeded = [];

foreach ($orders as $order) {
    foreach ($order['items'] as $item) {
        $marketName = $item['market'];
        if (!in_array($marketName, $marketsNeeded)) {
            $marketsNeeded[] = $marketName;
        }
    }
}

foreach ($marketsNeeded as $marketName) {
    $marketData = null;
    foreach ($allMarkets as $m) {
        if ($m['name'] == $marketName) {
            $marketData = $m;
            break;
        }
    }
    if ($marketData) {
        $marketData['type'] = 'pickup';
        $marketData['tasks'] = []; 
        foreach ($orders as $order) {
            $itemsForThisMarket = [];
            foreach ($order['items'] as $item) {
                if ($item['market'] == $marketName) {
                    $itemsForThisMarket[] = $item['name'];
                }
            }
            if (!empty($itemsForThisMarket)) {
                $marketData['tasks'][] = [
                    'customer_name' => $order['customer_name'],
                    'items' => $itemsForThisMarket
                ];
            }
        }
        $pickupPoints[] = $marketData;
    }
}

$deliveryPoints = [];
foreach ($orders as $o) {
    $o['type'] = 'delivery';
    $o['name'] = $o['customer_name'];
    $simpleItems = array_map(function($i) { return $i['name']; }, $o['items']);
    $o['tasks'] = [[ 'customer_name' => 'Serahkan Barang', 'items' => $simpleItems ]];
    $deliveryPoints[] = $o;
}

$adminPoint = [
    'name' => 'Posisi Anda',
    'lat' => -8.6559, 
    'lng' => 115.2190,
    'type' => 'start',
    'tasks' => []
];

$routeSequence = array_merge([$adminPoint], $pickupPoints, $deliveryPoints);
?>

<div class="relative h-full flex flex-col">
    
    <div id="map" class="h-[45vh] w-full z-10 border-b border-gray-300"></div>

    <div class="bg-gray-800 text-white px-5 py-3 flex justify-between items-center shadow-lg z-20">
        <div>
            <p class="text-[10px] text-gray-400 uppercase tracking-widest">Tujuan Selanjutnya</p>
            <h2 id="next-dest-name" class="font-bold text-base text-yellow-400 truncate w-48">Memuat...</h2>
        </div>
        <div class="text-right">
            <button onclick="zoomToActive()" class="bg-gray-700 hover:bg-gray-600 p-2 rounded-full text-xs">
                <i class="fas fa-crosshairs text-white"></i>
            </button>
        </div>
    </div>

    <div class="flex-1 bg-gray-100 relative overflow-y-auto overflow-x-hidden pb-24 px-4 pt-8">
        
        <div class="relative">
            
            <div class="absolute left-5 top-4 bottom-0 border-l-2 border-dashed border-gray-300 z-0 -ml-[1px]"></div>

            <?php foreach($routeSequence as $index => $point): 
                if($index == 0) continue; 
                
                $isPickup = $point['type'] == 'pickup';
                $colorClass = $isPickup ? 'orange' : 'green';
                $iconClass = $isPickup ? 'shopping-basket' : 'box-open';
                $badgeText = $isPickup ? 'BELANJA' : 'SERAH TERIMA';
            ?>
            
            <div id="step-card-<?= $index ?>" class="relative pl-14 pb-6 transition-all duration-300 pointer-events-none select-none">
                
                <div class="absolute left-5 top-0 -translate-x-1/2 bg-<?= $colorClass ?>-100 text-<?= $colorClass ?>-600 border-2 border-<?= $colorClass ?>-200 w-9 h-9 rounded-full flex items-center justify-center text-sm shadow-sm z-10 ring-4 ring-gray-100">
                    <i class="fas fa-<?= $iconClass ?>"></i>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    
                    <div class="p-3 border-b border-gray-100 bg-<?= $colorClass ?>-50/30">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[9px] font-bold text-<?= $colorClass ?>-600 bg-<?= $colorClass ?>-100 px-1.5 py-0.5 rounded uppercase tracking-wide"><?= $badgeText ?></span>
                                <h3 class="font-bold text-gray-800 text-sm mt-1"><?= $point['name'] ?></h3>
                                <p class="text-[10px] text-gray-500"><i class="fas fa-map-marker-alt mr-1"></i><?= $isPickup ? 'Ambil Pesanan' : $point['address'] ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-white space-y-2">
                        <?php foreach($point['tasks'] as $task): ?>
                        <div class="bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                            <div class="flex items-center mb-1.5 pb-1 border-b border-gray-200">
                                <i class="fas fa-user text-gray-400 text-[10px] mr-1.5"></i>
                                <span class="text-[10px] font-bold text-gray-700 uppercase"><?= $task['customer_name'] ?></span>
                            </div>
                            <div class="space-y-1">
                                <?php foreach($task['items'] as $item): ?>
                                <label class="flex items-center space-x-2 cursor-pointer group/item">
                                    <input type="checkbox" class="form-checkbox h-3.5 w-3.5 text-<?= $colorClass ?>-600 rounded border-gray-300 focus:ring-<?= $colorClass ?>-500" disabled>
                                    <span class="text-[11px] text-gray-600 group-hover/item:text-gray-900"><?= $item ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="p-2 bg-gray-50 border-t border-gray-100">
                        <button id="btn-active-<?= $index ?>" onclick="nextRoute(<?= $index ?>)" class="hidden w-full bg-<?= $colorClass ?>-600 text-white text-[11px] font-bold py-2.5 rounded-lg shadow hover:bg-<?= $colorClass ?>-700 transition flex justify-center items-center">
                            <span><?= $isPickup ? 'Selesai Belanja' : 'Konfirmasi Terkirim' ?></span>
                            <i class="fas fa-chevron-right ml-2"></i> 
                        </button>
                        
                        <button id="btn-locked-<?= $index ?>" disabled class="w-full bg-gray-100 text-gray-400 text-[11px] font-bold py-2.5 rounded-lg flex justify-center items-center cursor-not-allowed border border-gray-200">
                            <i class="fas fa-lock mr-1.5 text-[10px]"></i> Menunggu Giliran
                        </button>

                        <button id="btn-done-<?= $index ?>" disabled class="hidden w-full bg-green-50 text-green-600 text-[11px] font-bold py-2.5 rounded-lg flex justify-center items-center border border-green-200">
                            <i class="fas fa-check-circle mr-1.5"></i> Selesai
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <div id="step-card-finish" class="relative pl-14 hidden pb-10">
                <div class="absolute left-5 top-0 -translate-x-1/2 bg-gray-800 text-white w-9 h-9 rounded-full flex items-center justify-center text-sm shadow z-10 ring-4 ring-gray-100">
                    <i class="fas fa-flag-checkered"></i>
                </div>
                <div class="bg-white border border-gray-200 p-4 rounded-xl shadow-lg text-center">
                    <h3 class="font-bold text-base text-gray-800">Tugas Selesai!</h3>
                    <button onclick="location.reload()" class="bg-gray-800 text-white px-4 py-2 mt-3 rounded-lg text-xs font-bold">Kembali</button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    var map, routingControl;
    var currentStepIndex = 0; 
    var routeSequence = <?php echo json_encode($routeSequence); ?>;
    
    document.addEventListener('DOMContentLoaded', function() {
        map = L.map('map'); 
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OSM' }).addTo(map);
        renderCurrentRoute();
    });

    function renderCurrentRoute() {
        if (currentStepIndex >= routeSequence.length - 1) {
            finishAll();
            return;
        }

        var startPoint = routeSequence[currentStepIndex];
        var endPoint = routeSequence[currentStepIndex + 1];

        document.getElementById('next-dest-name').innerText = endPoint.name;
        
        updateCardStates(currentStepIndex + 1);

        if (routingControl) map.removeControl(routingControl);

        routingControl = L.Routing.control({
            waypoints: [
                L.latLng(startPoint.lat, startPoint.lng),
                L.latLng(endPoint.lat, endPoint.lng)
            ],
            routeWhileDragging: false, draggableWaypoints: false, addWaypoints: false, showAlternatives: false, fitSelectedRoutes: true,
            lineOptions: { styles: [{color: '#2563eb', opacity: 0.9, weight: 6}] },
            createMarker: function(i, wp, nWps) {
                if (i === 1) return L.marker(wp.latLng).bindPopup("<b>Tujuan:</b> " + endPoint.name).openPopup();
                return L.marker(wp.latLng); 
            }
        }).addTo(map);
    }

    window.nextRoute = function(index) {
        if (index !== currentStepIndex + 1) return;
        currentStepIndex++;
        renderCurrentRoute();
        setTimeout(() => {
            var nextCardId = 'step-card-' + (currentStepIndex + 1);
            var nextEl = document.getElementById(nextCardId);
            if(nextEl) nextEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 300);
    };

    function updateCardStates(activeIndex) {
        for (let i = 1; i < routeSequence.length; i++) {
            let card = document.getElementById('step-card-' + i);
            let btnActive = document.getElementById('btn-active-' + i);
            let btnLocked = document.getElementById('btn-locked-' + i);
            let btnDone = document.getElementById('btn-done-' + i);
            let checkboxes = card.querySelectorAll('input[type="checkbox"]');

            if(card) {
                
                card.classList.remove('pointer-events-none', 'select-none', 'z-20', 'scale-105');
                btnActive.classList.add('hidden');
                btnLocked.classList.add('hidden');
                btnDone.classList.add('hidden');

                if (i < activeIndex) {
                    
                    card.classList.add('pointer-events-none'); 
                    btnDone.classList.remove('hidden'); 
                    checkboxes.forEach(cb => { cb.checked = true; cb.disabled = true; });

                } else if (i === activeIndex) {
                    
                    
                    card.classList.add('z-20', 'scale-105'); 
                    btnActive.classList.remove('hidden');
                    checkboxes.forEach(cb => cb.disabled = false);

                } else {
                    
                    card.classList.add('pointer-events-none', 'select-none'); 
                    btnLocked.classList.remove('hidden'); 
                    checkboxes.forEach(cb => { cb.checked = false; cb.disabled = true; });
                }
            }
        }
    }

    function zoomToActive() {
        if (routingControl) {
            var bounds = L.latLngBounds(routingControl.getWaypoints().map(wp => wp.latLng));
            map.fitBounds(bounds);
        }
    }

    function finishAll() {
        if (routingControl) map.removeControl(routingControl);
        document.getElementById('next-dest-name').innerText = "SELESAI";
        document.getElementById('step-card-finish').classList.remove('hidden');
        document.getElementById('step-card-finish').scrollIntoView({ behavior: 'smooth' });
    }
</script>