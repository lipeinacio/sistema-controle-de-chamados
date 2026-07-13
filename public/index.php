<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/src/bootstrap.php';

$erros = [];
$titulo = '';
$descricao = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim((string) ($_POST['titulo'] ?? ''));
    $descricao = trim((string) ($_POST['descricao'] ?? ''));

    if (!tokenCsrfValido($_POST['csrf_token'] ?? null)) {
        $erros[] = 'Não foi possível validar o formulário. Atualize a página e tente novamente.';
    }

    if ($titulo === '') {
        $erros[] = 'Informe o título da solicitação.';
    } elseif (mb_strlen($titulo) > 150) {
        $erros[] = 'O título deve ter no máximo 150 caracteres.';
    }

    if ($descricao === '') {
        $erros[] = 'Informe a descrição da solicitação.';
    } elseif (mb_strlen($descricao) > 2000) {
        $erros[] = 'A descrição deve ter no máximo 2.000 caracteres.';
    }

    if ($erros === []) {
        try {
            $repositorio = new SolicitacaoRepository(Database::conectar());
            $repositorio->cadastrar($titulo, $descricao);

            definirMensagem('sucesso', 'Solicitação cadastrada com sucesso.');
            header('Location: /index.php', true, 303);
            exit;
        } catch (PDOException $excecao) {
            error_log($excecao->getMessage());
            $erros[] = 'Não foi possível cadastrar a solicitação. Tente novamente.';
        }
    }
}

$mensagem = obterMensagem();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova solicitação | Chamados Internos</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header class="topo">
        <div class="container topo__conteudo">
            <a class="marca" href="/index.php">Chamados Internos</a>
            <nav aria-label="Navegação principal">
                <a class="link-navegacao link-navegacao--ativo" href="/index.php">Nova solicitação</a>
                <a class="link-navegacao" href="/solicitacoes.php">Ver solicitações</a>
            </nav>
        </div>
    </header>

    <main class="container conteudo">
        <section class="cartao cartao--formulario">
            <div class="cartao__cabecalho">
                <p class="rotulo">Cadastro</p>
                <h1>Nova solicitação</h1>
                <p>Descreva o que você precisa para que a equipe responsável possa ajudar.</p>
            </div>

            <?php if ($mensagem !== null): ?>
                <div class="alerta alerta--<?= escapar($mensagem['tipo']) ?>" role="status">
                    <?= escapar($mensagem['texto']) ?>
                </div>
            <?php endif; ?>

            <?php if ($erros !== []): ?>
                <div class="alerta alerta--erro" role="alert">
                    <strong>Revise os dados informados:</strong>
                    <ul>
                        <?php foreach ($erros as $erro): ?>
                            <li><?= escapar($erro) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" novalidate data-form-solicitacao>
                <input type="hidden" name="csrf_token" value="<?= escapar(criarTokenCsrf()) ?>">

                <div class="campo">
                    <label for="titulo">Título</label>
                    <input
                        type="text"
                        id="titulo"
                        name="titulo"
                        maxlength="150"
                        value="<?= escapar($titulo) ?>"
                        placeholder="Ex.: Acesso ao sistema financeiro"
                        required
                        autofocus
                    >
                    <small>Use um título curto e objetivo.</small>
                </div>

                <div class="campo">
                    <label for="descricao">Descrição</label>
                    <textarea
                        id="descricao"
                        name="descricao"
                        rows="7"
                        maxlength="2000"
                        placeholder="Informe os detalhes da solicitação"
                        required
                    ><?= escapar($descricao) ?></textarea>
                    <small><span data-contador>0</span>/2000 caracteres</small>
                </div>

                <div class="acoes-formulario">
                    <a class="botao botao--secundario" href="/solicitacoes.php">Ver solicitações</a>
                    <button class="botao botao--primario" type="submit">Cadastrar</button>
                </div>
            </form>
        </section>
    </main>

    <script src="/assets/js/app.js"></script>
</body>
</html>
