<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/src/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Método não permitido.');
}

$token = $_POST['csrf_token'] ?? null;
$id = filter_var(
    $_POST['id'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);
$acao = (string) ($_POST['acao'] ?? '');

if (!tokenCsrfValido(is_string($token) ? $token : null)) {
    definirMensagem('erro', 'Não foi possível validar a ação. Tente novamente.');
} elseif ($id === false) {
    definirMensagem('erro', 'A solicitação informada é inválida.');
} elseif (!in_array($acao, ['concluir', 'cancelar'], true)) {
    definirMensagem('erro', 'A ação informada é inválida.');
} else {
    try {
        $repositorio = new SolicitacaoRepository(Database::conectar());

        if ($acao === 'concluir') {
            $alterou = $repositorio->concluir($id);
            $texto = $alterou
                ? 'Solicitação concluída com sucesso.'
                : 'A solicitação não foi encontrada ou já está concluída.';
        } else {
            $alterou = $repositorio->cancelar($id);
            $texto = $alterou
                ? 'Solicitação cancelada com sucesso.'
                : 'A solicitação não foi encontrada.';
        }

        definirMensagem($alterou ? 'sucesso' : 'erro', $texto);
    } catch (PDOException $excecao) {
        error_log($excecao->getMessage());
        definirMensagem('erro', 'Não foi possível atualizar a solicitação. Tente novamente.');
    }
}

header('Location: /solicitacoes.php', true, 303);
exit;
