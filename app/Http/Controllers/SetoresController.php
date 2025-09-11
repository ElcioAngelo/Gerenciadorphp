<?php

namespace App\Http\Controllers;

use App\Models\Setores; // ⚠️ Certifique-se que o Model é 'Setor', singular
use Illuminate\Http\Request;

class SetoresController extends Controller
{
    // Salvar novo setor
    public function salvarSetor(Request $request)
    {
        // DEBUG temporário (apague depois)
        // dd($request->all());

        // Validação básica
        if (empty($request->nome_do_setor)) {
            return "❌ O campo 'nome_do_setor' é obrigatório.";
        }

        try {
            $setor = Setores::create([
                'nome_do_setor'   => $request->nome_do_setor,
                'descricao'       => $request->descricao ?? null,
                'nome_responsavel'=> $request->nome_responsavel ?? null,
            ]);

            return "✅ Setor cadastrado com sucesso! ID: {$setor->id}";
        } catch (\Exception $e) {
            return "❌ Erro ao salvar setor: " . $e->getMessage();
        }
    }

    // Listar setores
    public function listarSetores()
    {
        return Setores::orderBy('nome_do_setor')->get();
    }
}
