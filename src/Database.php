<?php

declare(strict_types=1);

final class Database
{
    private static ?PDO $conexao = null;

    public static function conectar(): PDO
    {
        if (self::$conexao instanceof PDO) {
            return self::$conexao;
        }

        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $port = getenv('DB_PORT') ?: '5432';
        $nome = getenv('DB_NAME') ?: 'chamados';
        $usuario = getenv('DB_USER') ?: 'chamados_user';
        $senha = getenv('DB_PASSWORD') ?: 'chamados_pass';

        $dsn = "pgsql:host={$host};port={$port};dbname={$nome}";

        self::$conexao = new PDO($dsn, $usuario, $senha, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        return self::$conexao;
    }
}
