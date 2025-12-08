<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function ticketsUsed(Event $event, Request $request)
    {
        $date = $request->query('date');

        $sector = $request->query('sector');

        $query = $event->redemptions()
            ->with(['tickets' => function ($q) {
                $q->where('used', true)
                  ->with('validator:id,name')
                  ->orderBy('validated_at', 'desc');
            }])
            ->when($date, function ($q) use ($date) {
                $q->whereDate('created_at', $date);
            })
            ->when($sector, function ($q) use ($sector) {
                $q->whereHas('event', function ($ev) use ($sector) {
                    $ev->where('sector', $sector);
                });
            })
            ->paginate(10);

        return response()->json([
            'success' => true,
            'event'   => $event,
            'data'    => $query,
        ]);
    }
    public function redemptions(Request $request)
    {
        $eventId = $request->query('event');
        $date    = $request->query('date');
        $sector  = $request->query('sector');

        $query = \App\Models\InvitationRedemption::query()
            ->with([
                'event:id,name,date,sector',
                'tickets' => function ($q) {
                    $q->select('id', 'invitation_redemption_id', 'code', 'used', 'validated_at', 'validated_by')
                    ->with('validator:id,name')
                    ->orderBy('id');
                }
            ])
            ->when($eventId, function ($q) use ($eventId) {
                $q->where('event_id', $eventId);
            })
            ->when($date, function ($q) use ($date) {
                $q->whereDate('created_at', $date);
            })
            ->when($sector, function ($q) use ($sector) {
                $q->whereHas('event', function ($e) use ($sector) {
                    $e->where('sector', $sector);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'filters' => [
                'event'  => $eventId,
                'date'   => $date,
                'sector' => $sector
            ],
            'data' => $query
        ]);
    }

}
