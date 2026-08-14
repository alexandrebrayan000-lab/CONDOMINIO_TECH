```php
<?php

session_start();
require_once __DIR__ . '/../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome        = trim($_POST['nome'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $bloco       = trim($_POST['bloco'] ?? '');
    $apartamento = trim($_POST['apartamento'] ?? '');
    $perfil      = trim($_POST['perfil'] ?? 'morador');
    $senha       = $_POST['senha'] ?? '';

    // Valida campos essenciais
    if (empty($nome) || empty($email) || empty($senha)) {
        header("Location: ../pages/cadastro.php?erro=campos_vazios");
        exit;
    }

    // Verifica se o e-mail já existe
    $stmt_check = $pdo->prepare("
        SELECT id
        FROM usuarios
        WHERE email = :email
        LIMIT 1
    ");

    $stmt_check->execute([
        ':email' => $email
    ]);

    if ($stmt_check->fetch()) {
        header("Location: ../pages/cadastro.php?erro=email_existente");
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | BUSCA O CONDOMÍNIO ATIVO
    |--------------------------------------------------------------------------
    */

    $stmt_condominio = $pdo->query("
        SELECT id
        FROM condominios
        WHERE ativo = 1
        ORDER BY id ASC
        LIMIT 1
    ");

    $condominio_id = $stmt_condominio->fetchColumn();

    if (!$condominio_id) {
        header("Location: ../pages/cadastro.php?erro=condominio_nao_encontrado");
        exit;
    }

    // Criptografa a senha
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    /*
    |--------------------------------------------------------------------------
    | CADASTRA O USUÁRIO
    |--------------------------------------------------------------------------
    */

    $sql = "
        INSERT INTO usuarios
        (
            condominio_id,
            nome,
            email,
            senha,
            perfil,
            bloco,
            apartamento
        )
        VALUES
        (
            :condominio_id,
            :nome,
            :email,
            :senha,
            :perfil,
            :bloco,
            :apartamento
        )
    ";

    $stmt_insert = $pdo->prepare($sql);

    $sucesso = $stmt_insert->execute([
        ':condominio_id' => $condominio_id,
        ':nome'          => $nome,
        ':email'         => $email,
        ':senha'         => $senhaHash,
        ':perfil'        => $perfil,
        ':bloco'         => $bloco !== '' ? $bloco : null,
        ':apartamento'   => $apartamento !== '' ? $apartamento : null
    ]);

    if ($sucesso) {
        header("Location: ../pages/cadastro.php?sucesso=1");
        exit;
    }

    header("Location: ../pages/cadastro.php?erro=falha_geral");
    exit;

} else {

    header("Location: ../pages/cadastro.php");
    exit;
}
```
