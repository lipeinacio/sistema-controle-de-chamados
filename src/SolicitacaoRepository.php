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

    public function listar(): array
    {
        $sql = <<<'SQL'
            SELECT id, titulo, descricao, status, data_criacao
            FROM solicitacoes
            ORDER BY data_criacao DESC, id DESC
            SQL;

        return $this->conexao->query($sql)->fetchAll();
    }

    public function concluir(int $id): bool
    {
        $sql = <<<'SQL'
            UPDATE solicitacoes
            SET status = 'Concluido'
            WHERE id = :id AND status <> 'Concluido'
            SQL;

        $comando = $this->conexao->prepare($sql);
        $comando->execute(['id' => $id]);

        return $comando->rowCount() > 0;
    }

    public function cancelar(int $id): bool
    {
        $comando = $this->conexao->prepare('DELETE FROM solicitacoes WHERE id = :id');
        $comando->execute(['id' => $id]);

        return $comando->rowCount() > 0;
    }
}
