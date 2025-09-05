<?php

namespace App\Repositories;

use App\Models\Usuario;
use App\Models\Login;
use App\Models\Cadastros;
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
    public function UpdatePassword($userId, $newPassword)
    {
        return Usuario::where('id', $userId)->update(['password' => $newPassword]);
    }
    public function CreateUser($user)
    {
        return $user->save();
    }
    public function BuscarContatos($userId, $search = null)
    {
        if($search != null){
            return Cadastros::where('id_usuario', $userId)
            ->where(function($query) use ($search) {
                $query->where('nome', 'like', '%' . $search . '%')
                      ->orWhere('cpf', 'like', '%' . $search . '%');
            })->get();
        }   
        return Cadastros::where('id_usuario', $userId)->get();
    }
    public function BuscaContato($contatoCpf)
    {
        return Cadastros::where('cpf', $contatoCpf)->first();
    }
    public function SaveContact($contact)
    {
        return $contact->save();
    }
    public function ApagarContato($id)
    {
        return Cadastros::where('id', $id)->delete();
    }
    public function ApagarUsuario($id)
    {
        return Usuario::where('id', $id)->delete();
    }
}
