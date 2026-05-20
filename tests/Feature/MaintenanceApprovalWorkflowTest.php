<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\BuildingApprovalToken;
use App\Models\BuildingContact;
use App\Models\Company;
use App\Models\Employee;
use App\Models\MaintenanceReport;
use App\Models\MaintenanceSchedule;
use App\Models\SmsLog;
use App\Models\User;
use App\Services\MaintenanceApprovalService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MaintenanceApprovalWorkflowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('sms.driver', 'log');
        Config::set('sms.maintenance_approval.sms_cooldown_hours', 24);

        Schema::dropIfExists('sms_logs');
        Schema::dropIfExists('building_approval_tokens');
        Schema::dropIfExists('building_contacts');
        Schema::dropIfExists('maintenance_reports');
        Schema::dropIfExists('maintenance_schedules');
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
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
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
            $table->string('position')->default('teknisyen');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('status')->default('aktif');
            $table->timestamps();
        });

        Schema::create('building_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id');
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable();
            $table->foreignId('building_id');
            $table->foreignId('assigned_employee_id')->nullable();
            $table->string('maintenance_type')->default('rutin_bakim');
            $table->date('scheduled_date')->nullable();
            $table->string('priority')->default('normal');
            $table->string('status')->default('baslandi');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('maintenance_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable();
            $table->foreignId('building_id')->nullable();
            $table->foreignId('maintenance_schedule_id');
            $table->foreignId('employee_id')->nullable();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->text('work_description')->nullable();
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->string('completion_status')->nullable();
            $table->boolean('customer_signature')->default(false);
            $table->string('customer_name')->nullable();
            $table->enum('approval_status', ['onay_bekliyor', 'onaylandi'])->default('onaylandi');
            $table->string('approved_by_name')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('approval_ip', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('building_approval_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id');
            $table->foreignId('building_id');
            $table->string('token', 64)->unique();
            $table->timestamp('expires_at');
            $table->timestamp('last_sms_sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable();
            $table->foreignId('building_id')->nullable();
            $table->string('phone_masked');
            $table->string('message_type');
            $table->string('provider');
            $table->string('status');
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    public function test_store_report_marks_pending_approval_and_sends_sms(): void
    {
        [$user, $employee, $maintenance] = $this->seedAssignedMaintenance();

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/mobile/maintenance/{$maintenance->id}/store-report", [
            'start_time' => now()->subHour()->toIso8601String(),
            'end_time' => now()->toIso8601String(),
            'work_description' => 'Test bakim isi tamamlandi detay',
            'completion_status' => 'tamamlandi',
        ]);

        $response->assertOk()->assertJsonPath('data.approval_status', 'onay_bekliyor');

        $report = MaintenanceReport::first();
        $this->assertSame('onay_bekliyor', $report->approval_status);
        $this->assertDatabaseHas('building_approval_tokens', ['building_id' => $maintenance->building_id]);
        $this->assertDatabaseHas('sms_logs', [
            'building_id' => $maintenance->building_id,
            'message_type' => 'maintenance_approval',
            'status' => 'sent',
        ]);
    }

    public function test_public_form_approves_all_pending_reports(): void
    {
        [$user, $employee, $maintenance] = $this->seedAssignedMaintenance();

        $report = MaintenanceReport::create([
            'company_id' => $maintenance->company_id,
            'building_id' => $maintenance->building_id,
            'maintenance_schedule_id' => $maintenance->id,
            'employee_id' => $employee->id,
            'start_time' => now()->subHour(),
            'end_time' => now(),
            'work_description' => 'Tamamlanan is',
            'total_cost' => 100,
            'completion_status' => 'tamamlandi',
            'approval_status' => 'onay_bekliyor',
        ]);
        $maintenance->update(['status' => 'tamamlandi']);

        $token = BuildingApprovalToken::create([
            'company_id' => $maintenance->company_id,
            'building_id' => $maintenance->building_id,
            'token' => 'test-token-abc',
            'expires_at' => now()->addDays(7),
        ]);

        $this->get("/onay/{$token->token}")
            ->assertOk()
            ->assertSee('Tamamlanan is');

        $this->post("/onay/{$token->token}", [
            'approved_by_name' => 'Ahmet Yilmaz',
            'accept_terms' => '1',
        ])->assertOk()->assertSee('Onayınız Alındı');

        $report->refresh();
        $this->assertSame('onaylandi', $report->approval_status);
        $this->assertSame('Ahmet Yilmaz', $report->approved_by_name);
        $this->assertTrue($report->customer_signature);
    }

    public function test_expired_token_returns_410(): void
    {
        $company = Company::create(['name' => 'Test', 'slug' => 'test-co', 'email' => 'co@test.com']);
        $building = Building::create(['company_id' => $company->id, 'name' => 'Bina', 'address' => 'Adres']);

        $token = BuildingApprovalToken::create([
            'company_id' => $company->id,
            'building_id' => $building->id,
            'token' => 'expired-token',
            'expires_at' => now()->subDay(),
        ]);

        $this->get("/onay/{$token->token}")->assertStatus(410);
    }

    public function test_sms_cooldown_skips_second_send(): void
    {
        $company = Company::create(['name' => 'Test', 'slug' => 'test-co2', 'email' => 'co2@test.com']);
        $building = Building::create(['company_id' => $company->id, 'name' => 'Bina', 'address' => 'Adres']);
        BuildingContact::create([
            'building_id' => $building->id,
            'name' => 'Yonetici',
            'title' => 'yonetici',
            'phone' => '05551234567',
            'is_primary' => true,
            'is_active' => true,
        ]);

        MaintenanceReport::create([
            'company_id' => $company->id,
            'building_id' => $building->id,
            'maintenance_schedule_id' => MaintenanceSchedule::create([
                'company_id' => $company->id,
                'building_id' => $building->id,
                'maintenance_type' => 'rutin_bakim',
                'scheduled_date' => today(),
                'status' => 'tamamlandi',
            ])->id,
            'employee_id' => null,
            'start_time' => now(),
            'work_description' => 'Is',
            'completion_status' => 'tamamlandi',
            'approval_status' => 'onay_bekliyor',
        ]);

        $service = app(MaintenanceApprovalService::class);
        $first = $service->sendApprovalSms($building);
        $this->assertTrue($first['sent']);

        $second = $service->sendApprovalSms($building);
        $this->assertFalse($second['sent']);
        $this->assertSame('cooldown', $second['skipped_reason']);

        $this->assertSame(1, SmsLog::where('building_id', $building->id)->count());
    }

    /**
     * @return array{0: User, 1: Employee, 2: MaintenanceSchedule}
     */
    private function seedAssignedMaintenance(): array
    {
        $company = Company::create(['name' => 'Test Co', 'slug' => 'test-co-3', 'email' => 'co3@test.com']);
        $building = Building::create(['company_id' => $company->id, 'name' => 'Merkez Site', 'address' => 'Adres 1']);
        BuildingContact::create([
            'building_id' => $building->id,
            'name' => 'Site Yoneticisi',
            'title' => 'yonetici',
            'phone' => '05321234567',
            'is_primary' => true,
            'is_active' => true,
        ]);

        $user = User::create([
            'company_id' => $company->id,
            'name' => 'Teknisyen',
            'email' => 'tech@test.com',
            'password' => bcrypt('password'),
        ]);

        $employee = Employee::create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'first_name' => 'Ali',
            'last_name' => 'Veli',
            'email' => 'tech@test.com',
            'position' => 'teknisyen',
        ]);

        $maintenance = MaintenanceSchedule::create([
            'company_id' => $company->id,
            'building_id' => $building->id,
            'assigned_employee_id' => $employee->id,
            'maintenance_type' => 'ariza_onarim',
            'scheduled_date' => today(),
            'status' => 'baslandi',
            'description' => 'Test',
        ]);

        return [$user, $employee, $maintenance];
    }
}
