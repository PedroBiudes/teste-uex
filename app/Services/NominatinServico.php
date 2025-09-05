<?php

namespace App\Services;

use GuzzleHttp\Client;

class NominatinServico
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://nominatim.openstreetmap.org/',
            'timeout'  => 5.0,
            'headers' => ['User-Agent' => 'UexApp/1.0 (pedrobiudes1@hotmail.com)'],
        ]);
    }

    /**
     * Busca a latitude e longitude a partir de um endereço.
     *
     * @param string $enderecoCompleto
     * @return array|null
     */
    public function getLatLong(string $enderecoCompleto): ?array
    {
        try {
            $response = $this->client->request('GET', "search", [
                'query' => [
                    'q' => $enderecoCompleto,
                    'format' => 'json',
                    'limit' => 1 
                ]
            ]);

            $body = json_decode($response->getBody(), true);

            if (!empty($body)) {
                $result = $body[0];
                
                return [
                    'latitude' => $result['lat'],
                    'longitude' => $result['lon'],
                ];
            }

            return null;

        } catch (\Exception $e) {
            return null;
        }
    }
}