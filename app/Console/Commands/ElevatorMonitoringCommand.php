<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ElevatorLabel;
use App\Models\ElevatorNotification;
use App\Models\ElevatorEscalation;
use App\Models\AuditLog;
use Carbon\Carbon;

class ElevatorMonitoringCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'elevator:monitor {--dry-run : Sadece kontrol et, bildirim gönderme}';

    /**
     * The console command description.
     */
    protected $description = 'Asansör etiketlerini izler, uyarılar ve eskalasyonlar oluşturur';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        $this->info('🔍 Asansör etiketleri izleme başlatıldı...');
        $this->info('📅 Tarih: ' . now()->format('d.m.Y H:i:s'));

        if ($dryRun) {
            $this->warn('🧪 DRY RUN modu - Bildirimler gönderilmeyecek');
        }

        $this->line('');

        // Aktif etiketleri al
        $activeLabels = ElevatorLabel::active()
            ->with(['building', 'notifications', 'escalations'])
            ->get();

        $this->info("📊 Toplam aktif etiket: {$activeLabels->count()}");
        $this->line('');

        $stats = [
            'warnings_sent' => 0,
            'overdue_notifications' => 0,
            'escalations_created' => 0,
            'sealing_candidates' => 0,
            'errors' => 0,
        ];

        foreach ($activeLabels as $label) {
            try {
                $this->processLabel($label, $dryRun, $stats);
            } catch (\Exception $e) {
                $stats['errors']++;
                $this->error("❌ Etiket #{$label->id} işlenirken hata: " . $e->getMessage());

                // Hata kaydı
                AuditLog::create([
                    'object_type' => 'ElevatorLabel',
                    'object_id' => $label->id,
                    'action' => 'monitoring_error',
                    'description' => 'Monitoring sırasında hata: ' . $e->getMessage(),
                    'service_name' => 'elevator_monitoring',
                    'metadata' => ['error' => $e->getMessage()],
                ]);
            }
        }

        $this->line('');
        $this->info('📈 İşlem Özeti:');
        $this->table(
            ['Metrik', 'Sayı'],
            [
                ['Uyarı bildirimleri', $stats['warnings_sent']],
                ['Gecikme bildirimleri', $stats['overdue_notifications']],
                ['Oluşturulan eskalasyonlar', $stats['escalations_created']],
                ['Mühürleme adayları', $stats['sealing_candidates']],
                ['Hatalar', $stats['errors']],
            ]
        );

        // Sistem audit log'u
        AuditLog::logSystemAction(
            'elevator_monitoring_completed',
            'Günlük asansör izleme tamamlandı',
            'elevator_monitoring',
            $stats
        );

        $this->info('✅ Asansör etiketleri izleme tamamlandı!');

        return Command::SUCCESS;
    }

    /**
     * Tek bir etiketi işle
     */
    private function processLabel($label, $dryRun, &$stats)
    {
        $remainingDays = $label->getRemainingDays();
        $building = $label->building;

        $this->line("🏢 {$building->name} ({$building->elevator_code}) - {$label->label_color_text}");
        $this->line("   📅 Son tarih: {$label->due_date->format('d.m.Y')} ({$remainingDays} gün)");

        // Kalan gün sayısına göre aksiyonlar
        if ($remainingDays < 0) {
            // Gecikmiş
            $this->processOverdueLabel($label, abs($remainingDays), $dryRun, $stats);
        } elseif (in_array($remainingDays, [30, 7, 1])) {
            // Uyarı eşikleri
            $this->processWarningLabel($label, $remainingDays, $dryRun, $stats);
        } elseif ($remainingDays == 0) {
            // Bugün son gün
            $this->processDueTodayLabel($label, $dryRun, $stats);
        }

        // Mühürleme adaylarını kontrol et
        if ($label->canBeSealed() && !$label->is_sealing_candidate) {
            $this->processSealingCandidate($label, $dryRun, $stats);
        }
    }

    /**
     * Uyarı bildirimi gönder
     */
    private function processWarningLabel($label, $remainingDays, $dryRun, &$stats)
    {
        // Bu eşik için daha önce bildirim gönderilmiş mi kontrol et
        $existingNotification = $label->notifications()
            ->where('notification_type', 'uyari')
            ->where('days_remaining', $remainingDays)
            ->where('status', 'gonderildi')
            ->exists();

        if ($existingNotification) {
            $this->line("   ⏭️  {$remainingDays} gün uyarısı daha önce gönderilmiş");
            return;
        }

        $this->warn("   ⚠️  {$remainingDays} gün uyarısı gönderiliyor...");

        if (!$dryRun) {
            $notification = ElevatorNotification::createWarningNotification($label, $remainingDays);
            $notification->send();
            $stats['warnings_sent']++;
        }
    }

    /**
     * Gecikme bildirimi gönder
     */
    private function processOverdueLabel($label, $daysOverdue, $dryRun, &$stats)
    {
        $this->error("   🚨 {$daysOverdue} gün gecikmiş!");

        // Gecikme bildirimi gönder
        $existingOverdueNotification = $label->notifications()
            ->where('notification_type', 'gecikme')
            ->where('days_remaining', -$daysOverdue)
            ->where('status', 'gonderildi')
            ->exists();

        if (!$existingOverdueNotification) {
            $this->warn("   📧 Gecikme bildirimi gönderiliyor...");

            if (!$dryRun) {
                $notification = ElevatorNotification::createOverdueNotification($label, $daysOverdue);
                $notification->send();
                $stats['overdue_notifications']++;
            }
        }

        // Eskalasyon kontrolü
        $this->processEscalation($label, $daysOverdue, $dryRun, $stats);
    }

    /**
     * Bugün son gün
     */
    private function processDueTodayLabel($label, $dryRun, &$stats)
    {
        $this->warn("   🎯 Bugün son gün!");

        // Son gün uyarısı gönder
        $this->processWarningLabel($label, 0, $dryRun, $stats);
    }

    /**
     * Eskalasyon işlemi
     */
    private function processEscalation($label, $daysOverdue, $dryRun, &$stats)
    {
        $escalationPolicies = ElevatorEscalation::ESCALATION_POLICIES;

        foreach ($escalationPolicies as $level => $policy) {
            $triggerDays = $policy['trigger_days'];

            if ($daysOverdue >= $triggerDays) {
                // Bu seviye için eskalasyon var mı kontrol et
                $existingEscalation = $label->escalations()
                    ->where('level', $level)
                    ->where('status', 'aktif')
                    ->exists();

                if (!$existingEscalation) {
                    $this->error("   🚨 Seviye {$level} eskalasyon tetikleniyor... ({$policy['description']})");

                    if (!$dryRun) {
                        ElevatorEscalation::createEscalation($label, $level, $daysOverdue);
                        $stats['escalations_created']++;
                    }
                }
            }
        }
    }

    /**
     * Mühürleme adayı işaretleme
     */
    private function processSealingCandidate($label, $dryRun, &$stats)
    {
        $this->error("   🔒 Mühürleme adayı olarak işaretleniyor...");

        if (!$dryRun) {
            $label->update(['is_sealing_candidate' => true]);

            // Mühürleme bildirimi gönder
            $building = $label->building;

            $notification = ElevatorNotification::create([
                'building_id' => $label->building_id,
                'elevator_label_id' => $label->id,
                'type' => 'eposta',
                'recipient' => $building->responsible_email,
                'recipient_name' => $building->responsible_person,
                'subject' => "[MÜHÜRLEME UYARISI] {$building->name} - {$building->elevator_code}",
                'content' => "Kırmızı etiketli {$building->elevator_code} için süre dolmuş ve mühürleme işlemi gereklidir. Lütfen acil aksiyonlar alınız.",
                'notification_type' => 'muhurlenme',
                'template_data' => [
                    'building_name' => $building->name,
                    'elevator_code' => $building->elevator_code,
                    'label_color' => $label->label_color,
                ],
            ]);

            $notification->send();
            $stats['sealing_candidates']++;
        }
    }
}
