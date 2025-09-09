<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setor extends Model
{
    protected $table = 'setor';

    protected $fillable = [
        'nome',
        'descricao',
        'responsavel',
    ];
}
