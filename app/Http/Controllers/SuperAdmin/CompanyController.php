<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::with(['users'])->withCount('users');

        // Arama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Durum filtresi
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'suspended') {
                $query->where('is_active', false);
            } elseif ($request->status === 'expired') {
                $query->where('subscription_end', '<', now());
            }
        }

        // Paket filtresi
        if ($request->filled('plan')) {
            $query->where('subscription_plan', $request->plan);
        }

        $companies = $query->latest()->paginate(15);

        // Aktif abonelik planlarını getir
        $subscriptionPlans = SubscriptionPlan::active()->ordered()->get();

        // İstatistikler
        $stats = [
            'active_companies' => Company::where('is_active', true)->count(),
            'expired_companies' => Company::where('subscription_end', '<', now())->count(),
            'suspended_companies' => Company::where('is_active', false)->count(),
            'monthly_revenue' => 0, // Payment tablosu mevcut olmadığı için 0 olarak ayarlandı
        ];

        return view('super-admin.companies.index', compact('companies', 'subscriptionPlans', 'stats'));
    }

    public function create()
    {
        // Aktif abonelik planlarını getir
        $subscriptionPlans = SubscriptionPlan::active()->ordered()->get();
        return view('super-admin.companies.create', compact('subscriptionPlans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:1000',
            'subscription_plan' => 'required|in:basic,orta,super',
            'monthly_fee' => 'required|numeric|min:0',
            'subscription_start' => 'required|date',
            'subscription_end' => 'required|date|after:subscription_start',
            'max_buildings' => 'required|integer|min:1',
            'max_employees' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',

            // Opsiyonel admin kullanıcı bilgileri (otomatik oluşturulacak)
            'admin_name' => 'nullable|string|max:255',
            'admin_email' => 'nullable|email|unique:users,email',
            'admin_password' => 'nullable|string|min:8|confirmed',
        ]);

        $company = Company::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'subscription_plan' => $validated['subscription_plan'],
            'subscription_status' => 'trial',
            'subscription_start' => $validated['subscription_start'],
            'subscription_end' => $validated['subscription_end'],
            'monthly_fee' => $validated['monthly_fee'],
            'max_buildings' => $validated['max_buildings'],
            'max_employees' => $validated['max_employees'],
            'is_active' => true,
            'notes' => $validated['notes'],
        ]);

        // Eğer manuel admin bilgileri verilmişse, otomatik oluşturulan admin'i güncelle
        if (!empty($validated['admin_name']) && !empty($validated['admin_email']) && !empty($validated['admin_password'])) {
            // Önce mevcut admin'i bul
            $admin = $company->users()->whereHas('userRoles.role', function($q) {
                $q->where('slug', 'company_admin');
            })->first();

            if ($admin) {
                // Admin'i güncelle
                $admin->update([
                    'name' => $validated['admin_name'],
                    'email' => $validated['admin_email'],
                    'password' => bcrypt($validated['admin_password']),
                ]);

                // Session'daki otomatik bilgileri güncelle
                session([
                    'new_company_admin_info' => [
                        'company_name' => $company->name,
                        'admin_email' => $validated['admin_email'],
                        'admin_password' => $validated['admin_password'],
                        'created_at' => now()
                    ]
                ]);
            }
        } else {
            // Manuel bilgiler verilmemişse, otomatik oluşturulan bilgileri session'a kaydet
            $admin = $company->users()->whereHas('userRoles.role', function($q) {
                $q->where('slug', 'company_admin');
            })->first();

            if ($admin) {
                $adminInfo = Company::getNewAdminInfo();
                $adminPassword = $adminInfo['admin_password'] ?? 'Otomatik oluşturuldu';

                session([
                    'new_company_admin_info' => [
                        'company_name' => $company->name,
                        'admin_email' => $admin->email,
                        'admin_password' => $adminPassword,
                        'created_at' => now()
                    ]
                ]);
            }
        }

        return redirect()->route('super-admin.companies.index')
            ->with('success', 'Firma başarıyla oluşturuldu. Admin giriş bilgileri aşağıda gösterilmektedir.');
    }

    public function show(Company $company)
    {
        $company->load(['users.userRoles.role', 'buildings', 'employees']);

        // İstatistikler
        $stats = [
            'total_buildings' => $company->buildings()->count(),
            'total_employees' => $company->employees()->count(),
            'total_users' => $company->users()->count(),
            'total_paid' => 0, // Payment tablosu mevcut olmadığı için 0
            'pending_payments' => 0, // Payment tablosu mevcut olmadığı için 0
            'overdue_payments' => 0, // Payment tablosu mevcut olmadığı için 0
        ];

        // Son 6 ay ödeme geçmişi (Payment tablosu mevcut olmadığı için boş array)
        $monthlyPayments = [0, 0, 0, 0, 0, 0];

        return view('super-admin.companies.show', compact('company', 'stats', 'monthlyPayments'));
    }

    public function edit(Company $company)
    {
        // Users ilişkisini eager loading ile yükle
        $company->load(['users.userRoles.role']);

        // Aktif abonelik planlarını getir
        $subscriptionPlans = SubscriptionPlan::active()->ordered()->get();
        return view('super-admin.companies.edit', compact('company', 'subscriptionPlans'));
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email,' . $company->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:1000',
            'subscription_plan' => 'required|in:basic,orta,super',
            'subscription_status' => 'required|in:active,suspended,cancelled,trial',
            'monthly_fee' => 'required|numeric|min:0',
            'subscription_start' => 'required|date',
            'subscription_end' => 'required|date|after:subscription_start',
            'max_buildings' => 'required|integer|min:1',
            'max_employees' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $company->update($validated);

        return redirect()->route('super-admin.companies.show', $company)
            ->with('success', 'Firma bilgileri güncellendi.');
    }

    public function destroy(Company $company)
    {
        // Firmaya ait tüm verileri sil
        $company->delete();

        return redirect()->route('super-admin.companies.index')
            ->with('success', 'Firma ve tüm verileri silindi.');
    }

    // Firmayı askıya alma
    public function suspend(Company $company)
    {
        $company->suspend();

        return redirect()->back()
            ->with('success', $company->name . ' firması askıya alındı.');
    }

    // Firmayı aktifleştirme
    public function activate(Company $company)
    {
        $company->activate();
        return redirect()->back()->with('success', 'Firma aktif hale getirildi.');
    }

    public function details(Company $company)
    {
        $company->load(['users.userRoles.role', 'buildings', 'employees', 'payments' => function($q) {
            $q->latest();
        }]);

        return view('super-admin.companies.details', compact('company'));
    }

    /**
     * Yeni oluşturulan admin bilgilerini session'dan temizle
     */
    public function clearAdminInfo()
    {
        Company::clearNewAdminInfo();
        return response()->json(['success' => true]);
    }
}
