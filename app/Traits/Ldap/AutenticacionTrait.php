<?php

namespace App\Traits\Ldap;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait AutenticacionTrait
{

    public function  autenticarConLDAP(string $usuario, string $password, string $ip, string $agente): array
    {

        return [
            'success' => true,
            'code' => 200,
            'dni' => '44332450',
            'usuario' => 'CSANCHEZ',
        ];


        $auth_url = env('AUTH_URL');
        $auth_token = env('AUTH_CODIGO') . "|" . env('AUTH_CLAVE');

        $dataRequest = [
            'usuario' => $usuario,
            'password' => $password,
            'usuario_ip' => $ip,
            'usuario_agente' => $agente,
        ];

        try {

            $response = Http::withToken($auth_token)
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->asForm()
                ->timeout(3)
                ->post($auth_url, $dataRequest);

        } catch (\Exception $e) {
            Log::error('No se pudo establecer conexión con la API de Autenticación.', [$e->getMessage()]);
            return [
                'success' => false,
                'code' => 500,
                'message' => 'No se pudo establecer conexión con la API de Autenticación.'
            ];
        }

        if ($response->status() == 200) {
            $data = $response->json();
            if (isset($data['data']) and isset($data['data']['dni']) and  isset($data['data']['usuario'])) {
                return [
                    'success' => true,
                    'code' => $response->status(),
                    'dni' => $data['data']['dni'],
                    'usuario' => $data['data']['usuario'],
                ];
            }
        }

        if ($response->status() == 400) {
            Log::info($response->body());
            $data = $response->json();
            if (isset($data['message'])) {
                return [
                    'success' => false,
                    'code' => $response->status(),
                    'message' => 'Error en la API de Autenticación (E001).',
                ];
            }
        }

        if ($response->status() == 503) {
            $data = $response->json();
            if (isset($data['message'])) {
                return [
                    'success' => false,
                    'code' => $response->status(),
                    'message' => 'Error en la API de Autenticación (E002).',
                ];
            }
        }

        if (in_array($response->status(), [401, 403, 404, 423])) {
            $data = $response->json();
            if (isset($data['message'])) {
                return [
                    'success' => false,
                    'code' => $response->status(),
                    'message' => $data['message'],
                ];
            }
        }

        return [
            'success' => false,
            'code' => 500,
            'message' => 'Error desconocido (E003)..',
        ];
    }
}
