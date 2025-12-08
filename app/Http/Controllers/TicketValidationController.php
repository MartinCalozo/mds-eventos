<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TicketValidationLog;

class TicketValidationController extends Controller
{
    public function validateTicket(Request $request, string $code)
    {
        // Si no hay usuario autenticado → 401
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthenticated.'
            ], 401);
        }

        if (!is_string($code) || strlen($code) < 5 || strlen($code) > 100) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid ticket code'
            ], 400);
        }


        // Buscar ticket + relaciones
        $ticket = Ticket::where('code', $code)
            ->with('redemption.event')
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'error'   => 'Ticket not found'
            ], 404);
        }

        // Ticket ya usado → error
        if ($ticket->used) {
            return response()->json([
                'success' => false,
                'error' => 'Ticket already used',
                'validated_at' => $ticket->validated_at,
                'validated_by' => $ticket->validator?->name
            ], 400);
        }

        // Validar ticket
        $ticket->update([
            'used'         => true,
            'validated_by' => $request->user()->id,
            'validated_at' => now()
        ]);

        // Guardar log
        TicketValidationLog::create([
            'ticket_id'    => $ticket->id,
            'validated_by' => $request->user()->id,
            'result'       => true
        ]);

        return response()->json([
            'success'  => true,
            'access'   => 'granted',
            'ticket'   => $ticket->load('redemption'),
            'event_id' => $ticket->redemption->event_id
        ]);
    }
}
