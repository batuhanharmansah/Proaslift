<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bakım Onayı - {{ $buildingName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-lg mx-auto px-4 py-8">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-blue-600 px-6 py-5 text-white">
                <h1 class="text-xl font-bold">Bakım Onayı</h1>
                <p class="text-blue-100 text-sm mt-1">{{ $buildingName }}</p>
            </div>

            <div class="px-6 py-5">
                @if($pendingCount === 0)
                    <p class="text-gray-600 text-center py-6">Onay bekleyen bakım işi bulunmuyor.</p>
                @else
                    <p class="text-sm text-gray-600 mb-4">
                        Aşağıda {{ $pendingCount }} tamamlanan bakım işi listelenmektedir. Onay vererek işlemi tamamlayabilirsiniz.
                    </p>

                    <ul class="space-y-3 mb-6">
                        @foreach($jobs as $job)
                            <li class="border border-gray-200 rounded-xl p-4 bg-gray-50">
                                <div class="flex justify-between items-start gap-2">
                                    <span class="font-semibold text-gray-900">{{ $job['maintenance_type_label'] }}</span>
                                    <span class="text-xs text-gray-500 whitespace-nowrap">{{ $job['scheduled_date'] }}</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">Teknisyen: {{ $job['employee_name'] }}</p>
                                @if($job['work_description'])
                                    <p class="text-sm text-gray-700 mt-2">{{ $job['work_description'] }}</p>
                                @endif
                                @if($job['total_cost'])
                                    <p class="text-xs text-gray-500 mt-1">Tutar: ₺{{ number_format($job['total_cost'], 2, ',', '.') }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ul>

                    <form method="POST" action="{{ url('/onay/' . $token) }}" class="space-y-4 border-t border-gray-100 pt-5">
                        @csrf
                        <div>
                            <label for="approved_by_name" class="block text-sm font-medium text-gray-700 mb-1">Ad Soyad</label>
                            <input type="text" name="approved_by_name" id="approved_by_name" required
                                   value="{{ old('approved_by_name') }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Adınız ve soyadınız">
                            @error('approved_by_name')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-start gap-2">
                            <input type="checkbox" name="accept_terms" id="accept_terms" value="1"
                                   {{ old('accept_terms') ? 'checked' : '' }}
                                   class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="accept_terms" class="text-sm text-gray-700">
                                Yapılan işleri okudum ve onaylıyorum.
                            </label>
                        </div>
                        @error('accept_terms')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror

                        <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl transition">
                            Onayla ve Gönder
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">
            Bu form güvenli bağlantı ile sunulmaktadır.
        </p>
    </div>
</body>
</html>
