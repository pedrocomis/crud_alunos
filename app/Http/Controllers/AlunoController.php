<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AlunoController extends Controller
{
    /**
     * Lista todos os alunos.
     */
    public function index(): JsonResponse
    {
        return response()->json(Aluno::orderBy('id')->get());
    }

    /**
     * Cadastra um aluno.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:alunos,email'],
        ], [
            'nome.required' => 'O campo nome é obrigatório.',
            'nome.string' => 'O nome deve ser um texto válido.',
            'nome.max' => 'O nome deve ter no máximo 100 caracteres.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'email.max' => 'O e-mail deve ter no máximo 255 caracteres.',
            'email.unique' => 'Este e-mail já está cadastrado para outro aluno.',
        ]);

        $aluno = Aluno::create($validated);

        return response()->json([
            'mensagem' => 'Aluno cadastrado com sucesso!',
            'aluno' => $aluno,
        ], 201);
    }

    /**
     * Retorna um aluno específico.
     */
    public function show(string $id): JsonResponse
    {
        $aluno = Aluno::find($id);

        if (! $aluno) {
            return response()->json([
                'mensagem' => 'Aluno não encontrado.',
            ], 404);
        }

        return response()->json($aluno);
    }

    /**
     * Atualiza um aluno específico.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $aluno = Aluno::find($id);

        if (! $aluno) {
            return response()->json([
                'mensagem' => 'Aluno não encontrado para atualização.',
            ], 404);
        }

        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:alunos,email,' . $aluno->id],
        ], [
            'nome.required' => 'O campo nome é obrigatório.',
            'nome.string' => 'O nome deve ser um texto válido.',
            'nome.max' => 'O nome deve ter no máximo 100 caracteres.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'email.max' => 'O e-mail deve ter no máximo 255 caracteres.',
            'email.unique' => 'Este e-mail já está cadastrado para outro aluno.',
        ]);

        $aluno->fill($validated);
        $aluno->save();

        return response()->json([
            'mensagem' => 'Aluno atualizado com sucesso!',
            'aluno' => $aluno,
        ]);
    }

    /**
     * Exclui um aluno específico.
     */
    public function destroy(string $id): JsonResponse
    {
        $aluno = Aluno::find($id);

        if (! $aluno) {
            return response()->json([
                'mensagem' => 'Aluno não encontrado para exclusão.',
            ], 404);
        }

        $aluno->delete();

        return response()->json([
            'mensagem' => 'Aluno excluído com sucesso!',
        ]);
    }
}