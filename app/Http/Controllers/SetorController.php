<?php

namespace App\Http\Controllers;

use App\Models\Setor;
use Illuminate\Http\Request;

class SetorController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nome_do_setor' => 'required|string|max:255',
        ]);

        $setor = Setor::create([
            'nome' => $request->nome_do_setor,
            'descricao' => $request->descricao,
            'responsavel' => $request->nome_responsavel,
        ]);

        return response()->json([
            'success' => true,
            'setor' => $setor
        ]);
    }

    public function index()
    {
        return response()->json(Setor::all());
    }
}
