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
     * @param String
     * @param Request $request JSON contendo os dados do novo usuário a ser cadastrado
     * 
     * @return JsonResponse
     */
    public function LoginUser(Request $request)
    {
        return $this->servico->LoginUser($request);
    }
    
    public function ChangePassword(Request $request)
    {
        return $this->servico->ChangePassword($request);
    }
    
    public function CreateUser(Request $request)
    {
        return $this->servico->CreateUser($request);
    }
    public function GetContacts(Request $request)
    {
        return $this->servico->GetContacts($request);
    }
    public function SaveContact(Request $request)
    {
        return $this->servico->SaveContact($request);
    }
    public function DeleteContact($id)
    {
        return $this->servico->DeleteContact($id);
    }
    public function DeleteUser($id)
    {
        return $this->servico->DeleteUser($id);
    }
}