<?php

namespace App\Services;

use GuzzleHttp\Client;

class WhatsAppService
{
    protected $httpClient;
    protected $accessToken; // تعريف التوكن هنا

    protected $url; // تعريف التوكن هنا
    public function __construct()
    {
        $this->httpClient = new Client();
        $this->accessToken = env("WHATSAPP_API_TOKEN"); // استبدل بالتوكن الخاص بك
        $this->url = env("WHATSAPP_API_URL"); // استبدل بالتوكن الخاص بك
    }

    public function sendWhatsAppMessage($recipientNumber, $message)
    {


        $data = [
            'messaging_product' => 'whatsapp',
            'to' => $recipientNumber,
            'type' => 'template',
            'template' => [
                'namespace' => 'otp',
                'name' => 'otp',
                'language' => [
                    'code' => 'en',
                    'policy' => 'deterministic',
                ],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $message,
                            ],
                        ],
                    ],
                    [
                        'type' => 'button',
                        'sub_type' => 'url',
                        'index' => 0,
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $message,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        try {
            $response = $this->httpClient->post($this->url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $data,
            ]);

            // return $response->getBody()->getContents();

            $responseBody = json_decode($response->getBody(), true);

            if ($response->getStatusCode() == 200 && isset($responseBody['messages']) && !empty($responseBody['messages'])) {
                return true; // Success
            } else {
                return false; // Failed
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
