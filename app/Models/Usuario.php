<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Usuario extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'documento',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    // 🔹 Relacionamentos
    public function logins()
    {
        return $this->hasMany(Login::class, 'usuario_id');
    }

    public function cadastros()
    {
        return $this->hasMany(Cadastro::class, 'usuario_id');
    }
}