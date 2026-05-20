<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        // Payment tablosu mevcut olmadığı için boş collection
        $payments = collect([]);
        $companies = Company::active()->orderBy('name')->get();

        // Özet istatistikler (Payment tablosu mevcut olmadığı için 0)
        $stats = [
            'total_paid' => 0,
            'total_pending' => 0,
            'total_overdue' => 0,
            'this_month' => 0,
        ];

        return view('super-admin.payments.index', compact('payments', 'companies', 'stats'));
    }

    public function create()
    {
        $companies = Company::active()->orderBy('name')->get();
        return view('super-admin.payments.create', compact('companies'));
    }

    public function store(Request $request)
    {
        // Payment tablosu mevcut olmadığı için sadece başarı mesajı
        return redirect()->route('super-admin.payments.index')
            ->with('success', 'Ödeme sistemi henüz aktif değil.');
    }

    public function show($id)
    {
        // Payment tablosu mevcut olmadığı için 404
        abort(404, 'Ödeme sistemi henüz aktif değil.');
    }

    public function edit($id)
    {
        // Payment tablosu mevcut olmadığı için 404
        abort(404, 'Ödeme sistemi henüz aktif değil.');
    }

    public function update(Request $request, $id)
    {
        // Payment tablosu mevcut olmadığı için sadece başarı mesajı
        return redirect()->route('super-admin.payments.index')
            ->with('success', 'Ödeme sistemi henüz aktif değil.');
    }

    public function destroy($id)
    {
        // Payment tablosu mevcut olmadığı için sadece başarı mesajı
        return redirect()->route('super-admin.payments.index')
            ->with('success', 'Ödeme sistemi henüz aktif değil.');
    }
}
