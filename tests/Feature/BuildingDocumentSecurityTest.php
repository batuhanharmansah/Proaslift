<?php

namespace Tests\Feature;

use App\Http\Middleware\CompanyActiveMiddleware;
use App\Http\Middleware\CompanyScopeMiddleware;
use App\Http\Middleware\RoleMiddleware;
use App\Models\AccountingEntry;
use App\Models\Building;
use App\Models\BuildingDocument;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BuildingDocumentSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('building_documents');
        Schema::dropIfExists('accounting_entries');
        Schema::dropIfExists('buildings');
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
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable();
            $table->string('name');
            $table->text('address');
            $table->string('district');
            $table->string('city');
            $table->integer('floor_count');
            $table->integer('elevator_count');
            $table->string('elevator_type');
            $table->string('elevator_brand')->nullable();
            $table->string('elevator_model')->nullable();
            $table->integer('installation_year')->nullable();
            $table->string('contract_type');
            $table->decimal('monthly_fee', 10, 2);
            $table->date('contract_start_date');
            $table->date('contract_end_date');
            $table->string('status')->default('aktif');
            $table->timestamps();
        });

        Schema::create('building_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id');
            $table->foreignId('building_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable();
            $table->integer('file_size')->nullable();
            $table->string('document_type')->default('diger');
            $table->string('payment_month')->nullable();
            $table->decimal('payment_amount', 10, 2)->nullable();
            $table->foreignId('uploaded_by');
            $table->timestamps();
        });

        Schema::create('accounting_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable();
            $table->foreignId('building_id')->nullable();
            $table->unsignedBigInteger('account_type_id')->nullable();
            $table->string('type');
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->date('transaction_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('status')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function test_company_user_cannot_upload_document_to_another_company_building(): void
    {
        Storage::fake('public');

        [$user] = $this->createCompanyUserWithBuilding('alpha');
        [, $otherBuilding] = $this->createCompanyUserWithBuilding('beta');

        $response = $this->withoutMiddleware([
                RoleMiddleware::class,
                CompanyActiveMiddleware::class,
                CompanyScopeMiddleware::class,
            ])
            ->actingAs($user)
            ->post(route('building.documents.store'), [
                'building_id' => $otherBuilding->id,
                'title' => 'Yetkisiz Belge',
                'description' => 'Bu kayıt oluşmamalı',
                'document_type' => 'diger',
                'file' => UploadedFile::fake()->create('test.pdf', 50, 'application/pdf'),
            ], [
                'Accept' => 'application/json',
            ]);

        $response->assertNotFound();
        $this->assertDatabaseCount('building_documents', 0);
    }

    public function test_company_user_cannot_mark_payment_for_another_company_building(): void
    {
        [$user] = $this->createCompanyUserWithBuilding('alpha');
        [, $otherBuilding] = $this->createCompanyUserWithBuilding('beta');

        $response = $this->withoutMiddleware([
                RoleMiddleware::class,
                CompanyActiveMiddleware::class,
                CompanyScopeMiddleware::class,
            ])
            ->actingAs($user)
            ->postJson(route('building.documents.mark-as-paid'), [
                'building_id' => $otherBuilding->id,
                'payment_year' => 2026,
                'payment_month' => 4,
                'payment_amount' => 2500,
            ]);

        $response->assertNotFound();
        $this->assertDatabaseCount('building_documents', 0);
        $this->assertDatabaseCount('accounting_entries', 0);
    }

    /**
     * @return array{0: User, 1: Building}
     */
    private function createCompanyUserWithBuilding(string $suffix): array
    {
        $company = Company::withoutEvents(function () use ($suffix) {
            return Company::create([
                'name' => "Company {$suffix}",
                'slug' => "company-{$suffix}",
                'email' => "{$suffix}@example.com",
                'is_active' => true,
            ]);
        });

        $user = User::create([
            'name' => "User {$suffix}",
            'email' => "user-{$suffix}@example.com",
            'password' => bcrypt('password'),
            'company_id' => $company->id,
            'email_verified_at' => now(),
        ]);

        $building = Building::create([
            'company_id' => $company->id,
            'name' => "Building {$suffix}",
            'address' => 'Ornek Mah. Test Sok. No:1',
            'district' => 'Kadikoy',
            'city' => 'Istanbul',
            'floor_count' => 10,
            'elevator_count' => 2,
            'elevator_type' => 'yolcu',
            'elevator_brand' => 'Test',
            'elevator_model' => 'Model X',
            'installation_year' => 2020,
            'contract_type' => 'bakim',
            'monthly_fee' => 1500,
            'contract_start_date' => '2026-01-01',
            'contract_end_date' => '2026-12-31',
            'status' => 'aktif',
        ]);

        return [$user, $building];
    }
}
