<?php

namespace App\Service;

use Firebase\JWT\JWT;
use Psr\Container\ContainerExceptionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CentrifugoService
{

    public function __construct(
        private readonly ContainerBagInterface $params,
        private readonly HttpClientInterface   $httpClient,
    )
    {
    }

    public function generateConnectionToken(string $userEmail): string
    {
        $payload = [
            'sub' => $userEmail,
            'exp' => time() + 20 * 60,
        ];
        $secretKey = '';
        try {
            $secretKey = $this->params->get('centrifugo_client_token_key');
        } catch (\Exception|ContainerExceptionInterface) {
        }
        return JWT::encode($payload, $secretKey, 'HS256');
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function changeTemporarySeatStatus(int $showtimeId, bool $isReserved, int $seatId, string $userEmail): bool
    {
        try {
            $centrifugoUrl = $this->params->get('centrifugo_url');
            $centrifugoHttpApiKey = $this->params->get('centrifugo_httpapi_key');
            $this->httpClient->request(
                'POST',
                "http://$centrifugoUrl/api/publish",
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'X-API-Key' => $centrifugoHttpApiKey
                    ],
                    'json' => [
                        'channel' => "showtime_$showtimeId",
                        'data' => [
                            'isReserved' => $isReserved,
                            'seatId' => $seatId,
                            'userEmail' => $userEmail,
                            'timestamp' => time()
                        ]
                    ],
                ]
            );
            return true;
        } catch (\Exception|ContainerExceptionInterface) {
            return false;
        }
    }
}
