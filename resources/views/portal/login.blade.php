<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Müşteri Portalı - Giriş</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md p-6">
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Müşteri Portalı</h1>
            <p class="text-gray-500 text-sm mb-6">Binanızın bakım ve arıza bilgilerine buradan ulaşabilirsiniz.</p>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('portal.login.submit') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required
                           placeholder="05XX XXX XX XX"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Şifre</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition duration-200">
                    Giriş Yap
                </button>
            </form>

            <p class="text-xs text-gray-400 mt-6 text-center">
                Portal erişim bilgilerinizi bakım/servis firmanızdan alabilirsiniz.
            </p>
        </div>
    </div>
</body>
</html>
