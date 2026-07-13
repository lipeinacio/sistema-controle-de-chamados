<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/src/bootstrap.php';

$solicitacoes = [];
$erroCarregamento = null;

try {
    $repositorio = new SolicitacaoRepository(Database::conectar());
    $solicitacoes = $repositorio->listar();
} catch (PDOException $excecao) {
    error_log($excecao->getMessage());
    $erroCarregamento = 'Não foi possível carregar as solicitações. Tente novamente.';
}

$mensagem = obterMensagem();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitações | Chamados Internos</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header class="topo">
        <div class="container topo__conteudo">
            <a class="marca" href="/index.php">Chamados Internos</a>
            <nav aria-label="Navegação principal">
                <a class="link-navegacao" href="/index.php">Nova solicitação</a>
                <a class="link-navegacao link-navegacao--ativo" href="/solicitacoes.php">Ver solicitações</a>
            </nav>
        </div>
    </header>

    <main class="container conteudo">
        <section class="cartao">
            <div class="cabecalho-listagem">
                <div class="cartao__cabecalho">
                    <p class="rotulo">Acompanhamento</p>
                    <h1>Solicitações cadastradas</h1>
                    <p>Acompanhe, conclua ou cancele as solicitações internas.</p>
                </div>
                <a class="botao botao--primario" href="/index.php">Nova solicitação</a>
            </div>

            <?php if ($mensagem !== null): ?>
                <div class="alerta alerta--<?= escapar($mensagem['tipo']) ?>" role="status">
                    <?= escapar($mensagem['texto']) ?>
                </div>
            <?php endif; ?>

            <?php if ($erroCarregamento !== null): ?>
                <div class="alerta alerta--erro" role="alert">
                    <?= escapar($erroCarregamento) ?>
                </div>
            <?php elseif ($solicitacoes === []): ?>
                <div class="estado-vazio">
                    <h2>Nenhuma solicitação cadastrada</h2>
                    <p>Quando uma solicitação for registrada, ela aparecerá aqui.</p>
                    <a class="botao botao--secundario" href="/index.php">Cadastrar solicitação</a>
                </div>
            <?php else: ?>
                <div class="tabela-responsiva">
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Título</th>
                                <th scope="col">Descrição</th>
                                <th scope="col">Status</th>
                                <th scope="col">Data</th>
                                <th scope="col" class="coluna-acoes">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($solicitacoes as $solicitacao): ?>
                                <?php $concluida = $solicitacao['status'] === 'Concluido'; ?>
                                <tr>
                                    <td class="coluna-id">#<?= (int) $solicitacao['id'] ?></td>
                                    <td class="coluna-titulo"><?= escapar($solicitacao['titulo']) ?></td>
                                    <td class="coluna-descricao"><?= nl2br(escapar($solicitacao['descricao'])) ?></td>
                                    <td>
                                        <span class="status status--<?= $concluida ? 'concluido' : 'pendente' ?>">
                                            <?= $concluida ? 'Concluído' : 'Pendente' ?>
                                        </span>
                                    </td>
                                    <td class="coluna-data">
                                        <?= escapar((new DateTimeImmutable($solicitacao['data_criacao']))->format('d/m/Y H:i')) ?>
                                    </td>
                                    <td>
                                        <div class="acoes-registro">
                                            <form method="post" action="/processar-solicitacao.php">
                                                <input type="hidden" name="csrf_token" value="<?= escapar(criarTokenCsrf()) ?>">
                                                <input type="hidden" name="id" value="<?= (int) $solicitacao['id'] ?>">
                                                <input type="hidden" name="acao" value="concluir">
                                                <button
                                                    class="botao botao--sucesso botao--pequeno"
                                                    type="submit"
                                                    <?= $concluida ? 'disabled' : '' ?>
                                                    aria-label="Concluir solicitação <?= (int) $solicitacao['id'] ?>"
                                                >
                                                    <?= $concluida ? 'Concluído' : 'Finalizar' ?>
                                                </button>
                                            </form>

                                            <form
                                                method="post"
                                                action="/processar-solicitacao.php"
                                                data-confirmacao="Cancelar e excluir esta solicitação? Esta ação não poderá ser desfeita."
                                            >
                                                <input type="hidden" name="csrf_token" value="<?= escapar(criarTokenCsrf()) ?>">
                                                <input type="hidden" name="id" value="<?= (int) $solicitacao['id'] ?>">
                                                <input type="hidden" name="acao" value="cancelar">
                                                <button
                                                    class="botao botao--perigo botao--pequeno"
                                                    type="submit"
                                                    aria-label="Cancelar solicitação <?= (int) $solicitacao['id'] ?>"
                                                >
                                                    Cancelar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <script src="/assets/js/app.js"></script>
</body>
</html>
