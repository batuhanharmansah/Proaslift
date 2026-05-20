<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailedMaintenanceReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_schedule_id',
        'employee_id',
        'building_id',
        'form_number',
        'building_id_number',
        'maintenance_date',
        'entry_time',
        'exit_time',
        'capacity_person',
        'capacity_kg',
        'floor_count',
        'control_type',
        'machine_room_checks',
        'floor_checks',
        'cabin_checks',
        'shaft_checks',
        'maintenance_month',
        'description_warnings',
        'faulty_parts',
        'replaced_parts',
        'building_authority_signature',
        'building_authority_name',
        'building_authority_notes',
        'service_authority_signature',
        'service_authority_name',
        'completion_status',
        'general_notes',
        'photos',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'entry_time' => 'datetime:H:i',
        'exit_time' => 'datetime:H:i',
        'machine_room_checks' => 'array',
        'floor_checks' => 'array',
        'cabin_checks' => 'array',
        'shaft_checks' => 'array',
        'faulty_parts' => 'array',
        'replaced_parts' => 'array',
        'photos' => 'array',
        'building_authority_signature' => 'boolean',
        'service_authority_signature' => 'boolean',
    ];

    // İlişkiler
    public function maintenanceSchedule()
    {
        return $this->belongsTo(MaintenanceSchedule::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    // Accessor'lar
    public function getCompletionStatusLabelAttribute()
    {
        $statuses = [
            'tamamlandi' => 'Tamamlandı',
            'kismi_tamamlandi' => 'Kısmi Tamamlandı',
            'ertelendi' => 'Ertelendi'
        ];
        return $statuses[$this->completion_status] ?? $this->completion_status;
    }

    // Bakım checklist'leri için default değerler
    public static function getDefaultMachineRoomChecks()
    {
        return [
            'rope_inspection' => false, // Taşıyıcı halatların boy ve tel erimesi kontrolü
            'regulator_rope_inspection' => false, // Regülatör halatları boy ve tel erimesi kontrolü
            'machine_switch_level_check' => false, // Makina şalter ve seviye kontrolü
            'machine_brake_pad_check' => false, // Makina fren balata kontrolü
            'machine_bearing_check' => false, // Makina yatak ve rulman kontrolü
            'motor_coupling_check' => false, // Motor kaplin, şase, saplama ve kasnak ayarının kontrolü
            'machine_oil_levels' => false, // Makina tahrik grubu yağ seviyeleri
            'machine_brake_coil_check' => false, // Makina fren bobini kontrolü
            'machine_panel_fuse_check' => false, // Makina panosu sigorta ve kontaktör kontrolü
            'control_panel_fuse_check' => false, // Kumanda panosu sigorta ve kontaktör kontrolü
            'electrical_panel_leakage_check' => false, // Elektrik panosu 30mA kaçak akım rölesi kontrolü
            'machine_room_grounding_check' => false, // Makina dairesi topraklama ölçümü
            'machine_room_cleaning' => false, // Makina dairesi temizliği
        ];
    }

    public static function getDefaultFloorChecks()
    {
        return [
            'door_lock_safety_circuit' => false, // Kat kapı kilidi emniyet devreleri (130-140) kontrolü
            'door_automatic_device_check' => false, // Kapı otomatik cihazı görsel kontrol
            'door_spring_pulley_check' => false, // Kapı yaylı makara kontrolü
            'door_shock_absorber_check' => false, // Kapı amortisör ayarlaması kontrolü
            'door_spring_rope_wheel_check' => false, // Kapı yaylı-halat-tekeri kontrolü
            'door_lock_cleaning' => false, // Kapı kilitleri temizliği
            'door_hinges_check' => false, // Kapı menteşeleri kontrolü
        ];
    }

    public static function getDefaultCabinChecks()
    {
        return [
            'safety_circuits_120' => false, // Emniyet devreleri (120) kontrol
            'cabin_door_lock_safety' => false, // Kabin kapı kilidi emniyet devreleri (140) kontrol
            'floor_selector_check' => false, // Kat seçici klemens, somun, kopyala kontrol
            'regulator_rope_connection' => false, // Regülatör halat bağlantısı kontrol
            'revision_movement_check' => false, // Revizyon hareket kontrolü
            'level_switch_check' => false, // Seviye şalteri kontrol
            'limit_switch_817_818_check' => false, // 817-818 sınır kesici kontrol
            'emergency_lighting_alarm_check' => false, // Acil aydınlatma, alarm, diafon kontrol
            'emergency_stop_button_check' => false, // Kat seçici kabini iptal acil stop butonu kontrol (120)
            'level_alignment_check' => false, // Seviye hizaları kontrol
            'signs_warning_labels_check' => false, // Tablolar ve uyarı levhaları kontrol
            'floor_buttons_check' => false, // Kat butonları kontrol
            'cabin_lighting_check' => false, // Asansör içi aydınlatma kontrol
            'cabin_top_mechanical_brake_check' => false, // Kabin üstü mekanik fren çakılar kontrol
            'overload_control_check' => false, // Aşırı yük kontrol
        ];
    }

    public static function getDefaultShaftChecks()
    {
        return [
            'counterweight_rope_saddle_check' => false, // Karşı ağırlık halat şasesi, somun, kopyala kontrol
            'counterweight_paten_bolt_check' => false, // Karşı ağırlık paten ve civata kontrol
            'guide_rail_lubrication_check' => false, // Anaray ve ağırlık rayı yağlanması kontrol
            'shaft_bottom_regulator_pulley_check' => false, // Kuyu dibi regülatör kasnağı kontrol
            'shaft_bottom_regulator_pulley_cleaning' => false, // Kuyu dibi regülatör kasnağı temizliği
            'shaft_bottom_buffer_check' => false, // Kuyu dibi tampon kontrol
            'shaft_bottom_safety_circuits' => false, // Kuyu dibi emniyet devreleri (120) kontrol
            'shaft_bottom_cleaning' => false, // Kuyu dibi temizliği
        ];
    }

    // Form numarası otomatik oluşturma
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->form_number)) {
                $lastReport = self::orderBy('id', 'desc')->first();
                $lastNumber = $lastReport ? intval($lastReport->form_number) : 1874;
                $model->form_number = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}
