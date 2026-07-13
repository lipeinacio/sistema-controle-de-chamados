<?php

declare(strict_types=1);

date_default_timezone_set('America/Sao_Paulo');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/SolicitacaoRepository.php';
require_once __DIR__ . '/helpers.php';
