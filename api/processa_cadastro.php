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

    // Valida se os campos essenciais foram preenchidos
    if (empty($nome) || empty($email) || empty($senha)) {
        header("Location: ../pages/cadastro.php?erro=campos_vazios");
        exit;
    }

    // Verifica se o e-mail já existe no banco
    $stmt_check = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
    $stmt_check->execute([':email' => $email]);

    if ($stmt_check->fetch()) {
        header("Location: ../pages/cadastro.php?erro=email_existente");
        exit;
    }

    // Criptografa a senha de forma segura
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Insere o novo usuário no MySQL
    $sql = "INSERT INTO usuarios (nome, email, senha, perfil, bloco, apartamento) 
            VALUES (:nome, :email, :senha, :perfil, :bloco, :apartamento)";
    
    $stmt_insert = $pdo->prepare($sql);
    $sucesso = $stmt_insert->execute([
        ':nome'        => $nome,
        ':email'       => $email,
        ':senha'       => $senhaHash,
        ':perfil'      => $perfil,
        ':bloco'       => $bloco,
        ':apartamento' => $apartamento
    ]);

    if ($sucesso) {
        header("Location: ../pages/cadastro.php?sucesso=1");
        exit;
    } else {
        header("Location: ../pages/cadastro.php?erro=falha_geral");
        exit;
    }
} else {
    header("Location: ../pages/cadastro.php");
    exit;
}