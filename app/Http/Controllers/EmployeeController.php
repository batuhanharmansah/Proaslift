<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Employee::with(['user', 'company'])->where('company_id', auth()->user()->company_id);

        // Arama filtresi
        if ($request->filled('search')) {
            $search = addcslashes($request->search, '%_\\');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        // Pozisyon filtresi
        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        // Durum filtresi
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $employees = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:employees,email',
            'address' => 'required|string',
            'position' => 'required|in:teknisyen,usta,muhendis,yonetici,muhasebe',
            'salary' => 'required|numeric|min:0',
            'hire_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Otomatik güvenli şifre üret; Observer User oluşturur, şifreyi burada kesin yazıyoruz
        $password = Str::random(12);
        $request->merge(['_employee_temp_password' => $password]);

        $employee = Employee::create(array_merge(
            $request->except('tc_no', '_employee_temp_password'),
            ['company_id' => auth()->user()->company_id]
        ));

        // Observer user_id atar; refresh ile DB'den alıp şifreyi kesin bu User'a yazıyoruz (plain → model hashed cast)
        $employee->refresh();
        $user = $employee->user_id ? User::find($employee->user_id) : null;
        if (!$user) {
            \Log::warning('Personel oluşturuldu ancak user_id eksik', ['employee_id' => $employee->id, 'email' => $employee->email]);
            $user = User::where('email', $employee->email)->where('company_id', $employee->company_id)->latest('id')->first();
        }
        if ($user) {
            $user->password = $password;
            $user->save();
        }

        // Giriş bilgilerini session'a kaydet (admin personeli bilgilendirsin diye)
        session([
            'new_employee_info' => [
                'employee_name' => $employee->full_name,
                'email' => $employee->email,
                'password' => $password,
                'position' => $employee->position_label,
                'created_at' => now()
            ]
        ]);

        return redirect()->route('employees.index')
            ->with('success', 'Personel başarıyla eklendi. Giriş bilgileri aşağıda gösterilmektedir.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'address' => 'required|string',
            'position' => 'required|in:teknisyen,usta,muhendis,yonetici,muhasebe',
            'salary' => 'required|numeric|min:0',
            'hire_date' => 'required|date',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $employee->update($request->except('tc_no'));

        return redirect()->route('employees.index')
            ->with('success', 'Personel bilgileri başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Personel başarıyla silindi.');
    }

    /**
     * Display employee profile.
     */
    public function profile(Employee $employee)
    {
        // Model bulunamadıysa 404 hatası döndür
        if (!$employee->exists) {
            abort(404, 'Çalışan bulunamadı.');
        }

        return view('employees.profile', compact('employee'));
    }

    /**
     * Update employee profile.
     */
    public function updateProfile(Request $request, Employee $employee)
    {
        // Model bulunamadıysa 404 hatası döndür
        if (!$employee->exists) {
            abort(404, 'Çalışan bulunamadı.');
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'address' => 'required|string',
            'position' => 'required|in:teknisyen,usta,muhendis,yonetici,muhasebe',
            'salary' => 'required|numeric|min:0',
            'hire_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $employee->update($request->only(['first_name', 'last_name', 'phone', 'email', 'address', 'position', 'salary', 'hire_date', 'notes']));

        return redirect()->route('employees.profile', $employee)->with('success', 'Çalışan bilgileri başarıyla güncellendi.');
    }

}
