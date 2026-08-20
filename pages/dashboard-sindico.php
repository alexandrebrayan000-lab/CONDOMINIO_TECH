<?php

session_start();
require_once __DIR__ . '/../config/conexao.php';

/*
|--------------------------------------------------------------------------
| PROTEÇÃO
|--------------------------------------------------------------------------
*/

// Apenas síndicos podem acessar
if (
    !isset($_SESSION['usuario_id']) ||
    ($_SESSION['usuario_perfil'] ?? '') !== 'sindico'
) {
    header("Location: ../index.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| IDENTIFICAÇÃO DO CONDOMÍNIO
|--------------------------------------------------------------------------
*/

$condominio_id = $_SESSION['usuario_condominio_id'] ?? null;

if (!$condominio_id) {
    die("Erro: condomínio do síndico não identificado.");
}

$condominio_id = (int) $condominio_id;


/*
|--------------------------------------------------------------------------
| INFORMAÇÕES DO CONDOMÍNIO
|--------------------------------------------------------------------------
*/

$stmt_condominio = $pdo->prepare("
    SELECT nome
    FROM condominios
    WHERE id = :condominio_id
    LIMIT 1
");

$stmt_condominio->execute([
    ':condominio_id' => $condominio_id
]);

$condominio = $stmt_condominio->fetch();

$nome_condominio = $condominio['nome'] ?? 'Condomínio';


/*
|--------------------------------------------------------------------------
| 1. TOTAL DE MORADORES
|--------------------------------------------------------------------------
*/

$stmt_moradores = $pdo->prepare("
    SELECT COUNT(*)
    FROM usuarios
    WHERE condominio_id = :condominio_id
      AND perfil = 'morador'
      AND ativo = 1
");

$stmt_moradores->execute([
    ':condominio_id' => $condominio_id
]);

$total_moradores = $stmt_moradores->fetchColumn();


/*
|--------------------------------------------------------------------------
| 2. TOTAL DE RESERVAS ATIVAS
|--------------------------------------------------------------------------
*/

$stmt_reservas = $pdo->prepare("
    SELECT COUNT(*)
    FROM reservas r
    INNER JOIN espacos e
        ON r.espaco_id = e.id
    WHERE e.condominio_id = :condominio_id
      AND r.data_reserva >= CURDATE()
      AND r.status = 'confirmado'
");

$stmt_reservas->execute([
    ':condominio_id' => $condominio_id
]);

$total_reservas = $stmt_reservas->fetchColumn();


/*
|--------------------------------------------------------------------------
| 3. INTERAÇÕES RECENTES COM A IA
|--------------------------------------------------------------------------
*/

$stmt_ia = $pdo->prepare("
    SELECT
        i.*,
        u.nome AS usuario_nome
    FROM ia_interacoes i
    LEFT JOIN usuarios u
        ON i.usuario_id = u.id
    WHERE u.condominio_id = :condominio_id
    ORDER BY i.criado_em DESC
    LIMIT 5
");

$stmt_ia->execute([
    ':condominio_id' => $condominio_id
]);

$interacoes_ia = $stmt_ia->fetchAll();


/*
|--------------------------------------------------------------------------
| 4. PRÓXIMAS RESERVAS
|--------------------------------------------------------------------------
*/

$stmt_res = $pdo->prepare("
    SELECT
        r.data_reserva,
        e.nome AS espaco_nome,
        u.nome AS usuario_nome,
        u.apartamento,
        u.bloco
    FROM reservas r

    INNER JOIN espacos e
        ON r.espaco_id = e.id

    INNER JOIN usuarios u
        ON r.usuario_id = u.id

    WHERE e.condominio_id = :condominio_id
      AND r.data_reserva >= CURDATE()
      AND r.status = 'confirmado'

    ORDER BY r.data_reserva ASC

    LIMIT 5
");

$stmt_res->execute([
    ':condominio_id' => $condominio_id
]);

$reservas = $stmt_res->fetchAll();


/*
|--------------------------------------------------------------------------
| 5. ESPAÇOS ATIVOS
|--------------------------------------------------------------------------
*/

$stmt_espacos = $pdo->prepare("
    SELECT
        id,
        nome,
        capacidade
    FROM espacos
    WHERE condominio_id = :condominio_id
      AND ativo = 1
    ORDER BY nome ASC
");

$stmt_espacos->execute([
    ':condominio_id' => $condominio_id
]);

$espacos = $stmt_espacos->fetchAll();

?>

<!DOCTYPE html>

<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Painel da Gestão | Condomínio Tech
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/global.css"
    >

    <style>

        .container {

            max-width: 1200px;

            margin: 2rem auto;

            padding: 0 1.5rem;

        }


        /*
        |--------------------------------------------------------------------------
        | CABEÇALHO
        |--------------------------------------------------------------------------
        */

        .header {

            margin-bottom: 2rem;

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 1rem;

        }

        .header h1 {

            margin-bottom: 0.4rem;

        }

        .header p {

            color: var(--text-secondary);

        }

        .header-right {

            display: flex;

            align-items: center;

            gap: 1rem;

        }

        .btn-voltar {

            color: var(--accent-blue);

            text-decoration: none;

            white-space: nowrap;

        }

        .btn-voltar:hover {

            text-decoration: underline;

        }


        /*
        |--------------------------------------------------------------------------
        | CARDS DE ESTATÍSTICAS
        |--------------------------------------------------------------------------
        */

        .grid-stats {

            display: grid;

            grid-template-columns:
                repeat(auto-fit, minmax(220px, 1fr));

            gap: 1.5rem;

            margin-bottom: 2rem;

        }

        .stat-card {

            background: var(--bg-card);

            border: 1px solid var(--border-color);

            padding: 1.5rem;

            border-radius: var(--radius-md);

        }

        .stat-card h3 {

            font-size: 0.85rem;

            color: var(--text-secondary);

            margin-bottom: 0.5rem;

        }

        .stat-card .number {

            font-size: 2rem;

            font-weight: bold;

            color: var(--accent-blue);

        }


        /*
        |--------------------------------------------------------------------------
        | GESTÃO DE ESPAÇOS
        |--------------------------------------------------------------------------
        */

        .card-gestao {

            background: var(--bg-card);

            border: 1px solid var(--border-color);

            border-radius: var(--radius-md);

            padding: 1.5rem;

            margin-bottom: 1.5rem;

        }

        .card-gestao h2 {

            font-size: 1.1rem;

            margin-bottom: 0.4rem;

        }

        .card-gestao-description {

            color: var(--text-secondary);

            font-size: 0.9rem;

            margin-bottom: 1.2rem;

        }

        .gestao-buttons {

            display: flex;

            flex-wrap: wrap;

            gap: 0.8rem;

        }

        .btn-gestao {

            display: inline-block;

            padding: 0.7rem 1rem;

            border-radius: var(--radius-sm);

            text-decoration: none;

            font-size: 0.9rem;

            font-weight: 600;

            transition: 0.2s;

        }

        .btn-primary {

            background: var(--accent-blue);

            color: #000;

        }

        .btn-primary:hover {

            background: var(--accent-hover);

        }

        .btn-secondary {

            background: var(--bg-input);

            color: var(--text-primary);

            border: 1px solid var(--border-color);

        }

        .btn-secondary:hover {

            border-color: var(--accent-blue);

        }


        /*
        |--------------------------------------------------------------------------
        | LISTA DE ESPAÇOS
        |--------------------------------------------------------------------------
        */

        .espacos-resumo {

            display: grid;

            grid-template-columns:
                repeat(auto-fit, minmax(180px, 1fr));

            gap: 0.8rem;

            margin-top: 1.2rem;

        }

        .espaco-mini {

            padding: 0.9rem;

            background: var(--bg-input);

            border: 1px solid var(--border-color);

            border-radius: var(--radius-sm);

        }

        .espaco-mini strong {

            display: block;

            margin-bottom: 0.25rem;

        }

        .espaco-mini span {

            color: var(--text-secondary);

            font-size: 0.8rem;

        }


        /*
        |--------------------------------------------------------------------------
        | SEÇÕES
        |--------------------------------------------------------------------------
        */

        .grid-sections {

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 1.5rem;

        }

        .card {

            background: var(--bg-card);

            border: 1px solid var(--border-color);

            border-radius: var(--radius-md);

            padding: 1.5rem;

        }

        .card h2 {

            font-size: 1.1rem;

            margin-bottom: 1rem;

            color: var(--text-primary);

        }


        /*
        |--------------------------------------------------------------------------
        | TABELAS
        |--------------------------------------------------------------------------
        */

        .table-wrapper {

            overflow-x: auto;

        }

        .table {

            width: 100%;

            border-collapse: collapse;

            font-size: 0.88rem;

        }

        .table th,
        .table td {

            padding: 0.75rem 0.5rem;

            text-align: left;

            border-bottom:
                1px solid var(--border-color);

        }

        .table th {

            color: var(--text-secondary);

            font-weight: 500;

        }


        /*
        |--------------------------------------------------------------------------
        | BADGE
        |--------------------------------------------------------------------------
        */

        .badge {

            display: inline-block;

            padding: 0.2rem 0.5rem;

            border-radius: 4px;

            font-size: 0.75rem;

            background: var(--bg-input);

        }


        /*
        |--------------------------------------------------------------------------
        | VAZIO
        |--------------------------------------------------------------------------
        */

        .empty {

            color: var(--text-secondary);

            font-size: 0.9rem;

            padding: 0.5rem 0;

        }


        /*
        |--------------------------------------------------------------------------
        | RESPONSIVO
        |--------------------------------------------------------------------------
        */

        @media (max-width: 900px) {

            .grid-sections {

                grid-template-columns: 1fr;

            }

        }

        @media (max-width: 700px) {

            .header {

                flex-direction: column;

                align-items: flex-start;

            }

        }

    </style>

</head>

<body>

<div class="container">


    <!-- CABEÇALHO -->

    <div class="header">

        <div>

            <h1>
                ⚡ Painel do Síndico
            </h1>

            <p>
                <?= htmlspecialchars($nome_condominio); ?>
                · Visão geral da administração
            </p>

        </div>


        <div class="header-right">

            <a
                href="../index.php"
                class="btn-voltar"
            >
                ← Voltar ao App
            </a>

        </div>

    </div>


    <!-- ESTATÍSTICAS -->

    <div class="grid-stats">


        <div class="stat-card">

            <h3>
                MORADORES CADASTRADOS
            </h3>

            <div class="number">
                <?= (int) $total_moradores; ?>
            </div>

        </div>


        <div class="stat-card">

            <h3>
                RESERVAS ATIVAS
            </h3>

            <div class="number">
                <?= (int) $total_reservas; ?>
            </div>

        </div>


        <div class="stat-card">

            <h3>
                SISTEMA IA
            </h3>

            <div
                class="number"
                style="color: var(--accent-green);"
            >
                Ativo 24/7
            </div>

        </div>


    </div>


    <!-- GESTÃO DE ESPAÇOS -->

    <div class="card-gestao">

        <h2>
            🏢 Gestão de Espaços
        </h2>

        <p class="card-gestao-description">
            Gerencie os espaços do condomínio e consulte as reservas realizadas pelos moradores.
        </p>


        <div class="gestao-buttons">

            <a
                href="espacos.php"
                class="btn-gestao btn-primary"
            >
                ⚙️ Gerenciar Espaços
            </a>


            <a
                href="reservas.php"
                class="btn-gestao btn-secondary"
            >
                📅 Agendamento de Espaços
            </a>

        </div>


        <?php if (count($espacos) > 0): ?>

            <div class="espacos-resumo">

                <?php foreach ($espacos as $espaco): ?>

                    <div class="espaco-mini">

                        <strong>
                            <?= htmlspecialchars($espaco['nome']); ?>
                        </strong>

                        <?php if ($espaco['capacidade'] !== null): ?>

                            <span>
                                👥 Até
                                <?= (int) $espaco['capacidade']; ?>
                                pessoas
                            </span>

                        <?php else: ?>

                            <span>
                                Capacidade não informada
                            </span>

                        <?php endif; ?>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <p class="empty">
                Nenhum espaço ativo cadastrado.
            </p>

        <?php endif; ?>


    </div>


    <!-- DUAS COLUNAS -->

    <div class="grid-sections">


        <!-- IA -->

        <div class="card">

            <h2>
                🤖 Perguntas Recentes para a IA Concierge
            </h2>


            <?php if (count($interacoes_ia) > 0): ?>

                <div class="table-wrapper">

                    <table class="table">

                        <thead>

                            <tr>

                                <th>
                                    Morador
                                </th>

                                <th>
                                    Pergunta / Intenção
                                </th>

                                <th>
                                    Data
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php foreach ($interacoes_ia as $ia): ?>

                                <tr>

                                    <td>
                                        <b>
                                            <?= htmlspecialchars(
                                                $ia['usuario_nome'] ?? 'Visitante'
                                            ); ?>
                                        </b>
                                    </td>


                                    <td>

                                        <?= htmlspecialchars(
                                            $ia['pergunta']
                                        ); ?>

                                        <br>

                                        <span class="badge">

                                            <?= htmlspecialchars(
                                                $ia['intencao']
                                            ); ?>

                                        </span>

                                    </td>


                                    <td>

                                        <?= date(
                                            'd/m H:i',
                                            strtotime($ia['criado_em'])
                                        ); ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php else: ?>

                <p class="empty">
                    Nenhuma interação registrada ainda.
                </p>

            <?php endif; ?>


        </div>


        <!-- RESERVAS -->

        <div class="card">

            <h2>
                📅 Próximas Reservas de Espaços
            </h2>


            <?php if (count($reservas) > 0): ?>

                <div class="table-wrapper">

                    <table class="table">

                        <thead>

                            <tr>

                                <th>
                                    Espaço
                                </th>

                                <th>
                                    Morador
                                </th>

                                <th>
                                    Data
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php foreach ($reservas as $res): ?>

                                <tr>

                                    <td>

                                        <b>
                                            <?= htmlspecialchars(
                                                $res['espaco_nome']
                                            ); ?>
                                        </b>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars(
                                            $res['usuario_nome']
                                        ); ?>

                                        <?php if (
                                            !empty($res['bloco']) ||
                                            !empty($res['apartamento'])
                                        ): ?>

                                            <br>

                                            <span class="badge">

                                                <?= htmlspecialchars(
                                                    ($res['bloco'] ?? '') .
                                                    '-' .
                                                    ($res['apartamento'] ?? '')
                                                ); ?>

                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <td>

                                        <?= date(
                                            'd/m/Y',
                                            strtotime($res['data_reserva'])
                                        ); ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php else: ?>

                <p class="empty">
                    Nenhuma reserva futura registrada.
                </p>

            <?php endif; ?>


        </div>


    </div>

</div>

</body>

</html>