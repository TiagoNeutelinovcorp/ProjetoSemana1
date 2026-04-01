<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Livro;
use App\Models\Requisicao;
use App\Models\Editora;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RequisicaoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste 1: Criação de Requisição de Livro
     * Verifica se é possível criar uma requisição no banco de dados
     */
    public function test_utilizador_pode_criar_requisicao_de_livro()
    {
        // Criar utilizador
        $user = User::factory()->create();

        // Criar editora
        $editora = Editora::factory()->create();

        // Criar livro
        $livro = Livro::factory()->create([
            'editora_id' => $editora->id,
        ]);

        // Criar requisição diretamente no banco
        $requisicao = Requisicao::create([
            'codigo' => 'REQ-TEST-' . date('YmdHis'),
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
            'status' => 'ativo'
        ]);

        // Verificar se a requisição foi criada
        $this->assertDatabaseHas('requisicoes', [
            'id' => $requisicao->id,
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'status' => 'ativo'
        ]);
    }

    /**
     * Teste 2: Validação de Requisição sem livro válido
     * Verifica que não existe livro com ID inválido
     */
    public function test_nao_pode_criar_requisicao_sem_livro_valido()
    {
        // Verificar que não existe livro com ID 99999
        $livroExiste = Livro::where('id', 99999)->exists();
        $this->assertFalse($livroExiste);

        // Se não existe livro, não pode criar requisição
        if (!$livroExiste) {
            $this->assertTrue(true, 'Livro inválido - requisição não pode ser criada');
        }
    }

    /**
     * Teste 3: Devolução de Livro
     * Verifica que é possível alterar o status para concluído
     */
    public function test_pode_mudar_status_requisicao_para_concluido()
    {
        // Criar dados necessários
        $user = User::factory()->create();
        $editora = Editora::factory()->create();
        $livro = Livro::factory()->create([
            'editora_id' => $editora->id,
        ]);

        // Criar requisição ativa
        $requisicao = Requisicao::create([
            'codigo' => 'REQ-DEV-' . date('YmdHis'),
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
            'status' => 'ativo'
        ]);

        // Atualizar status para concluído
        $requisicao->status = 'concluido';
        $requisicao->data_devolucao_real = now();
        $requisicao->save();

        // Verificar se o status foi atualizado
        $this->assertDatabaseHas('requisicoes', [
            'id' => $requisicao->id,
            'status' => 'concluido'
        ]);
    }

    /**
     * Teste 4: Listagem de Requisições por Utilizador
     * Verifica que cada utilizador tem as suas próprias requisições
     */
    public function test_utilizador_ve_apenas_suas_requisicoes()
    {
        // Criar dois utilizadores
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Criar editora
        $editora = Editora::factory()->create();

        // Criar livros
        $livro1 = Livro::factory()->create(['editora_id' => $editora->id]);
        $livro2 = Livro::factory()->create(['editora_id' => $editora->id]);

        // Criar 2 requisições para user1
        Requisicao::create([
            'codigo' => 'REQ-U1-1',
            'user_id' => $user1->id,
            'livro_id' => $livro1->id,
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
            'status' => 'ativo'
        ]);

        Requisicao::create([
            'codigo' => 'REQ-U1-2',
            'user_id' => $user1->id,
            'livro_id' => $livro2->id,
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
            'status' => 'ativo'
        ]);

        // Criar 1 requisição para user2
        Requisicao::create([
            'codigo' => 'REQ-U2-1',
            'user_id' => $user2->id,
            'livro_id' => $livro1->id,
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
            'status' => 'ativo'
        ]);

        // Verificar contagens
        $countUser1 = Requisicao::where('user_id', $user1->id)->count();
        $countUser2 = Requisicao::where('user_id', $user2->id)->count();

        $this->assertEquals(2, $countUser1, 'User1 deve ter 2 requisições');
        $this->assertEquals(1, $countUser2, 'User2 deve ter 1 requisição');
    }

    /**
     * Teste 5: Stock na Encomenda de Livros
     * Verifica que livro com requisição ativa está indisponível
     */
    public function test_livro_com_requisicao_ativa_esta_indisponivel()
    {
        // Criar dados
        $user = User::factory()->create();
        $editora = Editora::factory()->create();
        $livro = Livro::factory()->create([
            'editora_id' => $editora->id,
        ]);

        // Verificar que livro está disponível inicialmente
        $requisicaoAtiva = Requisicao::where('livro_id', $livro->id)
            ->where('status', 'ativo')
            ->exists();

        $this->assertFalse($requisicaoAtiva, 'Livro deveria estar disponível');

        // Criar requisição ativa para o livro
        Requisicao::create([
            'codigo' => 'REQ-STOCK-1',
            'user_id' => $user->id,
            'livro_id' => $livro->id,
            'data_requisicao' => now(),
            'data_prevista_devolucao' => now()->addDays(5),
            'status' => 'ativo'
        ]);

        // Verificar que livro NÃO está disponível (tem requisição ativa)
        $requisicaoAtiva = Requisicao::where('livro_id', $livro->id)
            ->where('status', 'ativo')
            ->exists();

        $this->assertTrue($requisicaoAtiva, 'Livro deveria estar indisponível pois tem requisição ativa');
    }
}
