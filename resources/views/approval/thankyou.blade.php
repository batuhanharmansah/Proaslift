<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Onay Alındı</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-lg border border-gray-100 p-8 text-center">
        <div class="text-5xl mb-4">✓</div>
        @if($alreadyApproved)
            <h1 class="text-xl font-bold text-gray-900 mb-2">Zaten Onaylanmış</h1>
            <p class="text-gray-600">{{ $buildingName }} için onay bekleyen iş kalmadı.</p>
        @else
            <h1 class="text-xl font-bold text-gray-900 mb-2">Onayınız Alındı</h1>
            <p class="text-gray-600">
                {{ $buildingName }} için
                <strong>{{ $approvedCount }}</strong>
                bakım işi onaylandı. Teşekkür ederiz.
            </p>
        @endif
    </div>
</body>
</html>
