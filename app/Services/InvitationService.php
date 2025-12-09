<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class InvitationService
{
    protected string $baseUrl;
    protected string $token;
    protected bool $useFallback;

    public function __construct()
    {
        $this->baseUrl    = config('services.invitations.url');
        $this->token      = config('services.invitations.token');
        $this->useFallback = config('services.invitations.fallback', false);
    }

    public function getInvitationByHash(string $hash): array
    {
        try {
            $response = Http::timeout(5)
                ->withToken($this->token)
                ->get("{$this->baseUrl}/{$hash}");

            if ($response->failed()) {
                $json = $response->json();

                if (isset($json['success']) && $json['success'] === false) {
                    return [
                        'success' => false,
                        'message' => $json['message'] ?? 'Invalid hash'
                    ];
                }

                return $this->fallbackOrError(
                    "External API returned error: " . $response->status(),
                    $response->status()
                );
            }
            $data = $response->json();

            if (isset($data['success']) && $data['success'] === false) {
                return [
                    'success' => false,
                    'message' => $data['message'] ?? 'Invalid invitation'
                ];
            }

            if (!$data || !isset($data['invitation_id'])) {
                return $this->fallbackOrError("Invalid invitation data received");
            }

            return [
                'success' => true,
                'data'    => $data,
            ];

        } catch (\Exception $e) {
            return $this->fallbackOrError("Connection error: " . $e->getMessage());
        }
    }

    private function fallbackOrError(string $error, int $status = 500): array
    {
        if ($this->useFallback) {
            return [
                'success' => true,
                'data' => [
                    'invitation_id' => 'fallback-id',
                    'event_name'    => 'Fake Event',
                    'event_date'    => now()->addDays(7)->toDateTimeString(),
                    'guest_count'   => 3,
                    'sector'        => 'VIP',
                ],
                'fallback' => true
            ];
        }

        // Si no hay fallback, se devuelve error real
        return [
            'success' => false,
            'error'   => $error,
            'status'  => $status,
        ];
    }
}
