<div class="overflow-x-auto">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Bina</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Asansör</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Sözleşme</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Aylık Ücret</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Durum</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">İşlemler</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($buildings as $building)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div>
                            <div class="font-semibold text-gray-900">{{ $building->name }}</div>
                            <div class="text-sm text-gray-500">{{ $building->district }}, {{ $building->city }}</div>
                            @if($building->primaryContact)
                                <div class="text-xs text-blue-600">{{ $building->primaryContact->name }}</div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm">
                            <div>{{ $building->elevator_count }} adet</div>
                            <div class="text-gray-500">{{ $building->elevator_type_label }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm">
                            <div>{{ $building->contract_type_label }}</div>
                            <div class="text-gray-500">{{ $building->contract_end_date->format('d.m.Y') }} bitim</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold text-green-600">
                        ₺{{ number_format($building->monthly_fee, 2) }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                            {{ $building->status === 'aktif' ? 'bg-green-100 text-green-800' :
                               ($building->status === 'beklemede' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ $building->status_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                            <a href="{{ route('buildings.show', $building) }}" class="text-blue-600 hover:text-blue-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <a href="{{ route('buildings.edit', $building) }}" class="text-yellow-600 hover:text-yellow-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        @if(request('search'))
                            Arama kriterlerinize uygun bina bulunamadı.
                        @else
                            Henüz bina bulunmuyor. İlk binayı eklemek için yukarıdaki butonu kullanın.
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@if($buildings->hasPages())
    <div class="px-6 py-4 border-t">{{ $buildings->links() }}</div>
@endif
