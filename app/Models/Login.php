<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Login extends Model
{
    protected $table = 'login';

    protected $fillable = [
        'usuario_id',
        'data_login',
        'token',
        'expira_em',
    ];

    protected $casts = [
        'data_login' => 'datetime',
        'expira_em' => 'datetime',
    ];

    // 🔹 Relacionamento
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}