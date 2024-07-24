<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class PaymentService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env("MOYASAR_SECRET_KEY");


    }

    public function createPayment($amount, $currency, $description, $source, $callbackUrl, $metadata)
    {

        try {
            $response = $this->client->post('https://api.moyasar.com/v1/payments', [

                'auth' => [$this->apiKey, ''],
                'json' => [
                    'amount' => $amount * 100, // Amount in cents
                    'currency' => $currency,
                    'description' => $description,
                    'source' => $source,
                    'callback_url' => $callbackUrl,
                    'metadata' => $metadata,
                ],
            ]);

            $body = json_decode($response->getBody(), true);

            return [
                'status' => $body['status'],
                'payment' => $body,
            ];
        } catch (RequestException $e) {
            $responseBody = $e->getResponse()->getBody()->getContents();
            $error = json_decode($responseBody, true);

            $errorMessage = isset($error['message']) ? $error['message'] : $e->getMessage();
            $errorDetails = isset($error['errors']) ? $error['errors'] : 'No additional details available.';

            return [
                'status' => 'failed',
                'error' => [
                    'message' => $errorMessage,
                    'details' => $errorDetails,
                ],
            ];
        }
    }
}
