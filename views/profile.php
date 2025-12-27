<div class="p-6 pt-10">
    <div class="flex flex-col items-center mb-8">
        <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mb-4 border-4 border-white shadow-lg">
            <i class="fas fa-user text-4xl text-green-600"></i>
        </div>
        <h1 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($_SESSION['user']['name']) ?></h1>
        <span class="text-xs bg-green-100 text-green-700 px-3 py-1 rounded-full font-bold mt-2 uppercase tracking-wider">
            <?= htmlspecialchars($_SESSION['user']['role']) ?>
        </span>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-50 flex items-center justify-between">
            <span class="text-sm text-gray-600">Username</span>
            <span class="text-sm font-bold text-gray-800"><?= htmlspecialchars($_SESSION['user']['username']) ?></span>
        </div>
        <div class="p-4 flex items-center justify-between">
            <span class="text-sm text-gray-600">Bergabung Sejak</span>
            <span class="text-sm font-bold text-gray-800">Des 2025</span>
        </div>
    </div>

    <a href="?action=logout" class="block w-full bg-red-50 text-red-600 font-bold py-4 rounded-xl text-center hover:bg-red-100 transition">
        <i class="fas fa-sign-out-alt mr-2"></i> Keluar
    </a>
    
    <div class="mt-8 text-center">
        <p class="text-[10px] text-gray-400">Versi Aplikasi 1.0.0</p>
    </div>
</div>
