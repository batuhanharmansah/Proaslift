@extends('super-admin.layouts.app')

@section('title', 'Sistem Sağlığı - Harmanşah Yazılım')
@section('page-title', 'Sistem Sağlığı')

@section('content')
<div class="p-6" x-data="{ detail: null }">
    <div class="mb-8 flex items-start justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Sistem Sağlığı İzleme</h1>
            <p class="text-gray-600 mt-1">Web ve mobil tarafta oluşan hatalar, başarısız kuyruk işleri ve throttle blokları burada listelenir.</p>
        </div>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('system-monitor.categorize') }}">
                @csrf
                <button type="submit" class="whitespace-nowrap bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50">
                    Yeniden Kategorize Et
                </button>
            </form>
            <form method="POST" action="{{ route('system-monitor.import-history') }}"
                  onsubmit="return confirm('Log dosyalarındaki ve başarısız kuyruk işlerindeki geçmiş hatalar içe aktarılacak. Devam edilsin mi?');">
                @csrf
                <button type="submit" class="whitespace-nowrap bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-900">
                    Eski Hataları İçe Aktar
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 text-sm whitespace-pre-line">
            {{ session('success') }}
        </div>
    @endif

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

    <!-- Kategori Dağılımı -->
    @if($categoryCounts->isNotEmpty())
        <div class="flex flex-wrap gap-2 mb-6">
            @foreach($categoryCounts as $row)
                <a href="{{ route('system-monitor.index', array_merge(request()->except('page'), ['category' => $row->category])) }}"
                   class="px-3 py-1.5 rounded-full text-xs font-medium border {{ request('category') === $row->category ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                    {{ \App\Support\SystemEventCategorizer::label($row->category) }}
                    <span class="opacity-60">({{ $row->total }})</span>
                </a>
            @endforeach
        </div>
    @endif

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
        <select name="category" class="rounded-lg border-gray-300 text-sm">
            <option value="">Tüm Kategoriler</option>
            @foreach(\App\Support\SystemEventCategorizer::LABELS as $slug => $label)
                <option value="{{ $slug }}" @selected(request('category')===$slug)>{{ $label }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border-gray-300 text-sm" placeholder="Başlangıç">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border-gray-300 text-sm" placeholder="Bitiş">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Mesajda ara..." class="rounded-lg border-gray-300 text-sm">
        <div class="flex gap-2">
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
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Kategori</th>
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
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                {{ $event->categoryLabel() }}
                            </span>
                        </td>
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
                                    category: @js($event->categoryLabel()),
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
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full max-h-[70vh] flex flex-col" x-show="detail">
            <template x-if="detail">
                <div class="flex flex-col overflow-hidden">
                    <div class="flex justify-between items-start p-4 border-b border-gray-100 flex-shrink-0">
                        <div>
                            <h3 class="text-base font-bold text-gray-900" x-text="detail.type"></h3>
                            <p class="text-xs text-gray-500 mt-0.5" x-text="detail.category + ' · ' + detail.created_at"></p>
                        </div>
                        <button @click="detail = null" class="text-gray-400 hover:text-gray-600 text-lg leading-none">✕</button>
                    </div>
                    <div class="p-4 overflow-y-auto text-sm">
                        <p class="text-gray-800 mb-4 break-words" x-text="detail.message"></p>
                        <template x-if="detail.context">
                            <div class="mb-4">
                                <h4 class="text-xs font-semibold text-gray-600 mb-1 uppercase">Bağlam</h4>
                                <pre class="bg-gray-50 rounded-lg p-3 text-xs overflow-x-auto max-h-40 overflow-y-auto" x-text="JSON.stringify(detail.context, null, 2)"></pre>
                            </div>
                        </template>
                        <template x-if="detail.stack">
                            <div>
                                <h4 class="text-xs font-semibold text-gray-600 mb-1 uppercase">Stack Trace</h4>
                                <pre class="bg-gray-50 rounded-lg p-3 text-xs overflow-x-auto whitespace-pre-wrap max-h-64 overflow-y-auto" x-text="detail.stack"></pre>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection
