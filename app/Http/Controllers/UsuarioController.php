<?php

namespace App\Http\Controllers;

use App\Services\UsuarioServico;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    private $servico;

    public function __construct(UsuarioServico $usuarioServico)
    {
        $this->servico = $usuarioServico;
    }

    /**
     * Método de criar um usuário do controller
     * @method POST
     * 
     * @param String $type tipo de usuário se é administrativo ou se é aluno* 
     * @param Request $request JSON contendo os dados do novo usuário a ser cadastrado
     * 
     * @return JsonResponse
     */
    public function LoginUser(Request $request)
    {
        return $this->servico->LoginUser($request);
    }
}