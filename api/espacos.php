<?php

session_start();
require_once __DIR__ . '/../config/conexao.php';

// Apenas síndico
if (
    !isset($_SESSION['usuario_id']) ||
    ($_SESSION['usuario_perfil'] ?? '') !== 'sindico'
) {
    header("Location: ../index.php");
    exit;
}

// Descobre o condomínio do síndico
$stmt = $pdo->prepare("
    SELECT condominio_id
    FROM usuarios
    WHERE id = :usuario_id
");

$stmt->execute([
    ':usuario_id' => $_SESSION['usuario_id']
]);

$usuario = $stmt->fetch();

if (!$usuario || !$usuario['condominio_id']) {
    die("Condomínio não encontrado.");
}

$condominio_id = $usuario['condominio_id'];


// ============================
// CADASTRAR ESPAÇO
// ============================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $capacidade = $_POST['capacidade'] ?? null;

    if ($nome === '') {
        header("Location: ../pages/espacos.php?erro=nome");
        exit;
    }

    if ($capacidade === '') {
        $capacidade = null;
    } else {
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
            header("Location: ../pages/espacos.php?erro=capacidade");
            exit;
        }
    }

    // Verifica se já existe espaço com o mesmo nome
    $stmt = $pdo->prepare("
        SELECT id
        FROM espacos
        WHERE condominio_id = :condominio_id
        AND nome = :nome
    ");

    $stmt->execute([
        ':condominio_id' => $condominio_id,
        ':nome' => $nome
    ]);

    if ($stmt->fetch()) {
        header("Location: ../pages/espacos.php?erro=existente");
        exit;
    }

    // Cadastra o espaço
    $stmt = $pdo->prepare("
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
            1
        )
    ");

    $stmt->execute([
        ':condominio_id' => $condominio_id,
        ':nome' => $nome,
        ':descricao' => $descricao !== '' ? $descricao : null,
        ':capacidade' => $capacidade
    ]);

    header("Location: ../pages/espacos.php?sucesso=1");
    exit;
}


// ============================
// ATIVAR / DESATIVAR ESPAÇO
// ============================

if (isset($_GET['acao'], $_GET['id'])) {

    $acao = $_GET['acao'];
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if (!$id) {
        header("Location: ../pages/espacos.php");
        exit;
    }

    if ($acao === 'ativar') {
        $novo_status = 1;
    } elseif ($acao === 'desativar') {
        $novo_status = 0;
    } else {
        header("Location: ../pages/espacos.php");
        exit;
    }

    // Só altera espaços pertencentes ao condomínio do síndico
    $stmt = $pdo->prepare("
        UPDATE espacos
        SET ativo = :ativo
        WHERE id = :id
        AND condominio_id = :condominio_id
    ");

    $stmt->execute([
        ':ativo' => $novo_status,
        ':id' => $id,
        ':condominio_id' => $condominio_id
    ]);

    header("Location: ../pages/espacos.php?sucesso=status");
    exit;
}


// Se chegar aqui sem nenhuma ação
header("Location: ../pages/espacos.php");
exit;