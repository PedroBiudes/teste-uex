<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cadastros extends Model
{
    protected $table = 'cadastros';

    protected $fillable = [
        'id_usuario',
        'nome',
        'cpf',
        'telefone',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',
        'latitude',
        'longitude',
    ];

    // 🔹 Relacionamento
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}