<?php

namespace App\Repositories\Interfaces;

interface UsuarioInterface
{
    public function GetUserByEmailLogin($email);

    public function FindLastLoginForUser($userId);

    public function SaveLogin($login);
}
