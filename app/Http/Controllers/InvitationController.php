<?php

namespace App\Http\Controllers;

use App\Services\InvitationService;

class InvitationController extends Controller
{
    public function show(string $hash, InvitationService $service)
    {
        $result = $service->getInvitationByHash($hash);

        return response()->json($result);
    }
}
