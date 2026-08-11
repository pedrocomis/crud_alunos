<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlunoApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_crud_api_de_aluno(): void
    {
        $create = $this->postJson('/api/alunos', [
            'nome' => 'Maria Silva',
            'email' => 'maria@example.com',
        ]);

        $create->assertStatus(201)
            ->assertJsonPath('nome', 'Maria Silva')
            ->assertJsonPath('email', 'maria@example.com');

        $id = $create->json('id');

        $list = $this->getJson('/api/alunos');
        $list->assertOk()
            ->assertJsonFragment([
                'nome' => 'Maria Silva',
                'email' => 'maria@example.com',
            ]);

        $show = $this->getJson('/api/alunos/' . $id);
        $show->assertOk()
            ->assertJsonPath('nome', 'Maria Silva')
            ->assertJsonPath('email', 'maria@example.com');

        $update = $this->putJson('/api/alunos/' . $id, [
            'nome' => 'Maria Atualizada',
            'email' => 'maria.nova@example.com',
        ]);

        $update->assertOk()
            ->assertJsonPath('mensagem', 'Aluno atualizado com sucesso!');

        $delete = $this->deleteJson('/api/alunos/' . $id);
        $delete->assertOk()
            ->assertJsonPath('mensagem', 'Aluno excluído com sucesso!');
    }

    public function test_validacao_de_campos_obrigatorios(): void
    {
        $response = $this->postJson('/api/alunos', [
            'nome' => '',
            'email' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nome', 'email']);
    }

    public function test_email_invalido_e_duplicado(): void
    {
        $this->postJson('/api/alunos', [
            'nome' => 'Joao',
            'email' => 'joao@example.com',
        ])->assertStatus(201);

        $duplicado = $this->postJson('/api/alunos', [
            'nome' => 'Joao 2',
            'email' => 'joao@example.com',
        ]);

        $duplicado->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        $invalido = $this->postJson('/api/alunos', [
            'nome' => 'Joao 3',
            'email' => 'nao-e-email',
        ]);

        $invalido->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
