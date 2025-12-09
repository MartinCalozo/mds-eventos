<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\InvitationService;
use App\Models\InvitationRedemption;
use App\Http\Requests\RedeemRequest;
use App\Jobs\ProcessRedemption;

class InvitationRedemptionController extends Controller
{
    public function store(RedeemRequest $request, InvitationService $service)
    {
        $hash = $request->validated()['hash'];

        // Verificar si ya fue redimida
        if (InvitationRedemption::where('hash', $hash)->exists()) {
            Log::warning("Redemption attempt for already redeemed hash", [
                'hash' => $hash,
                'ip' => request()->ip(),
                'timestamp' => now(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Invitation already redeemed'
            ], 400);
        }

        // Consultar API externa
        $result = $service->getInvitationByHash($hash);

        if (!$result['success']) {
            Log::error("External API failed during redemption", [
                'hash' => $hash,
                'reason' => $result['message'] ?? 'Unknown',
                'ip' => request()->ip(),
                'timestamp' => now(),
            ]);

            return response()->json($result, 400);
        }

        $inv = $result['data'];

        // Enviar redención a la cola
        ProcessRedemption::dispatch($hash, $inv);

        return response()->json([
            'success' => true,
            'message' => 'Redemption is being processed'
        ], 202);
    }
}
