<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
class AlunoController extends Controller
{
    // Lista todos os alunos
    public function index()
    {
        return Aluno::all();
    }

    // Cadastra um aluno
    public function store(Request $request)
    {
        $aluno = Aluno::create($request->all());

        return response()->json($aluno, 201);
    }

    // Exclui um aluno
    public function destroy($id)
    {
        $aluno = Aluno::findOrFail($id);
        $aluno->delete();

        return response()->json([
            'mensagem' => 'Aluno excluído com sucesso.'
        ]);
    }
}