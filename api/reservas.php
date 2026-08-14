<?php

session_start();
require_once __DIR__ . '/../config/conexao.php';

/*
|--------------------------------------------------------------------------
| PROTEÇÃO
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../pages/login.php");
    exit;
}

$usuario_id = (int) $_SESSION['usuario_id'];

$condominio_id = $_SESSION['usuario_condominio_id'] ?? null;

if (!$condominio_id) {
    die("Erro: condomínio do usuário não identificado.");
}

$condominio_id = (int) $condominio_id;


/*
|--------------------------------------------------------------------------
| ACEITA SOMENTE POST
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/reservas.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| RECEBE OS DADOS
|--------------------------------------------------------------------------
*/

$espaco_id = filter_input(
    INPUT_POST,
    'espaco_id',
    FILTER_VALIDATE_INT
);

$data_reserva = trim(
    $_POST['data_reserva'] ?? ''
);


/*
|--------------------------------------------------------------------------
| VALIDA OS CAMPOS
|--------------------------------------------------------------------------
*/

if (!$espaco_id || empty($data_reserva)) {
    header("Location: ../pages/reservas.php?erro=campos_invalidos");
    exit;
}


/*
|--------------------------------------------------------------------------
| VALIDA A DATA
|--------------------------------------------------------------------------
*/

$data = DateTime::createFromFormat('Y-m-d', $data_reserva);

if (!$data || $data->format('Y-m-d') !== $data_reserva) {
    header("Location: ../pages/reservas.php?erro=data_invalida");
    exit;
}


/*
|--------------------------------------------------------------------------
| NÃO PERMITE RESERVA NO PASSADO
|--------------------------------------------------------------------------
*/

$hoje = new DateTime(date('Y-m-d'));

if ($data < $hoje) {
    header("Location: ../pages/reservas.php?erro=data_invalida");
    exit;
}


/*
|--------------------------------------------------------------------------
| VERIFICA O ESPAÇO
|--------------------------------------------------------------------------
|
| O espaço precisa:
| - existir
| - pertencer ao condomínio do usuário
| - estar ativo
|
*/

$stmt_espaco = $pdo->prepare("
    SELECT id, nome
    FROM espacos
    WHERE id = :espaco_id
      AND condominio_id = :condominio_id
      AND ativo = 1
    LIMIT 1
");

$stmt_espaco->execute([
    ':espaco_id' => $espaco_id,
    ':condominio_id' => $condominio_id
]);

$espaco = $stmt_espaco->fetch();

if (!$espaco) {
    header("Location: ../pages/reservas.php?erro=espaco_invalido");
    exit;
}


/*
|--------------------------------------------------------------------------
| VERIFICA SE A DATA JÁ ESTÁ RESERVADA
|--------------------------------------------------------------------------
*/

$stmt_check = $pdo->prepare("
    SELECT id
    FROM reservas
    WHERE espaco_id = :espaco_id
      AND data_reserva = :data_reserva
      AND status = 'confirmado'
    LIMIT 1
");

$stmt_check->execute([
    ':espaco_id' => $espaco_id,
    ':data_reserva' => $data_reserva
]);

if ($stmt_check->fetch()) {
    header("Location: ../pages/reservas.php?erro=data_ocupada");
    exit;
}


/*
|--------------------------------------------------------------------------
| CRIA A RESERVA
|--------------------------------------------------------------------------
*/

try {

    $sql = "
        INSERT INTO reservas (
            usuario_id,
            espaco_id,
            data_reserva,
            status
        )
        VALUES (
            :usuario_id,
            :espaco_id,
            :data_reserva,
            'confirmado'
        )
    ";

    $stmt_insert = $pdo->prepare($sql);

    $stmt_insert->execute([
        ':usuario_id' => $usuario_id,
        ':espaco_id' => $espaco_id,
        ':data_reserva' => $data_reserva
    ]);


    /*
    |--------------------------------------------------------------------------
    | SUCESSO
    |--------------------------------------------------------------------------
    */

    header("Location: ../pages/reservas.php?sucesso=1");
    exit;

} catch (PDOException $e) {

    /*
    |--------------------------------------------------------------------------
    | ERRO
    |--------------------------------------------------------------------------
    */

    header("Location: ../pages/reservas.php?erro=falha");
    exit;
}