<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setor extends Model
{
    protected $table = 'setores'; // ⚠️ Nome da tabela MariaDB
    protected $fillable = ['nome_do_setor', 'descricao', 'nome_responsavel'];
    public $timestamps = true;
}
