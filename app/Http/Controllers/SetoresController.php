<?php

namespace App\Http\Controllers;

use App\Models\Setor;
use Illuminate\Http\Request;

class SetoresController extends Controller
{
    // Salvar novo setor
    public function salvarSetor(Request $request)
    {
        $request->validate([
            'nome_do_setor' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:255',
            'nome_responsavel' => 'nullable|string|max:255',
        ]);

        try {
            $setor = Setor::create($request->only('nome_do_setor', 'descricao', 'nome_responsavel'));
            return response()->json([
                'success' => true,
                'message' => "Setor cadastrado com sucesso! ID: {$setor->id}",
                'setor' => $setor
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Listar setores
    public function listarSetores()
    {
        return response()->json(Setor::orderBy('nome_do_setor')->get());
    }
}
