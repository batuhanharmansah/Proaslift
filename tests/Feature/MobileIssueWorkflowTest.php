<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Company;
use App\Models\Employee;
use App\Models\IssueReport;
use App\Models\MaintenanceSchedule;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MobileIssueWorkflowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('mobile_device_tokens');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('maintenance_reports');
        Schema::dropIfExists('maintenance_schedules');
        Schema::dropIfExists('issue_reports');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('buildings');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('companies');

        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('email')->unique();
            $table->boolean('is_active')->default(true);
            $table->string('subscription_plan')->nullable();
            $table->string('subscription_status')->nullable();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->json('dashboard_widgets')->nullable();
            $table->timestamps();
        });

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('role_id');
            $table->foreignId('company_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('position');
            $table->decimal('salary', 10, 2)->default(0);
            $table->date('hire_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable();
            $table->string('name');
            $table->text('address');
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->integer('floor_count')->default(1);
            $table->integer('elevator_count')->default(1);
            $table->string('elevator_type')->default('yolcu');
            $table->string('elevator_brand')->nullable();
            $table->string('elevator_model')->nullable();
            $table->integer('installation_year')->nullable();
            $table->string('contract_type')->default('bakim');
            $table->decimal('monthly_fee', 10, 2)->default(0);
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->string('status')->default('aktif');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('elevator_code')->nullable();
            $table->timestamps();
        });

        Schema::create('issue_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id');
            $table->foreignId('building_id');
            $table->string('reported_by');
            $table->string('issue_type');
            $table->string('priority');
            $table->text('description');
            $table->text('location_details')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('status')->default('bildirildi');
            $table->foreignId('assigned_employee_id')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('estimated_completion_time')->nullable();
            $table->timestamp('actual_completion_time')->nullable();
            $table->text('customer_notes')->nullable();
            $table->json('photos')->nullable();
            $table->boolean('is_urgent')->default(false);
            $table->boolean('requires_immediate_attention')->default(false);
            $table->timestamps();
        });

        Schema::create('maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable();
            $table->foreignId('issue_report_id')->nullable();
            $table->foreignId('building_id');
            $table->foreignId('assigned_employee_id')->nullable();
            $table->string('maintenance_type');
            $table->string('title')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->string('scheduled_time')->nullable();
            $table->string('priority')->default('normal');
            $table->string('status')->default('planli');
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->integer('estimated_duration')->nullable();
            $table->timestamps();
        });

        Schema::create('maintenance_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_schedule_id');
            $table->foreignId('employee_id')->nullable();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->text('work_description')->nullable();
            $table->json('used_products')->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->text('problems_found')->nullable();
            $table->text('recommendations')->nullable();
            $table->string('completion_status')->nullable();
            $table->boolean('customer_signature')->default(false);
            $table->string('customer_name')->nullable();
            $table->text('customer_notes')->nullable();
            $table->json('photos')->nullable();
            $table->json('routine_maintenance_checklist')->nullable();
            $table->integer('completion_percentage')->nullable();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('company_id');
            $table->string('type');
            $table->string('priority')->default('medium');
            $table->string('title');
            $table->text('body');
            $table->json('data')->nullable();
            $table->string('related_entity_type')->nullable();
            $table->unsignedBigInteger('related_entity_id')->nullable();
            $table->boolean('read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('mobile_device_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('company_id')->nullable();
            $table->string('token')->unique();
            $table->string('platform')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });
    }

    public function test_mobile_issue_creation_creates_maintenance_automatically(): void
    {
        [$admin, $building] = $this->createAdminAndBuilding();
        Sanctum::actingAs($admin, ['mobile:access']);

        $response = $this->postJson('/api/mobile/issues', [
            'building_id' => $building->id,
            'reported_by' => 'Test Admin',
            'issue_type' => 'mekanik_ariza',
            'priority' => 'orta',
            'description' => 'Kapı sensörü arızalı',
            'is_urgent' => false,
            'requires_immediate_attention' => false,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $issue = IssueReport::firstOrFail();
        $maintenance = MaintenanceSchedule::firstOrFail();

        $this->assertSame($issue->id, $maintenance->issue_report_id);
        $this->assertSame('inceleniyor', $issue->fresh()->status);
        $this->assertSame('planli', $maintenance->status);
    }

    public function test_mobile_issue_creation_with_employee_assigns_maintenance(): void
    {
        [$admin, $building, $company] = $this->createAdminAndBuilding();
        $employeeUser = $this->createUser($company, 'employee-user@example.com', null);
        $employee = Employee::withoutEvents(fn () => Employee::create([
            'company_id' => $company->id,
            'user_id' => $employeeUser->id,
            'first_name' => 'Saha',
            'last_name' => 'Teknisyen',
            'email' => $employeeUser->email,
            'position' => 'teknisyen',
            'salary' => 0,
            'is_active' => true,
        ]));

        Sanctum::actingAs($admin, ['mobile:access']);

        $response = $this->postJson('/api/mobile/issues', [
            'building_id' => $building->id,
            'reported_by' => 'Test Admin',
            'issue_type' => 'acil_durum',
            'priority' => 'acil',
            'description' => 'Asansör mahsur kalma ihbarı',
            'assigned_employee_id' => $employee->id,
            'is_urgent' => true,
            'requires_immediate_attention' => true,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.assigned_employee_id', $employee->id);

        $issue = IssueReport::firstOrFail();
        $maintenance = MaintenanceSchedule::firstOrFail();

        $this->assertSame($employee->id, $issue->assigned_employee_id);
        $this->assertSame($employee->id, $maintenance->assigned_employee_id);
        $this->assertSame('ekip_atandi', $issue->fresh()->status);
        $this->assertSame('atandi', $maintenance->status);
    }

    public function test_employee_with_null_company_id_can_open_assigned_maintenance_detail(): void
    {
        [$admin, $building, $company] = $this->createAdminAndBuilding();
        $employeeUser = $this->createUser(null, 'worker@example.com', 'employee');
        $employee = Employee::withoutEvents(fn () => Employee::create([
            'company_id' => $company->id,
            'user_id' => $employeeUser->id,
            'first_name' => 'Field',
            'last_name' => 'Worker',
            'email' => $employeeUser->email,
            'position' => 'teknisyen',
            'salary' => 0,
            'is_active' => true,
        ]));

        $maintenance = MaintenanceSchedule::create([
            'company_id' => $company->id,
            'building_id' => $building->id,
            'assigned_employee_id' => $employee->id,
            'maintenance_type' => 'ariza_onarim',
            'title' => 'Atanmış İş',
            'scheduled_date' => now()->toDateString(),
            'scheduled_time' => now()->format('H:i'),
            'priority' => 'normal',
            'status' => 'atandi',
            'description' => 'Test bakım kaydı',
            'estimated_duration' => 60,
        ]);

        Sanctum::actingAs($employeeUser, ['mobile:access']);

        $response = $this->getJson("/api/mobile/maintenance/{$maintenance->id}");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $maintenance->id);
    }

    /**
     * @return array{0: User, 1: Building, 2: Company}
     */
    private function createAdminAndBuilding(): array
    {
        $company = Company::withoutEvents(fn () => Company::create([
            'name' => 'Alpha Company',
            'slug' => 'alpha-company',
            'email' => 'alpha@example.com',
            'is_active' => true,
        ]));

        $admin = $this->createUser($company, 'admin@example.com', 'company_admin');

        $building = Building::create([
            'company_id' => $company->id,
            'name' => 'Merkez Plaza',
            'address' => 'Test Mahallesi No:1',
            'district' => 'Kadikoy',
            'city' => 'Istanbul',
            'status' => 'aktif',
        ]);

        return [$admin, $building, $company];
    }

    private function createUser(?Company $company, string $email, ?string $roleSlug = null): User
    {
        $user = User::create([
            'name' => $email,
            'email' => $email,
            'password' => bcrypt('password123'),
            'company_id' => $company?->id,
            'email_verified_at' => now(),
        ]);

        if ($roleSlug) {
            $role = Role::firstOrCreate(
                ['slug' => $roleSlug],
                ['name' => ucfirst(str_replace('_', ' ', $roleSlug))]
            );

            $user->userRoles()->create([
                'role_id' => $role->id,
                'company_id' => $company?->id,
                'is_active' => true,
            ]);
        }

        return $user;
    }
}
