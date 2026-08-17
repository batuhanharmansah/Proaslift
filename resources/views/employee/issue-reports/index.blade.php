@extends('employee.layouts.app')

@section('title', 'Arıza Bildirimlerim')
@section('page-title', 'Arıza Bildirimlerim')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Arıza Bildirimlerim</h1>
            <p class="text-gray-600">Bana atanmış arıza bildirimleri</p>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul role="list" class="divide-y divide-gray-200">
            @forelse($issueReports as $issueReport)
                <li class="px-6 py-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center
                                    {{ $issueReport->priority == 'acil' ? 'bg-red-100 text-red-600' :
                                       ($issueReport->priority == 'yuksek' ? 'bg-orange-100 text-orange-600' : 'bg-blue-100 text-blue-600') }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="flex items-center">
                                    <p class="text-sm font-medium text-gray-900">{{ $issueReport->building->name ?? '-' }}</p>
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $issueReport->priority_color }}">
                                        {{ $issueReport->priority_label }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600">{{ $issueReport->issue_type_label }}</p>
                                <div class="mt-1 text-sm text-gray-500">{{ Str::limit($issueReport->description, 100) }}</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $issueReport->status_color }}">
                                {{ $issueReport->status_label }}
                            </span>
                            <a href="{{ route('employee.issue-reports.show', $issueReport) }}"
                               class="text-primary-600 hover:text-primary-900 text-sm font-medium">
                                Görüntüle
                            </a>
                        </div>
                    </div>
                </li>
            @empty
                <li class="px-6 py-12">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Size atanmış arıza bildirimi yok</h3>
                        <p class="mt-1 text-sm text-gray-500">Yeni bir arıza size atandığında burada görünecek.</p>
                    </div>
                </li>
            @endforelse
        </ul>
    </div>

    @if($issueReports->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $issueReports->links() }}
        </div>
    @endif
</div>
@endsection
