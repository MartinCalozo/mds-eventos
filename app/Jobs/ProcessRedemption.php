<?php

namespace App\Jobs;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\InvitationRedemption;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessRedemption implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $hash;
    public $inv;

    public function __construct($hash, $inv)
    {
        $this->hash = $hash;
        $this->inv = $inv;
    }

    public function handle(): void
    {
        Log::info("Processing redemption async", [
            'hash' => $this->hash,
            'timestamp' => now(),
        ]);

        // Crear o encontrar evento
        $event = Event::firstOrCreate([
            'name'   => $this->inv['event_name'],
            'date'   => $this->inv['event_date'],
            'sector' => $this->inv['sector'] ?? null
        ]);

        // Crear redención
        $red = InvitationRedemption::create([
            'invitation_id' => $this->inv['invitation_id'],
            'hash'          => $this->hash,
            'event_id'      => $event->id,
            'guest_count'   => $this->inv['guest_count']
        ]);

        // Crear tickets
        foreach (range(1, $this->inv['guest_count']) as $i) {
            Ticket::create([
                'invitation_redemption_id' => $red->id,
                'code'                     => Str::uuid(),
            ]);
        }

        Log::info("Async redemption completed", [
            'hash' => $this->hash,
            'tickets_created' => $this->inv['guest_count'],
            'timestamp' => now(),
        ]);
    }
}
