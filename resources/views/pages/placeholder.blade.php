@extends('layouts.app')

@section('title', $title . ' - Harmanşah Yazılım')

@section('content')
<div class="p-6">
    <div class="max-w-2xl mx-auto text-center">
        <div class="bg-white rounded-2xl p-12 shadow-lg border border-gray-100">
            <div class="w-20 h-20 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            
            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $title }}</h1>
            <p class="text-gray-600 text-lg mb-8">{{ $message }}</p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('dashboard') }}" 
                   class="bg-primary-500 hover:bg-primary-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 transform hover:scale-105">
                    Ana Sayfaya Dön
                </a>
                <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-lg transition duration-200" disabled>
                    Yakında Aktif
                </button>
            </div>

            <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500">
                    <strong>Harmanşah Yazılım</strong> ile bu özellik yakında kullanıma sunulacak.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection