<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * 🔔 ENTERPRISE MOBILE NOTIFICATION CONTROLLER
 * Profesyonel bildirim yönetimi - Liste, Okundu işaretleme, Filtreleme, Arama
 */
class NotificationController extends Controller
{
    use Concerns\ResolvesMobileCompanyId;

    /**
     * 📋 Get Notifications List
     */
    public function index(Request $request)
    {
        try {
            $userId = $request->user()->id;
            $companyId = $this->resolveMobileCompanyId($request);

            // Firma çözülemediyse boş liste dön
            if ($companyId === null) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'data' => [],
                        'current_page' => 1,
                        'last_page' => 1,
                        'per_page' => (int) min($request->get('per_page', 20), 100),
                        'total' => 0,
                        'from' => null,
                        'to' => null,
                    ],
                ]);
            }

            \Log::info('NotificationController: Fetching notifications', [
                'user_id' => $userId,
                'company_id' => $companyId,
                'user_email' => $request->user()->email,
            ]);

            $query = Notification::where('user_id', $userId)
                ->where('company_id', $companyId);

            // Filters
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            if ($request->filled('priority')) {
                $query->where('priority', $request->priority);
            }

            if ($request->filled('read')) {
                $query->where('read', $request->boolean('read'));
            }

            if ($request->filled('date_from')) {
                $query->where('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->where('created_at', '<=', $request->date_to);
            }

            // Search
            if ($request->filled('search')) {
                $search = addcslashes($request->search, '%_\\');
                $query->where(function($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                      ->orWhere('body', 'LIKE', "%{$search}%");
                });
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            $allowedSorts = ['created_at', 'priority', 'read', 'type'];
            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortOrder);
            } else {
                // Default: Okunmamış önce, sonra tarihe göre
                $query->orderByRaw('CASE WHEN `read` = 0 THEN 0 ELSE 1 END')
                      ->orderBy('created_at', 'desc');
            }

            // Pagination
            $perPage = min($request->get('per_page', 20), 100);
            $notifications = $query->paginate($perPage);

            \Log::info('NotificationController: Notifications found', [
                'total' => $notifications->total(),
                'count' => $notifications->count(),
                'current_page' => $notifications->currentPage(),
            ]);

            // Transform data
            $notifications->getCollection()->transform(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'type_label' => $notification->type_label,
                    'priority' => $notification->priority,
                    'priority_label' => $notification->priority_label,
                    'title' => $notification->title,
                    'body' => $notification->body,
                    'data' => $notification->data,
                    'related_entity_type' => $notification->related_entity_type,
                    'related_entity_id' => $notification->related_entity_id,
                    'read' => $notification->read,
                    'read_at' => $notification->read_at ? $notification->read_at->format('Y-m-d H:i:s') : null,
                    'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $notification->updated_at->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $notifications,
            ]);

        } catch (\Exception $e) {
            \Log::error('Get Notifications Error: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

            // Tablo yoksa veya DB hatası ise boş liste dön (canlıda migration atılmamış olabilir)
            if ($this->isTableOrSchemaException($e)) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'data' => [],
                        'current_page' => 1,
                        'last_page' => 1,
                        'per_page' => (int) min($request->get('per_page', 20), 100),
                        'total' => 0,
                        'from' => null,
                        'to' => null,
                    ],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Bildirimler yüklenemedi',
            ], 500);
        }
    }

    /**
     * 📊 Get Unread Count
     */
    public function unreadCount(Request $request)
    {
        try {
            $userId = $request->user()->id;
            $companyId = $this->resolveMobileCompanyId($request);

            // Firma çözülemediyse boş sayı dön
            if ($companyId === null) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'total_unread' => 0,
                        'by_type' => [],
                        'by_priority' => [],
                    ],
                ]);
            }

            $count = Notification::where('user_id', $userId)
                ->where('company_id', $companyId)
                ->where('read', false)
                ->count();

            // Kategori bazlı sayılar
            $countsByType = Notification::where('user_id', $userId)
                ->where('company_id', $companyId)
                ->where('read', false)
                ->select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray();

            // Öncelik bazlı sayılar
            $countsByPriority = Notification::where('user_id', $userId)
                ->where('company_id', $companyId)
                ->where('read', false)
                ->select('priority', DB::raw('count(*) as count'))
                ->groupBy('priority')
                ->pluck('count', 'priority')
                ->toArray();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_unread' => $count,
                    'by_type' => $countsByType,
                    'by_priority' => $countsByPriority,
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Get Unread Count Error: ' . $e->getMessage(), [
                'exception' => get_class($e),
            ]);

            // Tablo yoksa veya DB hatası ise sıfır sayı dön
            if ($this->isTableOrSchemaException($e)) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'total_unread' => 0,
                        'by_type' => [],
                        'by_priority' => [],
                    ],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Okunmamış sayısı alınamadı',
            ], 500);
        }
    }

    /**
     * Tablo/schema eksikliği (örn. notifications tablosu yok) hatası mı?
     */
    private function isTableOrSchemaException(\Throwable $e): bool
    {
        $msg = $e->getMessage();

        return str_contains($msg, "doesn't exist")
            || str_contains($msg, 'SQLSTATE[42S02]')
            || str_contains($msg, 'SQLSTATE[42S01]')
            || str_contains($msg, 'Base table or view not found');
    }

    /**
     * ✅ Mark Notification as Read
     */
    public function markAsRead(Request $request, $id)
    {
        try {
            $userId = $request->user()->id;
            $companyId = $this->resolveMobileCompanyId($request);

            if ($companyId === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Firma bilgisi bulunamadı',
                ], 403);
            }

            $notification = Notification::where('user_id', $userId)
                ->where('company_id', $companyId)
                ->findOrFail($id);

            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Bildirim okundu olarak işaretlendi',
                'data' => [
                    'id' => $notification->id,
                    'read' => $notification->read,
                    'read_at' => $notification->read_at->format('Y-m-d H:i:s'),
                ],
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bildirim bulunamadı',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Mark Notification as Read Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Bildirim güncellenemedi',
            ], 500);
        }
    }

    /**
     * ✅ Mark All Notifications as Read
     */
    public function markAllAsRead(Request $request)
    {
        try {
            $userId = $request->user()->id;
            $companyId = $this->resolveMobileCompanyId($request);

            if ($companyId === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Firma bilgisi bulunamadı',
                ], 403);
            }

            $updated = Notification::where('user_id', $userId)
                ->where('company_id', $companyId)
                ->where('read', false)
                ->update([
                    'read' => true,
                    'read_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => "{$updated} bildirim okundu olarak işaretlendi",
                'data' => [
                    'updated_count' => $updated,
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Mark All as Read Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Bildirimler güncellenemedi',
            ], 500);
        }
    }

    /**
     * ✅ Mark Notifications by Type as Read
     */
    public function markByTypeAsRead(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:maintenance,issue,financial,employee,system,general',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $userId = $request->user()->id;
            $companyId = $this->resolveMobileCompanyId($request);

            if ($companyId === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Firma bilgisi bulunamadı',
                ], 403);
            }

            $updated = Notification::where('user_id', $userId)
                ->where('company_id', $companyId)
                ->where('type', $request->type)
                ->where('read', false)
                ->update([
                    'read' => true,
                    'read_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => "{$updated} bildirim okundu olarak işaretlendi",
                'data' => [
                    'updated_count' => $updated,
                    'type' => $request->type,
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Mark By Type as Read Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Bildirimler güncellenemedi',
            ], 500);
        }
    }

    /**
     * 🗑️ Delete Notification
     */
    public function destroy(Request $request, $id)
    {
        try {
            $userId = $request->user()->id;
            $companyId = $this->resolveMobileCompanyId($request);

            if ($companyId === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Firma bilgisi bulunamadı',
                ], 403);
            }

            $notification = Notification::where('user_id', $userId)
                ->where('company_id', $companyId)
                ->findOrFail($id);

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Bildirim silindi',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bildirim bulunamadı',
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Delete Notification Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Bildirim silinemedi',
            ], 500);
        }
    }

    /**
     * 🗑️ Delete Read Notifications
     */
    public function deleteRead(Request $request)
    {
        try {
            $userId = $request->user()->id;
            $companyId = $this->resolveMobileCompanyId($request);

            if ($companyId === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Firma bilgisi bulunamadı',
                ], 403);
            }

            $deleted = Notification::where('user_id', $userId)
                ->where('company_id', $companyId)
                ->where('read', true)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => "{$deleted} okunmuş bildirim silindi",
                'data' => [
                    'deleted_count' => $deleted,
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Delete Read Notifications Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Bildirimler silinemedi',
            ], 500);
        }
    }

    /**
     * 🗑️ Delete Old Notifications (30+ days)
     */
    public function deleteOld(Request $request)
    {
        try {
            $userId = $request->user()->id;
            $companyId = $this->resolveMobileCompanyId($request);

            if ($companyId === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Firma bilgisi bulunamadı',
                ], 403);
            }

            $deleted = Notification::where('user_id', $userId)
                ->where('company_id', $companyId)
                ->where('created_at', '<', now()->subDays(30))
                ->delete();

            return response()->json([
                'success' => true,
                'message' => "{$deleted} eski bildirim silindi",
                'data' => [
                    'deleted_count' => $deleted,
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Delete Old Notifications Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Bildirimler silinemedi',
            ], 500);
        }
    }
}
