<div class="flex flex-col items-center justify-center min-h-[80vh] px-6">
    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mb-6">
        <i class="fas fa-user-circle text-4xl text-green-600"></i>
    </div>
    <h1 class="text-2xl font-bold text-gray-800 mb-1">Selamat Datang</h1>
    <p class="text-xs text-gray-500 mb-8">Silahkan login untuk melanjutkan</p>

    <form method="POST" action="?action=login" class="w-full space-y-4">
        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1 ml-1">Username</label>
            <input type="text" name="username" class="w-full bg-white border border-gray-200 rounded-xl py-3 px-4 text-sm focus:outline-none focus:border-green-500">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1 ml-1">Password</label>
            <input type="password" name="password" class="w-full bg-white border border-gray-200 rounded-xl py-3 px-4 text-sm focus:outline-none focus:border-green-500">
        </div>
        <button type="submit" class="w-full bg-green-600 text-white font-bold py-3.5 rounded-xl shadow-lg mt-4">Masuk</button>
    </form>
    <div class="mt-8 text-[10px] text-center text-gray-400">
        <p>Gunakan: <b>admin</b> (Kurir) / <b>siti</b> (Vendor) / <b>budi</b> (Customer)</p>
        <p class="mt-1">Password: <b>123</b></p>
    </div>
</div>