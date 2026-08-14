```php
<?php

session_start();
require_once __DIR__ . '/../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        header("Location: ../pages/login.php?erro=1");
        exit;
    }

    // Busca o usuário no banco, incluindo o condomínio ao qual pertence
    $stmt = $pdo->prepare("
        SELECT
            id,
            condominio_id,
            nome,
            email,
            senha,
            perfil,
            bloco,
            apartamento
        FROM usuarios
        WHERE email = :email
        AND ativo = 1
        LIMIT 1
    ");

    $stmt->execute([
        ':email' => $email
    ]);

    $usuario = $stmt->fetch();

    // Valida a senha
    if ($usuario && password_verify($senha, $usuario['senha'])) {

        // Guarda os dados do usuário na sessão
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_condominio_id'] = $usuario['condominio_id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_perfil'] = $usuario['perfil'];
        $_SESSION['usuario_bloco'] = $usuario['bloco'];
        $_SESSION['usuario_apto'] = $usuario['apartamento'];

        // Redireciona conforme o perfil
        if ($usuario['perfil'] === 'sindico') {
            header("Location: ../pages/dashboard-sindico.php");
        } else {
            header("Location: ../index.php");
        }

        exit;

    } else {

        header("Location: ../pages/login.php?erro=1");
        exit;
    }

} else {

    header("Location: ../pages/login.php");
    exit;
}
```
