# Sistema de Controle de Chamados Internos

Aplicação web para cadastrar e acompanhar solicitações internas. O projeto foi desenvolvido em PHP puro, sem frameworks, com persistência em PostgreSQL.

## Funcionalidades

- Cadastro de solicitações com título e descrição;
- validação dos campos no navegador e no servidor;
- listagem por ordem de criação;
- conclusão e cancelamento de solicitações;
- mensagens de retorno para todas as operações.

## Tecnologias

- PHP 8 com PDO;
- PostgreSQL;
- HTML, CSS e JavaScript;
- Docker e Docker Compose para o ambiente local.

## Como executar

É necessário ter Docker com o plugin Docker Compose instalado.

```bash
docker compose up -d --build
```

A aplicação ficará disponível em [http://localhost:8088](http://localhost:8088). O banco e a tabela são criados automaticamente na primeira execução.

Caso a porta `8088` esteja ocupada, escolha outra ao iniciar:

```bash
APP_PORT=8090 docker compose up -d
```

Para encerrar o ambiente:

```bash
docker compose down
```

## Teste de integração

Com os contêineres em execução, rode:

```bash
docker compose exec app php tests/integracao.php
```

O teste percorre o cadastro, a listagem, a conclusão e a exclusão dentro de uma transação, sem deixar registros no banco.

## Execução sem Docker

Também é possível executar o projeto com PHP 8 e PostgreSQL instalados na máquina. O PHP deve possuir o driver `pdo_pgsql`.

1. Crie um banco PostgreSQL e execute o arquivo `database/schema.sql`;
2. configure as variáveis `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` e `DB_PASSWORD`;
3. inicie o servidor apontando para a pasta pública:

```bash
php -S localhost:8088 -t public
```
