<?php

namespace App\Repositories;

use App\Models\Usuario;
use App\Models\Login;
use App\Repositories\Interfaces\UsuarioInterface;
use GuzzleHttp\RequestOptions;

//Camada de repositório, unica que deverá fazer buscas e alterações no banco de dados
class UsuarioRepository implements UsuarioInterface
{


    public function __construct(Usuario $user)
    {
    }
    public function GetUserByEmailLogin($email)
    {
        return Usuario::where('email', $email)->first();
    }   

    public function FindLastLoginForUser($userId)
    {
        return Login::where('usuario_id', $userId)->orderBy('created_at', 'desc')->first();
    }

    public function SaveLogin($login)
    {
        return $login->save();
    }
}
