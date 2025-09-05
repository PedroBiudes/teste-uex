<?php

namespace App\Repositories\Interfaces;

interface UsuarioInterface
{
    public function GetUserByEmailLogin($email);

    public function FindLastLoginForUser($userId);

    public function SaveLogin($login);

    public function UpdatePassword($userId, $newPassword);

    public function CreateUser($user);

    public function BuscarContatos($userId, $search = null);

    public function BuscaContato($contatoCpf);

    public function SaveContact($contact);

    public function ApagarContato($id);

    public function ApagarUsuario($id);
}
