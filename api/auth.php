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

    // Busca usuário no banco
    $stmt = $pdo->prepare("SELECT id, nome, email, senha, perfil, bloco, apartamento FROM usuarios WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch();

    // Valida a senha usando password_verify
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        // Guarda os dados na sessão
        $_SESSION['usuario_id']     = $usuario['id'];
        $_SESSION['usuario_nome']   = $usuario['nome'];
        $_SESSION['usuario_email']  = $usuario['email'];
        $_SESSION['usuario_perfil'] = $usuario['perfil']; // 'morador', 'sindico', etc.
        $_SESSION['usuario_bloco']  = $usuario['bloco'];
        $_SESSION['usuario_apto']   = $usuario['apartamento'];

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