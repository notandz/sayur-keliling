<div class="p-6 bg-white min-h-screen">
    <div class="flex flex-col items-center mb-8 mt-10">
        <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mb-4 text-4xl">😎</div>
        <h2 class="font-bold text-xl text-gray-800"><?= $_SESSION['user']['name'] ?></h2>
        <p class="text-sm text-gray-500">ID: KURIR-007</p>
        <span class="mt-2 bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full font-bold">Status: Aktif</span>
    </div>

    <div class="space-y-3">
        <div class="bg-gray-50 p-4 rounded-xl flex items-center justify-between">
            <span class="text-sm font-bold text-gray-700">Total Rating</span>
            <span class="text-yellow-500 font-bold"><i class="fas fa-star"></i> 4.9</span>
        </div>
        <a href="?action=logout" class="block w-full bg-red-50 text-red-600 font-bold text-center py-4 rounded-xl hover:bg-red-100">
            Keluar Aplikasi
        </a>
    </div>
</div>