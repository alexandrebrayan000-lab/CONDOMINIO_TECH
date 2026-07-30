<?php
session_start();
require_once __DIR__ . '/../config/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../pages/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['usuario_id'];
    $titulo     = trim($_POST['titulo'] ?? '');
    $categoria  = trim($_POST['categoria'] ?? 'outro');
    $descricao  = trim($_POST['descricao'] ?? '');

    if (empty($titulo) || empty($descricao)) {
        header("Location: ../pages/feedback.php?erro=campos_vazios");
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO ocorrencias (usuario_id, titulo, categoria, descricao) VALUES (:uid, :tit, :cat, :desc)");
    $sucesso = $stmt->execute([
        ':uid'  => $usuario_id,
        ':tit'  => $titulo,
        ':cat'  => $categoria,
        ':desc' => $descricao
    ]);

    if ($sucesso) {
        header("Location: ../pages/feedback.php?sucesso=1");
        exit;
    } else {
        header("Location: ../pages/feedback.php?erro=falha");
        exit;
    }
} else {
    header("Location: ../pages/feedback.php");
    exit;
}