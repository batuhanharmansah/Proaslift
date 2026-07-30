@extends('employee.layouts.app')

@section('title', 'Detaylı Bakım Raporu')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-clipboard-check me-2"></i>
                        ASANSÖR PERİYODİK BAKIM SERVİS FORMU
                    </h4>
                    @if($existingReport)
                        <div>
                            <span class="badge bg-success me-2">{{ $existingReport->completion_status_label }}</span>
                            <a href="{{ route('employee.maintenance.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Geri Dön
                            </a>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    @if($existingReport)
                        <!-- Form Bilgileri -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label fw-bold">Form No:</label>
                                    <input type="text" class="form-control" value="{{ $existingReport->form_number }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label fw-bold">ID No:</label>
                                    <input type="text" class="form-control" value="{{ $existingReport->building_id_number }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label fw-bold">Bina Adı:</label>
                                    <input type="text" class="form-control" value="{{ $schedule->building->name }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label fw-bold">Tarih:</label>
                                    <input type="text" class="form-control" value="{{ $existingReport->maintenance_date->format('d.m.Y') }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-bold">Giriş Saati:</label>
                                    <input type="text" class="form-control" value="{{ $existingReport->entry_time ? $existingReport->entry_time->format('H:i') : '-' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-bold">Çıkış Saati:</label>
                                    <input type="text" class="form-control" value="{{ $existingReport->exit_time ? $existingReport->exit_time->format('H:i') : '-' }}" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Asansör Bilgileri -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Asansör Bilgileri</h5>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Kapasite (Kişi):</label>
                                    <input type="text" class="form-control" value="{{ $existingReport->capacity_person ?? '-' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Kapasite (kg):</label>
                                    <input type="text" class="form-control" value="{{ $existingReport->capacity_kg ?? '-' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Durak Sayısı:</label>
                                    <input type="text" class="form-control" value="{{ $existingReport->floor_count ?? '-' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Kumanda:</label>
                                    <input type="text" class="form-control" value="{{ $existingReport->control_type ?? '-' }}" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Makine Dairesi Kontrolü -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Makine Dairesi Kontrolü</h5>
                            </div>
                            @php
                                $machineRoomChecks = [
                                    'rope_inspection' => 'Taşıyıcı halatların boy ve tel erimesi kontrolü',
                                    'regulator_rope_inspection' => 'Regülatör halatları boy ve tel erimesi kontrolü',
                                    'machine_switch_level_check' => 'Makina şalter ve seviye kontrolü',
                                    'machine_brake_pad_check' => 'Makina fren balata kontrolü',
                                    'machine_bearing_check' => 'Makina yatak ve rulman kontrolü',
                                    'motor_coupling_check' => 'Motor kaplin, şase, saplama ve kasnak ayarının kontrolü',
                                    'machine_oil_levels' => 'Makina tahrik grubu yağ seviyeleri',
                                    'machine_brake_coil_check' => 'Makina fren bobini kontrolü',
                                    'machine_panel_fuse_check' => 'Makina panosu sigorta ve kontaktör kontrolü',
                                    'control_panel_fuse_check' => 'Kumanda panosu sigorta ve kontaktör kontrolü',
                                    'electrical_panel_leakage_check' => 'Elektrik panosu 30mA kaçak akım rölesi kontrolü',
                                    'machine_room_grounding_check' => 'Makina dairesi topraklama ölçümü',
                                    'machine_room_cleaning' => 'Makina dairesi temizliği'
                                ];
                            @endphp
                            @foreach($machineRoomChecks as $key => $label)
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex align-items-center">
                                        <span class="me-2">
                                            @if(isset($existingReport->machine_room_checks[$key]) && $existingReport->machine_room_checks[$key])
                                                <i class="fas fa-check-circle text-success"></i>
                                            @else
                                                <i class="fas fa-times-circle text-danger"></i>
                                            @endif
                                        </span>
                                        <span class="{{ isset($existingReport->machine_room_checks[$key]) && $existingReport->machine_room_checks[$key] ? 'text-success' : 'text-danger' }}">
                                            {{ $label }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Katlar -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Katlar</h5>
                            </div>
                            @php
                                $floorChecks = [
                                    'door_lock_safety_circuit' => 'Kat kapı kilidi emniyet devreleri (130-140) kontrolü',
                                    'door_automatic_device_check' => 'Kapı otomatik cihazı görsel kontrol',
                                    'door_spring_pulley_check' => 'Kapı yaylı makara kontrolü',
                                    'door_shock_absorber_check' => 'Kapı amortisör ayarlaması kontrolü',
                                    'door_spring_rope_wheel_check' => 'Kapı yaylı-halat-tekeri kontrolü',
                                    'door_lock_cleaning' => 'Kapı kilitleri temizliği',
                                    'door_hinges_check' => 'Kapı menteşeleri kontrolü'
                                ];
                            @endphp
                            @foreach($floorChecks as $key => $label)
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex align-items-center">
                                        <span class="me-2">
                                            @if(isset($existingReport->floor_checks[$key]) && $existingReport->floor_checks[$key])
                                                <i class="fas fa-check-circle text-success"></i>
                                            @else
                                                <i class="fas fa-times-circle text-danger"></i>
                                            @endif
                                        </span>
                                        <span class="{{ isset($existingReport->floor_checks[$key]) && $existingReport->floor_checks[$key] ? 'text-success' : 'text-danger' }}">
                                            {{ $label }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Kabin İç ve Kabin Üstü Kontrolü -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Kabin İç ve Kabin Üstü Kontrolü</h5>
                            </div>
                            @php
                                $cabinChecks = [
                                    'safety_circuits_120' => 'Emniyet devreleri (120) kontrol',
                                    'cabin_door_lock_safety' => 'Kabin kapı kilidi emniyet devreleri (140) kontrol',
                                    'floor_selector_check' => 'Kat seçici klemens, somun, kopyala kontrol',
                                    'regulator_rope_connection' => 'Regülatör halat bağlantısı kontrol',
                                    'revision_movement_check' => 'Revizyon hareket kontrolü',
                                    'level_switch_check' => 'Seviye şalteri kontrol',
                                    'limit_switch_817_818_check' => '817-818 sınır kesici kontrol',
                                    'emergency_lighting_alarm_check' => 'Acil aydınlatma, alarm, diafon kontrol',
                                    'emergency_stop_button_check' => 'Kat seçici kabini iptal acil stop butonu kontrol (120)',
                                    'level_alignment_check' => 'Seviye hizaları kontrol',
                                    'signs_warning_labels_check' => 'Tablolar ve uyarı levhaları kontrol',
                                    'floor_buttons_check' => 'Kat butonları kontrol',
                                    'cabin_lighting_check' => 'Asansör içi aydınlatma kontrol',
                                    'cabin_top_mechanical_brake_check' => 'Kabin üstü mekanik fren çakılar kontrol',
                                    'overload_control_check' => 'Aşırı yük kontrol'
                                ];
                            @endphp
                            @foreach($cabinChecks as $key => $label)
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex align-items-center">
                                        <span class="me-2">
                                            @if(isset($existingReport->cabin_checks[$key]) && $existingReport->cabin_checks[$key])
                                                <i class="fas fa-check-circle text-success"></i>
                                            @else
                                                <i class="fas fa-times-circle text-danger"></i>
                                            @endif
                                        </span>
                                        <span class="{{ isset($existingReport->cabin_checks[$key]) && $existingReport->cabin_checks[$key] ? 'text-success' : 'text-danger' }}">
                                            {{ $label }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Kuyu İçi -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Kuyu İçi</h5>
                            </div>
                            @php
                                $shaftChecks = [
                                    'counterweight_rope_saddle_check' => 'Karşı ağırlık halat şasesi, somun, kopyala kontrol',
                                    'counterweight_paten_bolt_check' => 'Karşı ağırlık paten ve civata kontrol',
                                    'guide_rail_lubrication_check' => 'Anaray ve ağırlık rayı yağlanması kontrol',
                                    'shaft_bottom_regulator_pulley_check' => 'Kuyu dibi regülatör kasnağı kontrol',
                                    'shaft_bottom_regulator_pulley_cleaning' => 'Kuyu dibi regülatör kasnağı temizliği',
                                    'shaft_bottom_buffer_check' => 'Kuyu dibi tampon kontrol',
                                    'shaft_bottom_safety_circuits' => 'Kuyu dibi emniyet devreleri (120) kontrol',
                                    'shaft_bottom_cleaning' => 'Kuyu dibi temizliği'
                                ];
                            @endphp
                            @foreach($shaftChecks as $key => $label)
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex align-items-center">
                                        <span class="me-2">
                                            @if(isset($existingReport->shaft_checks[$key]) && $existingReport->shaft_checks[$key])
                                                <i class="fas fa-check-circle text-success"></i>
                                            @else
                                                <i class="fas fa-times-circle text-danger"></i>
                                            @endif
                                        </span>
                                        <span class="{{ isset($existingReport->shaft_checks[$key]) && $existingReport->shaft_checks[$key] ? 'text-success' : 'text-danger' }}">
                                            {{ $label }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Bakım Bilgileri -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">Bakım Bilgileri</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Bakım yapılan ay:</label>
                                    <input type="text" class="form-control" value="{{ $existingReport->maintenance_month ?? '-' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Tamamlanma Durumu:</label>
                                    <input type="text" class="form-control" value="{{ $existingReport->completion_status_label }}" readonly>
                                </div>
                            </div>
                        </div>

                        @if($existingReport->description_warnings)
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Açıklama – Uyarılar:</label>
                                    <textarea class="form-control" rows="3" readonly>{{ $existingReport->description_warnings }}</textarea>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($existingReport->faulty_parts && count($existingReport->faulty_parts) > 0)
                        <div class="row mb-4">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Arızalı Parçalar:</label>
                                    <ul class="list-group">
                                        @foreach($existingReport->faulty_parts as $part)
                                            @if($part)
                                                <li class="list-group-item">{{ $part }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Değiştirilen Parçalar:</label>
                                    <ul class="list-group">
                                        @foreach($existingReport->replaced_parts as $part)
                                            @if($part)
                                                <li class="list-group-item">{{ $part }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- İmzalar -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">İmzalar ve Onaylar</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Bina Yetkilisi:</label>
                                    <input type="text" class="form-control" value="{{ $existingReport->building_authority_name ?? '-' }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">İmza:</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" {{ $existingReport->building_authority_signature ? 'checked' : '' }} disabled>
                                        <label class="form-check-label">
                                            {{ $existingReport->building_authority_signature ? 'İmzalandı' : 'İmzalanmadı' }}
                                        </label>
                                    </div>
                                </div>
                                @if($existingReport->building_authority_notes)
                                <div class="form-group">
                                    <label class="form-label">Bina Yetkilisi Notları:</label>
                                    <textarea class="form-control" rows="2" readonly>{{ $existingReport->building_authority_notes }}</textarea>
                                </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Servis Yetkilisi:</label>
                                    <input type="text" class="form-control" value="{{ $existingReport->service_authority_name ?? '-' }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">İmza:</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" {{ $existingReport->service_authority_signature ? 'checked' : '' }} disabled>
                                        <label class="form-check-label">
                                            {{ $existingReport->service_authority_signature ? 'İmzalandı' : 'İmzalanmadı' }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($existingReport->general_notes)
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Genel Notlar:</label>
                                    <textarea class="form-control" rows="3" readonly>{{ $existingReport->general_notes }}</textarea>
                                </div>
                            </div>
                        </div>
                        @endif

                    @else
                        <!-- Form henüz oluşturulmamış -->
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-clipboard-list fa-4x text-muted"></i>
                            </div>
                            <h4 class="text-muted">Detaylı Bakım Formu Henüz Oluşturulmamış</h4>
                            <p class="text-muted">Bu bakım işi için detaylı bakım formu henüz doldurulmamış.</p>
                            <a href="{{ route('employee.maintenance.detailed-form', $schedule->id) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Detaylı Form Oluştur
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
