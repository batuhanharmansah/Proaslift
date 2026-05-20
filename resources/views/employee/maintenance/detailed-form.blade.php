@extends('layouts.app')

@section('title', 'Detaylı Bakım Formu')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-clipboard-check me-2"></i>
                        ASANSÖR PERİYODİK BAKIM SERVİS FORMU
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('employee.maintenance.detailed-store', $schedule->id) }}" method="POST">
                        @csrf

                        <!-- Form Başlık Bilgileri -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label fw-bold">Form No:</label>
                                    <input type="text" class="form-control" value="Otomatik" readonly>
                                    <small class="text-muted">Form numarası otomatik oluşturulacak</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label fw-bold">ID No:</label>
                                    <input type="text" name="building_id_number" class="form-control"
                                           value="{{ old('building_id_number') }}" placeholder="3297687372">
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
                                    <label class="form-label fw-bold">Tarih: <span class="text-danger">*</span></label>
                                    <input type="date" name="maintenance_date" class="form-control"
                                           value="{{ old('maintenance_date', now()->format('Y-m-d')) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-bold">Giriş Saati:</label>
                                    <input type="time" name="entry_time" class="form-control"
                                           value="{{ old('entry_time') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-bold">Çıkış Saati:</label>
                                    <input type="time" name="exit_time" class="form-control"
                                           value="{{ old('exit_time') }}">
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
                                    <input type="number" name="capacity_person" class="form-control"
                                           value="{{ old('capacity_person') }}" placeholder="10">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Kapasite (kg):</label>
                                    <input type="number" name="capacity_kg" class="form-control"
                                           value="{{ old('capacity_kg') }}" placeholder="200">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Durak Sayısı:</label>
                                    <input type="number" name="floor_count" class="form-control"
                                           value="{{ old('floor_count') }}" placeholder="7">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Kumanda:</label>
                                    <input type="text" name="control_type" class="form-control"
                                           value="{{ old('control_type') }}" placeholder="Otomatik">
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
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="machine_room_checks[{{ $key }}]" value="1"
                                               id="machine_{{ $key }}"
                                               {{ old('machine_room_checks.' . $key) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="machine_{{ $key }}">
                                            {{ $label }}
                                        </label>
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
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="floor_checks[{{ $key }}]" value="1"
                                               id="floor_{{ $key }}"
                                               {{ old('floor_checks.' . $key) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="floor_{{ $key }}">
                                            {{ $label }}
                                        </label>
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
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="cabin_checks[{{ $key }}]" value="1"
                                               id="cabin_{{ $key }}"
                                               {{ old('cabin_checks.' . $key) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cabin_{{ $key }}">
                                            {{ $label }}
                                        </label>
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
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="shaft_checks[{{ $key }}]" value="1"
                                               id="shaft_{{ $key }}"
                                               {{ old('shaft_checks.' . $key) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="shaft_{{ $key }}">
                                            {{ $label }}
                                        </label>
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
                                    <select name="maintenance_month" class="form-select">
                                        <option value="">Seçiniz</option>
                                        <option value="OCAK" {{ old('maintenance_month') == 'OCAK' ? 'selected' : '' }}>OCAK</option>
                                        <option value="ŞUBAT" {{ old('maintenance_month') == 'ŞUBAT' ? 'selected' : '' }}>ŞUBAT</option>
                                        <option value="MART" {{ old('maintenance_month') == 'MART' ? 'selected' : '' }}>MART</option>
                                        <option value="NİSAN" {{ old('maintenance_month') == 'NİSAN' ? 'selected' : '' }}>NİSAN</option>
                                        <option value="MAYIS" {{ old('maintenance_month') == 'MAYIS' ? 'selected' : '' }}>MAYIS</option>
                                        <option value="HAZİRAN" {{ old('maintenance_month') == 'HAZİRAN' ? 'selected' : '' }}>HAZİRAN</option>
                                        <option value="TEMMUZ" {{ old('maintenance_month') == 'TEMMUZ' ? 'selected' : '' }}>TEMMUZ</option>
                                        <option value="AĞUSTOS" {{ old('maintenance_month') == 'AĞUSTOS' ? 'selected' : '' }}>AĞUSTOS</option>
                                        <option value="EYLÜL" {{ old('maintenance_month') == 'EYLÜL' ? 'selected' : '' }}>EYLÜL</option>
                                        <option value="EKİM" {{ old('maintenance_month') == 'EKİM' ? 'selected' : '' }}>EKİM</option>
                                        <option value="KASIM" {{ old('maintenance_month') == 'KASIM' ? 'selected' : '' }}>KASIM</option>
                                        <option value="ARALIK" {{ old('maintenance_month') == 'ARALIK' ? 'selected' : '' }}>ARALIK</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Tamamlanma Durumu: <span class="text-danger">*</span></label>
                                    <select name="completion_status" class="form-select" required>
                                        <option value="tamamlandi" {{ old('completion_status') == 'tamamlandi' ? 'selected' : '' }}>Tamamlandı</option>
                                        <option value="kismi_tamamlandi" {{ old('completion_status') == 'kismi_tamamlandi' ? 'selected' : '' }}>Kısmi Tamamlandı</option>
                                        <option value="ertelendi" {{ old('completion_status') == 'ertelendi' ? 'selected' : '' }}>Ertelendi</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Açıklama – Uyarılar:</label>
                                    <textarea name="description_warnings" class="form-control" rows="3"
                                              placeholder="Bakım sırasında tespit edilen uyarılar ve açıklamalar">{{ old('description_warnings') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Arızalı Parçalar:</label>
                                    <textarea name="faulty_parts[]" class="form-control" rows="3"
                                              placeholder="Arızalı parçaları listeleyin"></textarea>
                                    <small class="text-muted">Her satıra bir parça yazın</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Değiştirilen Parçalar:</label>
                                    <textarea name="replaced_parts[]" class="form-control" rows="3"
                                              placeholder="Değiştirilen parçaları listeleyin"></textarea>
                                    <small class="text-muted">Her satıra bir parça yazın</small>
                                </div>
                            </div>
                        </div>

                        <!-- İmzalar -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3">İmzalar ve Onaylar</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Bina Yetkilisi Adı:</label>
                                    <input type="text" name="building_authority_name" class="form-control"
                                           value="{{ old('building_authority_name') }}">
                                </div>
                                <div class="form-group mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="building_authority_signature" value="1"
                                               id="building_signature">
                                        <label class="form-check-label" for="building_signature">
                                            Bina Yetkilisi İmzası
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Bina Yetkilisi Notları:</label>
                                    <textarea name="building_authority_notes" class="form-control" rows="2"
                                              placeholder="Asansör çalışır teslim aldım.">{{ old('building_authority_notes') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Servis Yetkilisi Adı:</label>
                                    <input type="text" name="service_authority_name" class="form-control"
                                           value="{{ old('service_authority_name', auth()->user()->name) }}" readonly>
                                </div>
                                <div class="form-group mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="service_authority_signature" value="1"
                                               id="service_signature" checked>
                                        <label class="form-check-label" for="service_signature">
                                            Servis Yetkilisi İmzası
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Genel Notlar:</label>
                                    <textarea name="general_notes" class="form-control" rows="3"
                                              placeholder="Ek notlar ve açıklamalar">{{ old('general_notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Form Butonları -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('employee.maintenance.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i> Geri Dön
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Bakım Raporunu Kaydet
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Form validasyonu
    $('form').on('submit', function(e) {
        var completionStatus = $('select[name="completion_status"]').val();
        if (!completionStatus) {
            e.preventDefault();
            alert('Lütfen tamamlanma durumunu seçin.');
            return false;
        }
    });
});
</script>
@endsection
