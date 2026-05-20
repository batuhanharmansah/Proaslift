<?php

namespace App\Http\Controllers;

use App\Models\AccountType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountTypeController extends Controller
{
    public function index()
    {
        $accounts = AccountType::where('company_id', auth()->user()->company_id)
            ->where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        // Toplam bakiye hesaplama
        $totalBalance = $accounts->sum('current_balance');

        return view('account-types.index', compact('accounts', 'totalBalance'));
    }

    public function create()
    {
        return view('account-types.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:kasa,banka,nakit,pos',
            'initial_balance' => 'required|numeric|min:0',
            'account_number' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        $data['company_id'] = auth()->user()->company_id;
        $data['current_balance'] = $request->initial_balance;

        AccountType::create($data);

        return redirect()->route('account-types.index')->with('success', 'Hesap başarıyla oluşturuldu.');
    }

    public function edit(AccountType $accountType)
    {
        if ($accountType->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        return view('account-types.edit', compact('accountType'));
    }

    public function update(Request $request, AccountType $accountType)
    {
        if ($accountType->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:kasa,banka,nakit,pos',
            'initial_balance' => 'required|numeric|min:0',
            'account_number' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $accountType->update($request->all());

        return redirect()->route('account-types.index')->with('success', 'Hesap güncellendi.');
    }

    public function destroy(AccountType $accountType)
    {
        if ($accountType->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        // Hesapta para varsa silmeye izin verme
        if ($accountType->current_balance > 0) {
            return redirect()->back()->with('error', 'Hesapta para bulunduğu için silinemez.');
        }

        $accountType->update(['is_active' => false]);

        return redirect()->route('account-types.index')->with('success', 'Hesap pasif hale getirildi.');
    }
}
