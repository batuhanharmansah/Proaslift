<?php

namespace App\Http\Controllers;

use App\Models\NotificationPreference;
use Illuminate\Http\Request;

class NotificationPreferenceController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;

        $existing = NotificationPreference::where('company_id', $companyId)->get();

        $matrix = [];
        foreach (NotificationPreference::EVENTS as $eventKey => $label) {
            foreach (NotificationPreference::CHANNELS as $channel) {
                $pref = $existing->first(fn($p) => $p->event_key === $eventKey && $p->channel === $channel);
                $matrix[$eventKey][$channel] = $pref ? $pref->is_enabled : true;
            }
        }

        return view('settings.notification-preferences', [
            'matrix' => $matrix,
            'events' => NotificationPreference::EVENTS,
            'channels' => NotificationPreference::CHANNELS,
        ]);
    }

    public function update(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $enabled = $request->input('enabled', []); // ['event_key.channel' => '1']

        foreach (NotificationPreference::EVENTS as $eventKey => $label) {
            foreach (NotificationPreference::CHANNELS as $channel) {
                $isEnabled = $request->boolean("enabled.{$eventKey}.{$channel}");

                NotificationPreference::updateOrCreate(
                    ['company_id' => $companyId, 'event_key' => $eventKey, 'channel' => $channel],
                    ['is_enabled' => $isEnabled]
                );
            }
        }

        return back()->with('success', 'Bildirim tercihleri kaydedildi.');
    }
}
