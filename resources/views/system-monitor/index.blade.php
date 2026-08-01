@extends('super-admin.layouts.app')

@section('title', 'Sistem Sağlığı - Harmanşah Yazılım')
@section('page-title', 'Sistem Sağlığı')

@section('content')
<div class="p-6" x-data="{ detail: null }">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Sistem Sağlığı İzleme</h1>
        <p class="text-gray-600 mt-1">Web ve mobil tarafta oluşan hatalar, başarısız kuyruk işleri ve throttle blokları burada listelenir.</p>
    </div>

    <!-- Özet Kartlar -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-red-200">
            <p class="text-xs font-medium text-red-600 uppercase">Kritik (24s)</p>
            <p class="text-2xl font-bold text-red-700">{{ $summary['critical_24h'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-yellow-200">
            <p class="text-xs font-medium text-yellow-600 uppercase">Uyarı (24s)</p>
            <p class="text-2xl font-bold text-yellow-700">{{ $summary['warning_24h'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-blue-200">
            <p class="text-xs font-medium text-blue-600 uppercase">Web (24s)</p>
            <p class="text-2xl font-bold text-blue-700">{{ $summary['web_24h'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-purple-200">
            <p class="text-xs font-medium text-purple-600 uppercase">Mobil (24s)</p>
            <p class="text-2xl font-bold text-purple-700">{{ $summary['mobile_24h'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-red-200">
            <p class="text-xs font-medium text-red-600 uppercase">Kritik (7g)</p>
            <p class="text-2xl font-bold text-red-700">{{ $summary['critical_7d'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200">
            <p class="text-xs font-medium text-gray-500 uppercase">Toplam (7g)</p>
            <p class="text-2xl font-bold text-gray-700">{{ $summary['total_7d'] }}</p>
        </div>
    </div>

    <!-- Filtreler -->
    <form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6 grid grid-cols-2 md:grid-cols-6 gap-3">
        <select name="source" class="rounded-lg border-gray-300 text-sm">
            <option value="">Tüm Kaynaklar</option>
            <option value="web" @selected(request('source')==='web')>Web</option>
            <option value="mobile" @selected(request('source')==='mobile')>Mobil</option>
        </select>
        <select name="severity" class="rounded-lg border-gray-300 text-sm">
            <option value="">Tüm Önem Dereceleri</option>
            <option value="critical" @selected(request('severity')==='critical')>Kritik</option>
            <option value="warning" @selected(request('severity')==='warning')>Uyarı</option>
            <option value="info" @selected(request('severity')==='info')>Bilgi</option>
        </select>
        <select name="type" class="rounded-lg border-gray-300 text-sm">
            <option value="">Tüm Tipler</option>
            @foreach($types as $type)
                <option value="{{ $type }}" @selected(request('type')===$type)>{{ $type }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border-gray-300 text-sm" placeholder="Başlangıç">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border-gray-300 text-sm" placeholder="Bitiş">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Mesajda ara..." class="rounded-lg border-gray-300 text-sm">
        <div class="col-span-2 md:col-span-6 flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700">Filtrele</button>
            <a href="{{ route('system-monitor.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-200">Temizle</a>
        </div>
    </form>

    <!-- Tablo -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Tarih</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Kaynak</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Tip</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Önem</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Mesaj</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($events as $event)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-gray-500">{{ $event->created_at->format('d.m.Y H:i:s') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $event->source === 'web' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                {{ $event->source === 'web' ? 'Web' : 'Mobil' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $event->type }}</td>
                        <td class="px-4 py-3">
                            @php
                                $severityClass = match($event->severity) {
                                    'critical' => 'bg-red-100 text-red-700',
                                    'warning' => 'bg-yellow-100 text-yellow-700',
                                    default => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $severityClass }}">{{ $event->severity }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-700 max-w-xl truncate">{{ $event->message }}</td>
                        <td class="px-4 py-3 text-right">
                            <button type="button"
                                @click="detail = {
                                    message: @js($event->message),
                                    stack: @js($event->stack_trace),
                                    context: @js($event->context),
                                    created_at: @js($event->created_at->format('d.m.Y H:i:s')),
                                    source: @js($event->source),
                                    type: @js($event->type),
                                    severity: @js($event->severity)
                                }"
                                class="text-blue-600 hover:text-blue-800 font-medium">Detay</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">Kayıt bulunamadı — harika, her şey sakin görünüyor.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $events->links() }}
    </div>

    <!-- Detay Modal -->
    <div x-show="detail" x-cloak
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
         @click.self="detail = null">
        <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[80vh] overflow-y-auto p-6" x-show="detail">
            <template x-if="detail">
                <div>
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900" x-text="detail.type"></h3>
                            <p class="text-sm text-gray-500" x-text="detail.created_at"></p>
                        </div>
                        <button @click="detail = null" class="text-gray-400 hover:text-gray-600">✕</button>
                    </div>
                    <p class="text-gray-800 mb-4" x-text="detail.message"></p>
                    <template x-if="detail.context">
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-600 mb-1">Bağlam</h4>
                            <pre class="bg-gray-50 rounded-lg p-3 text-xs overflow-x-auto" x-text="JSON.stringify(detail.context, null, 2)"></pre>
                        </div>
                    </template>
                    <template x-if="detail.stack">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-600 mb-1">Stack Trace</h4>
                            <pre class="bg-gray-50 rounded-lg p-3 text-xs overflow-x-auto whitespace-pre-wrap" x-text="detail.stack"></pre>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection
