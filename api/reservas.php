<?php
session_start();
require_once __DIR__ . '/../config/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../pages/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id   = $_SESSION['usuario_id'];
    $espaco_id    = filter_input(INPUT_POST, 'espaco_id', FILTER_VALIDATE_INT);
    $data_reserva = $_POST['data_reserva'] ?? '';

    if (!$espaco_id || empty($data_reserva)) {
        header("Location: ../pages/reservas.php?erro=campos_invalidos");
        exit;
    }

    // Verifica se a data já está ocupada para aquele espaço
    $stmt_check = $pdo->prepare("SELECT id FROM reservas WHERE espaco_id = :espaco_id AND data_reserva = :data_reserva AND status = 'confirmado'");
    $stmt_check->execute([
        ':espaco_id'    => $espaco_id,
        ':data_reserva' => $data_reserva
    ]);

    if ($stmt_check->fetch()) {
        header("Location: ../pages/reservas.php?erro=data_ocupada");
        exit;
    }

    // Insere a nova reserva
    $sql = "INSERT INTO reservas (usuario_id, espaco_id, data_reserva) VALUES (:usuario_id, :espaco_id, :data_reserva)";
    $stmt = $pdo->prepare($sql);
    
    $sucesso = $stmt->execute([
        ':usuario_id'   => $usuario_id,
        ':espaco_id'    => $espaco_id,
        ':data_reserva' => $data_reserva
    ]);

    if ($sucesso) {
        header("Location: ../pages/reservas.php?sucesso=1");
        exit;
    } else {
        header("Location: ../pages/reservas.php?erro=falha");
        exit;
    }
} else {
    header("Location: ../pages/reservas.php");
    exit;
}