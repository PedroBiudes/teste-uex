<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cadastro extends Model
{
    protected $table = 'cadastros';

    protected $fillable = [
        'usuario_id',
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
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}