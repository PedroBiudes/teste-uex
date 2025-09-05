<?php

namespace App\Http\Controllers;

use App\Services\ViaCepServico;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViaCepController extends Controller
{
    private $servico;
    private $viaCepService;

    public function __construct(ViaCepServico $viaCepService)
    {
        $this->servico = $viaCepService;
    }

    public function GetAddressByCep($cep)
    {
        return $this->servico->GetAddressByCep($cep);
    }
}