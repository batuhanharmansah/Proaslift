@extends('layouts.app')

@section('title', 'Kâr / Maliyet Raporu - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Kâr / Maliyet Raporu</h1>
                <p class="text-gray-600 mt-1">Net satış, kayıtlı maliyet ve kâr/zarar ayrı bir bakış açısıyla</p>
            </div>
            <a href="{{ route('financial.report') }}" class="bg-primary-500 hover:bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                ← Finansal Raporlara Dön
            </a>
        </div>
    </div>

    <!-- Bu Ay Özet Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-green-200 p-5">
            <p class="text-xs font-medium text-green-600 uppercase">Net Satış</p>
            <p class="text-2xl font-bold text-green-700 mt-1">₺{{ number_format($currentIncome, 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">Bu ay toplam gelir</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-red-200 p-5">
            <p class="text-xs font-medium text-red-600 uppercase">Kayıtlı Maliyet</p>
            <p class="text-2xl font-bold text-red-700 mt-1">₺{{ number_format($currentExpenseTotal, 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">Personel + diğer giderler</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-blue-200 p-5">
            <p class="text-xs font-medium text-blue-600 uppercase">Kâr / Zarar</p>
            <p class="text-2xl font-bold {{ $currentProfit >= 0 ? 'text-blue-700' : 'text-red-700' }} mt-1">₺{{ number_format($currentProfit, 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">Net satış − kayıtlı maliyet</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-purple-200 p-5">
            <p class="text-xs font-medium text-purple-600 uppercase">Kâr Marjı</p>
            <p class="text-2xl font-bold text-purple-700 mt-1">%{{ number_format($marginPercent, 1) }}</p>
            <p class="text-xs text-gray-500 mt-1">Kâr / net satış</p>
        </div>
    </div>

    <!-- Maliyet Dağılımı -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-2">Bu Ay Maliyet Dağılımı</h2>
        <p class="text-sm text-gray-500 mb-6">Personel maaş giderleri ile diğer giderlerin (yedek parça, kira, fatura vb.) ayrımı</p>
        <div class="space-y-3">
            <div class="flex justify-between items-center p-4 bg-orange-50 rounded-lg">
                <span class="text-sm font-medium text-gray-700">Personel Maaşı</span>
                <span class="text-sm font-bold text-orange-600">₺{{ number_format($personnelCost, 2) }}</span>
            </div>
            <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-700">Diğer Giderler (Alış/Kira/Fatura/Vergi vb.)</span>
                <span class="text-sm font-bold text-gray-600">₺{{ number_format($otherCost, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- 6 Aylık Trend -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Son 6 Ay Trendi</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500">Ay</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500">Net Satış</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500">Maliyet</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500">Kâr/Zarar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($monthlyTrend as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-700">{{ ucfirst($row['label']) }}</td>
                            <td class="px-4 py-3 text-right text-green-600">₺{{ number_format($row['income'], 2) }}</td>
                            <td class="px-4 py-3 text-right text-red-600">₺{{ number_format($row['expense'], 2) }}</td>
                            <td class="px-4 py-3 text-right font-medium {{ $row['profit'] >= 0 ? 'text-blue-600' : 'text-red-700' }}">₺{{ number_format($row['profit'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
