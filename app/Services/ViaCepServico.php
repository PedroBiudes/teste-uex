<?php

namespace App\Services;


use App\Repositories\Interfaces\UsuarioInterface;
use App\Helpers\Helpers;
use GuzzleHttp\Client;


class ViaCepServico
{
    protected $interface;
    protected $helpers;
    protected $client;

    public function __construct(UsuarioInterface $usuarioInterface, Helpers $helpers)
    {
        $this->interface = $usuarioInterface;
        $this->helpers = $helpers;
        $this->client = new Client([
            'base_uri' => 'https://viacep.com.br/ws/',
            'timeout'  => 2.0,
        ]);
    }

    public function GetAddressByCep($cep)
    {
        $cep = preg_replace('/[^0-9]/', '', $cep);

        if (strlen($cep) !== 8) {
            return null;
        }

        try {
            $response = $this->client->request('GET', "{$cep}/json/");
            $body = json_decode($response->getBody(), true);

            if (isset($body['erro']) && $body['erro'] === true) {
                return null;
            }

            return $body;

        } catch (\Exception $e) {
            return null;
        }
    }
}
