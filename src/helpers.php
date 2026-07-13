<?php

declare(strict_types=1);

function escapar(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

function criarTokenCsrf(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function tokenCsrfValido(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function definirMensagem(string $tipo, string $texto): void
{
    $_SESSION['mensagem'] = ['tipo' => $tipo, 'texto' => $texto];
}

/**
 * @return array{tipo: string, texto: string}|null
 */
function obterMensagem(): ?array
{
    $mensagem = $_SESSION['mensagem'] ?? null;
    unset($_SESSION['mensagem']);

    return is_array($mensagem) ? $mensagem : null;
}
