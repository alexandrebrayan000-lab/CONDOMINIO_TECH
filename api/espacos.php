```php
<?php

session_start();
require_once __DIR__ . '/../config/conexao.php';

/*
|--------------------------------------------------------------------------
| SEGURANÇA
|--------------------------------------------------------------------------
*/

// Apenas usuários logados como síndico podem gerenciar espaços
if (
    !isset($_SESSION['usuario_id']) ||
    ($_SESSION['usuario_perfil'] ?? '') !== 'sindico'
) {
    header("Location: ../index.php");
    exit;
}

// O usuário precisa estar vinculado a um condomínio
$condominio_id = $_SESSION['usuario_condominio_id'] ?? null;

if (!$condominio_id) {
    header("Location: ../pages/espacos.php?erro=condominio_nao_identificado");
    exit;
}

// Só aceita requisições POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/espacos.php");
    exit;
}

$acao = $_POST['acao'] ?? '';

/*
|--------------------------------------------------------------------------
| CADASTRAR ESPAÇO
|--------------------------------------------------------------------------
*/

if ($acao === 'cadastrar') {

    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $capacidade = $_POST['capacidade'] ?? null;

    // Checkbox marcado = ativo
    // Checkbox desmarcado = inativo
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    /*
    |--------------------------------------------------------------------------
    | VALIDAÇÃO DO NOME
    |--------------------------------------------------------------------------
    */

    if ($nome === '') {
        header("Location: ../pages/espacos.php?erro=nome_obrigatorio");
        exit;
    }

    if (mb_strlen($nome) > 100) {
        header("Location: ../pages/espacos.php?erro=nome_longo");
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDAÇÃO DA CAPACIDADE
    |--------------------------------------------------------------------------
    */

    if ($capacidade !== '' && $capacidade !== null) {

        $capacidade = filter_var(
            $capacidade,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1
                ]
            ]
        );

        if ($capacidade === false) {
            header("Location: ../pages/espacos.php?erro=capacidade_invalida");
            exit;
        }

    } else {

        $capacidade = null;
    }

    /*
    |--------------------------------------------------------------------------
    | VERIFICA SE JÁ EXISTE UM ESPAÇO COM O MESMO NOME
    |--------------------------------------------------------------------------
    */

    $stmt_existente = $pdo->prepare("
        SELECT id
        FROM espacos
        WHERE condominio_id = :condominio_id
        AND nome = :nome
        LIMIT 1
    ");

    $stmt_existente->execute([
        ':condominio_id' => $condominio_id,
        ':nome' => $nome
    ]);

    if ($stmt_existente->fetch()) {
        header("Location: ../pages/espacos.php?erro=espaco_existente");
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | CADASTRA O ESPAÇO
    |--------------------------------------------------------------------------
    */

    try {

        $sql = "
            INSERT INTO espacos
            (
                condominio_id,
                nome,
                descricao,
                capacidade,
                ativo
            )
            VALUES
            (
                :condominio_id,
                :nome,
                :descricao,
                :capacidade,
                :ativo
            )
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':condominio_id' => $condominio_id,
            ':nome'          => $nome,
            ':descricao'     => $descricao !== '' ? $descricao : null,
            ':capacidade'    => $capacidade,
            ':ativo'         => $ativo
        ]);

        header("Location: ../pages/espacos.php?sucesso=1");
        exit;

    } catch (PDOException $e) {

        header("Location: ../pages/espacos.php?erro=falha_cadastro");
        exit;
    }
}

/*
|--------------------------------------------------------------------------
| AÇÃO INVÁLIDA
|--------------------------------------------------------------------------
*/

header("Location: ../pages/espacos.php?erro=acao_invalida");
exit;
```
