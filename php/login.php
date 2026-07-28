<?php
session_start();
require_once 'conexao.php';

// Verifica se os dados vieram via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'] ?? '';

    if (!$email || empty($senha)) {
        header('Location: ../index.php?erro=campos_invalidos#login');
        exit;
    }

    // Se o banco ainda não estiver configurado hoje, simula uma pausa
    if (!$pdo) {
        header('Location: ../index.php?erro=banco_desconectado#login');
        exit;
    }

    try {
        // Consulta segura contra SQL Injection
        $stmt = $pdo->prepare("SELECT id, nome, email, senha FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch();

        // Verifica a senha (assumindo que usará password_hash no cadastro)
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // Regrava o ID da sessão por segurança
            session_regenerate_id(true);

            // Armazena na Sessão PHP
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_email'] = $usuario['email'];

            header('Location: ../index.php?status=sucesso');
            exit;
        } else {
            header('Location: ../index.php?erro=dados_incorretos#login');
            exit;
        }
    } catch (PDOException $e) {
        header('Location: ../index.php?erro=falha_sistema#login');
        exit;
    }
}