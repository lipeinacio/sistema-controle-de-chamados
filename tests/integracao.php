<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/src/bootstrap.php';

function verificar(bool $condicao, string $mensagem): void
{
    if (!$condicao) {
        throw new RuntimeException($mensagem);
    }
}

$conexao = Database::conectar();

try {
    $conexao->beginTransaction();
    $repositorio = new SolicitacaoRepository($conexao);

    $id = $repositorio->cadastrar(
        'Solicitação criada pelo teste',
        'Este registro será removido ao final do teste.'
    );

    $idsCadastrados = array_map(
        static fn (array $solicitacao): int => (int) $solicitacao['id'],
        $repositorio->listar()
    );
    verificar(in_array($id, $idsCadastrados, true), 'O cadastro não apareceu na listagem.');

    verificar($repositorio->concluir($id), 'Não foi possível concluir a solicitação.');

    $consultaStatus = $conexao->prepare('SELECT status FROM solicitacoes WHERE id = :id');
    $consultaStatus->execute(['id' => $id]);
    verificar($consultaStatus->fetchColumn() === 'Concluido', 'O status não foi atualizado.');

    verificar($repositorio->cancelar($id), 'Não foi possível cancelar a solicitação.');

    $consultaRegistro = $conexao->prepare('SELECT COUNT(*) FROM solicitacoes WHERE id = :id');
    $consultaRegistro->execute(['id' => $id]);
    verificar((int) $consultaRegistro->fetchColumn() === 0, 'O registro não foi excluído.');

    $conexao->rollBack();
    echo "Teste de integração concluído com sucesso.\n";
} catch (Throwable $erro) {
    if ($conexao->inTransaction()) {
        $conexao->rollBack();
    }

    fwrite(STDERR, "Falha no teste: {$erro->getMessage()}\n");
    exit(1);
}
