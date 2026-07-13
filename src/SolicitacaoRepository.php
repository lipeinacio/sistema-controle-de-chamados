<?php

declare(strict_types=1);

final class SolicitacaoRepository
{
    public function __construct(private PDO $conexao)
    {
    }

    public function cadastrar(string $titulo, string $descricao): void
    {
        $sql = 'INSERT INTO solicitacoes (titulo, descricao) VALUES (:titulo, :descricao)';
        $comando = $this->conexao->prepare($sql);
        $comando->execute([
            'titulo' => $titulo,
            'descricao' => $descricao,
        ]);
    }
}
