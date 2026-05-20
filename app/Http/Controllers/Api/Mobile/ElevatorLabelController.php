<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\ElevatorLabel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

/**
 * 🏷️ MOBILE ELEVATOR LABELS API
 * Asansör etiket takibi - liste, detay, tamamla, mühürle, iptal, bina geçmişi
 */
class ElevatorLabelController extends Controller
{
    /**
     * Scope query to company via building
     */
    private function queryCompanyLabels($companyId)
    {
        return ElevatorLabel::whereHas('building', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        });
    }

    /**
     * 📋 List elevator labels
     */
    public function index(Request $request)
    {
        try {
            $companyId = $request->user()->company_id;

            $query = $this->queryCompanyLabels($companyId)
                ->with('building:id,name,address,district,city,elevator_code,operational_status');

            if ($request->filled('label_color')) {
                $query->where('label_color', $request->label_color);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('risk_level')) {
                switch ($request->risk_level) {
                    case 'overdue':
                        $query->overdue();
                        break;
                    case 'due_soon':
                        $query->dueSoon((int) $request->get('days', 30));
                        break;
                    case 'sealing_candidate':
                        $query->sealingCandidates();
                        break;
                }
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('building', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('elevator_code', 'like', "%{$search}%")
                      ->orWhere('address', 'like', "%{$search}%");
                });
            }

            $query->orderBy('due_date', 'asc')->orderBy('control_date', 'desc');
            $labels = $query->paginate($request->get('per_page', 20));

            $items = $labels->getCollection()->map(function ($label) {
                return $this->formatLabel($label);
            });

            return response()->json([
                'success' => true,
                'data' => $items,
                'pagination' => [
                    'current_page' => $labels->currentPage(),
                    'last_page' => $labels->lastPage(),
                    'per_page' => $labels->perPage(),
                    'total' => $labels->total(),
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Mobile ElevatorLabels Index Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Etiketler yüklenemedi'], 500);
        }
    }

    /**
     * 📊 Stats
     */
    public function stats(Request $request)
    {
        try {
            $companyId = $request->user()->company_id;
            $cacheKey = 'mobile_elevator_labels_stats_' . $companyId;

            $stats = Cache::remember($cacheKey, 300, function () use ($companyId) {
                $base = $this->queryCompanyLabels($companyId);

                return [
                    'total' => (clone $base)->count(),
                    'aktif' => (clone $base)->where('status', 'aktif')->count(),
                    'tamamlandi' => (clone $base)->where('status', 'tamamlandi')->count(),
                    'muhurlendi' => (clone $base)->where('status', 'muhurlendi')->count(),
                    'iptal' => (clone $base)->where('status', 'iptal')->count(),
                    'yesil' => (clone $base)->where('status', 'aktif')->where('label_color', 'yesil')->count(),
                    'mavi' => (clone $base)->where('status', 'aktif')->where('label_color', 'mavi')->count(),
                    'sari' => (clone $base)->where('status', 'aktif')->where('label_color', 'sari')->count(),
                    'kirmizi' => (clone $base)->where('status', 'aktif')->where('label_color', 'kirmizi')->count(),
                    'overdue' => (clone $base)->overdue()->count(),
                    'due_soon_30' => (clone $base)->dueSoon(30)->count(),
                    'sealing_candidates' => (clone $base)->sealingCandidates()->count(),
                ];
            });

            return response()->json(['success' => true, 'data' => $stats]);
        } catch (\Exception $e) {
            \Log::error('Mobile ElevatorLabels Stats Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'İstatistikler yüklenemedi'], 500);
        }
    }

    /**
     * 📄 Single label detail
     */
    public function show(Request $request, $id)
    {
        try {
            $companyId = $request->user()->company_id;

            $label = $this->queryCompanyLabels($companyId)
                ->with('building:id,name,address,district,city,elevator_code,capacity_person,capacity_kg,manufacturer,operational_status')
                ->findOrFail($id);

            return response()->json(['success' => true, 'data' => $this->formatLabel($label, true)]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Etiket bulunamadı'], 404);
        } catch (\Exception $e) {
            \Log::error('Mobile ElevatorLabel Show Error', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json(['success' => false, 'message' => 'Etiket yüklenemedi'], 500);
        }
    }

    /**
     * ✅ Mark as completed
     */
    public function complete(Request $request, $id)
    {
        try {
            $companyId = $request->user()->company_id;
            $label = $this->queryCompanyLabels($companyId)->where('status', 'aktif')->findOrFail($id);

            $label->markAsCompleted($request->input('description'), $request->user()->id);
            Cache::forget('mobile_elevator_labels_stats_' . $companyId);
            Cache::forget('elevator_labels_stats_' . $companyId);

            return response()->json([
                'success' => true,
                'message' => 'Etiket tamamlandı olarak işaretlendi',
                'data' => $this->formatLabel($label->fresh()),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Etiket bulunamadı veya zaten kapatılmış'], 404);
        } catch (\Exception $e) {
            \Log::error('Mobile ElevatorLabel Complete Error', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json(['success' => false, 'message' => 'İşlem başarısız'], 500);
        }
    }

    /**
     * 🔒 Seal elevator
     */
    public function seal(Request $request, $id)
    {
        try {
            $companyId = $request->user()->company_id;
            $label = $this->queryCompanyLabels($companyId)->where('status', 'aktif')->findOrFail($id);

            if (!$label->canBeSealed()) {
                return response()->json(['success' => false, 'message' => 'Bu etiket mühürlenemez (sadece kırmızı ve gecikmiş etiketler mühürlenebilir)'], 422);
            }

            $label->markAsSealed($request->input('description'), $request->user()->id);
            Cache::forget('mobile_elevator_labels_stats_' . $companyId);
            Cache::forget('elevator_labels_stats_' . $companyId);

            return response()->json([
                'success' => true,
                'message' => 'Asansör mühürlendi',
                'data' => $this->formatLabel($label->fresh()),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Etiket bulunamadı'], 404);
        } catch (\Exception $e) {
            \Log::error('Mobile ElevatorLabel Seal Error', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json(['success' => false, 'message' => 'İşlem başarısız'], 500);
        }
    }

    /**
     * ❌ Cancel label
     */
    public function cancel(Request $request, $id)
    {
        try {
            $companyId = $request->user()->company_id;
            $label = $this->queryCompanyLabels($companyId)->where('status', 'aktif')->findOrFail($id);

            $label->cancel($request->input('reason'), $request->user()->id);
            Cache::forget('mobile_elevator_labels_stats_' . $companyId);
            Cache::forget('elevator_labels_stats_' . $companyId);

            return response()->json([
                'success' => true,
                'message' => 'Etiket iptal edildi',
                'data' => $this->formatLabel($label->fresh()),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Etiket bulunamadı'], 404);
        } catch (\Exception $e) {
            \Log::error('Mobile ElevatorLabel Cancel Error', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json(['success' => false, 'message' => 'İşlem başarısız'], 500);
        }
    }

    /**
     * 📜 Building label history
     */
    public function buildingHistory(Request $request, $buildingId)
    {
        try {
            $companyId = $request->user()->company_id;

            $building = Building::where('company_id', $companyId)->findOrFail($buildingId);

            $labels = ElevatorLabel::where('building_id', $building->id)
                ->orderBy('control_date', 'desc')
                ->limit(50)
                ->get()
                ->map(fn ($label) => $this->formatLabel($label));

            return response()->json(['success' => true, 'data' => $labels]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Bina bulunamadı'], 404);
        } catch (\Exception $e) {
            \Log::error('Mobile ElevatorLabel BuildingHistory Error', ['error' => $e->getMessage(), 'building_id' => $buildingId]);
            return response()->json(['success' => false, 'message' => 'Geçmiş yüklenemedi'], 500);
        }
    }

    private function formatLabel(ElevatorLabel $label, $full = false): array
    {
        $building = $label->relationLoaded('building') ? $label->building : null;
        $data = [
            'id' => $label->id,
            'building_id' => $label->building_id,
            'label_color' => $label->label_color,
            'label_color_text' => $label->label_color_text,
            'control_date' => $label->control_date?->format('Y-m-d'),
            'follow_up_days' => $label->follow_up_days,
            'due_date' => $label->due_date?->format('Y-m-d'),
            'next_control_date' => $label->next_control_date?->format('Y-m-d'),
            'status' => $label->status,
            'status_text' => $label->status_text,
            'description' => $label->description,
            'source' => $label->source,
            'source_text' => $label->source_text,
            'is_sealing_candidate' => $label->is_sealing_candidate,
            'created_at' => $label->created_at->toIso8601String(),
            'updated_at' => $label->updated_at->toIso8601String(),
        ];

        if ($building) {
            $data['building'] = [
                'id' => $building->id,
                'name' => $building->name,
                'address' => $building->address,
                'district' => $building->district,
                'city' => $building->city,
                'elevator_code' => $building->elevator_code,
                'operational_status' => $building->operational_status ?? null,
            ];
            if ($full) {
                $data['building']['capacity_person'] = $building->capacity_person ?? null;
                $data['building']['capacity_kg'] = $building->capacity_kg ?? null;
                $data['building']['manufacturer'] = $building->manufacturer ?? null;
            }
        }

        if ($full) {
            $data['remaining_days'] = $label->getRemainingDays();
            $data['can_be_sealed'] = $label->canBeSealed();
        }

        return $data;
    }
}
