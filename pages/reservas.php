<?php

session_start();
require_once __DIR__ . '/../config/conexao.php';

/*
|--------------------------------------------------------------------------
| PROTEÇÃO
|--------------------------------------------------------------------------
*/

// Exige login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Condomínio do usuário logado
$condominio_id = $_SESSION['usuario_condominio_id'] ?? null;

if (!$condominio_id) {
    die("Erro: condomínio do usuário não identificado.");
}

/*
|--------------------------------------------------------------------------
| BUSCA OS ESPAÇOS ATIVOS DO CONDOMÍNIO
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        id,
        nome,
        descricao,
        capacidade
    FROM espacos
    WHERE condominio_id = :condominio_id
    AND ativo = 1
    ORDER BY nome ASC
");

$stmt->execute([
    ':condominio_id' => $condominio_id
]);

$espacos = $stmt->fetchAll();

/*
|--------------------------------------------------------------------------
| MENSAGENS
|--------------------------------------------------------------------------
*/

$sucesso = $_GET['sucesso'] ?? '';
$erro = $_GET['erro'] ?? '';

?>
<!DOCTYPE html>

<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Reserva de Espaços | Condomínio Tech</title>

    <link
        rel="stylesheet"
        href="../assets/css/global.css"
    >

    <style>

        .page-container {
            max-width: 1100px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        .page-header {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .page-header h1 {
            margin-bottom: 0.4rem;
        }

        .page-header p {
            color: var(--text-secondary);
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
        | ALERTAS
        |--------------------------------------------------------------------------
        */

        .alert {
            padding: 0.8rem 1rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 0.9rem;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid var(--accent-green);
            color: var(--accent-green);
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid #ef4444;
            color: #f87171;
        }

        /*
        |--------------------------------------------------------------------------
        | ESPAÇOS
        |--------------------------------------------------------------------------
        */

        .grid-espacos {

            display: grid;

            grid-template-columns:
                repeat(auto-fit, minmax(280px, 1fr));

            gap: 1.5rem;

            margin-bottom: 3rem;
        }

        .card-espaco {

            background-color: var(--bg-card);

            border: 1px solid var(--border-color);

            border-radius: var(--radius-md);

            padding: 1.5rem;

            box-shadow: var(--shadow);

            display: flex;

            flex-direction: column;

            justify-content: space-between;

            min-height: 180px;
        }

        .card-espaco h3 {

            color: var(--accent-blue);

            margin-bottom: 0.6rem;

        }

        .card-espaco p {

            color: var(--text-secondary);

            font-size: 0.9rem;

            line-height: 1.5;

            margin-bottom: 1rem;

        }

        .capacidade-badge {

            display: inline-block;

            background-color: var(--bg-input);

            border: 1px solid var(--border-color);

            padding: 0.3rem 0.6rem;

            border-radius: var(--radius-sm);

            font-size: 0.8rem;

            color: var(--text-primary);

        }

        /*
        |--------------------------------------------------------------------------
        | FORMULÁRIO
        |--------------------------------------------------------------------------
        */

        .form-reserva {

            background-color: var(--bg-card);

            border: 1px solid var(--border-color);

            border-radius: var(--radius-md);

            padding: 2rem;

            max-width: 600px;

            margin: 0 auto;
        }

        .form-reserva h2 {

            margin-bottom: 0.5rem;

            font-size: 1.3rem;

        }

        .form-description {

            color: var(--text-secondary);

            font-size: 0.9rem;

            margin-bottom: 1.5rem;

        }

        .form-group {

            margin-bottom: 1.25rem;

        }

        .form-group label {

            display: block;

            margin-bottom: 0.5rem;

            font-size: 0.875rem;

            color: var(--text-secondary);

        }

        .form-group input,
        .form-group select {

            width: 100%;

            padding: 0.75rem 1rem;

            background-color: var(--bg-input);

            border: 1px solid var(--border-color);

            border-radius: var(--radius-sm);

            color: var(--text-primary);

            font-size: 0.95rem;

            outline: none;

            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group select:focus {

            border-color: var(--accent-blue);

        }

        .btn-submit {

            width: 100%;

            padding: 0.85rem;

            background-color: var(--accent-blue);

            color: #000;

            border: none;

            font-weight: 700;

            border-radius: var(--radius-sm);

            font-size: 1rem;

            cursor: pointer;

        }

        .btn-submit:hover {

            background-color: var(--accent-hover);

        }

        .btn-submit:disabled {

            opacity: 0.5;

            cursor: not-allowed;

        }

        /*
        |--------------------------------------------------------------------------
        | SEM ESPAÇOS
        |--------------------------------------------------------------------------
        */

        .empty {

            background: var(--bg-card);

            border: 1px solid var(--border-color);

            border-radius: var(--radius-md);

            padding: 2rem;

            text-align: center;

            color: var(--text-secondary);

            margin-bottom: 3rem;
        }

        /*
        |--------------------------------------------------------------------------
        | RESPONSIVO
        |--------------------------------------------------------------------------
        */

        @media (max-width: 700px) {

            .page-header {

                flex-direction: column;

                align-items: flex-start;

            }

        }

    </style>

</head>

<body>

<div class="page-container">

    <!-- CABEÇALHO -->

    <div class="page-header">

        <div>

            <h1>🗓️ Agendamento de Espaços</h1>

            <p>
                Escolha uma área comum e solicite sua reserva.
            </p>

        </div>

        <a
            href="../index.php"
            class="btn-voltar"
        >
            ← Voltar ao App
        </a>

    </div>


    <!-- MENSAGENS -->

    <?php if ($sucesso === '1'): ?>

        <div class="alert alert-success">
            Reserva realizada com sucesso!
        </div>

    <?php endif; ?>


    <?php if ($erro === 'data_ocupada'): ?>

        <div class="alert alert-error">
            Este espaço já está reservado na data selecionada.
        </div>

    <?php elseif ($erro === 'campos_invalidos'): ?>

        <div class="alert alert-error">
            Selecione um espaço e informe uma data válida.
        </div>

    <?php elseif ($erro === 'espaco_invalido'): ?>

        <div class="alert alert-error">
            O espaço selecionado não pertence a este condomínio ou está indisponível.
        </div>

    <?php elseif ($erro === 'data_invalida'): ?>

        <div class="alert alert-error">
            A data da reserva não pode ser anterior a hoje.
        </div>

    <?php elseif ($erro === 'falha'): ?>

        <div class="alert alert-error">
            Não foi possível realizar a reserva.
        </div>

    <?php endif; ?>


    <!-- ESPAÇOS -->

    <?php if (count($espacos) > 0): ?>

        <div class="grid-espacos">

            <?php foreach ($espacos as $espaco): ?>

                <div class="card-espaco">

                    <div>

                        <h3>
                            <?= htmlspecialchars($espaco['nome']); ?>
                        </h3>

                        <?php if (!empty($espaco['descricao'])): ?>

                            <p>
                                <?= htmlspecialchars($espaco['descricao']); ?>
                            </p>

                        <?php else: ?>

                            <p>
                                Espaço disponível para utilização dos moradores.
                            </p>

                        <?php endif; ?>

                    </div>


                    <div>

                        <?php if ($espaco['capacidade'] !== null): ?>

                            <span class="capacidade-badge">
                                👥 Até
                                <?= (int)$espaco['capacidade']; ?>
                                pessoas
                            </span>

                        <?php endif; ?>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="empty">

            <div style="font-size: 2rem; margin-bottom: 0.5rem;">
                🏢
            </div>

            <strong>
                Nenhum espaço disponível
            </strong>

            <p style="margin-top: 0.5rem;">
                Não existem espaços ativos cadastrados para este condomínio.
            </p>

        </div>

    <?php endif; ?>


    <!-- FORMULÁRIO DE RESERVA -->

    <?php if (count($espacos) > 0): ?>

        <div class="form-reserva">

            <h2>
                Solicitar Agendamento
            </h2>

            <p class="form-description">
                Selecione o espaço e a data desejada para realizar sua reserva.
            </p>


            <form
                action="../api/reservas.php"
                method="POST"
            >

                <!-- ESPAÇO -->

                <div class="form-group">

                    <label for="espaco_id">
                        Selecione o Espaço
                    </label>

                    <select
                        id="espaco_id"
                        name="espaco_id"
                        required
                    >

                        <option value="">
                            -- Escolha uma opção --
                        </option>

                        <?php foreach ($espacos as $espaco): ?>

                            <option
                                value="<?= (int)$espaco['id']; ?>"
                            >
                                <?= htmlspecialchars($espaco['nome']); ?>

                                <?php if ($espaco['capacidade'] !== null): ?>

                                    - até
                                    <?= (int)$espaco['capacidade']; ?>
                                    pessoas

                                <?php endif; ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- DATA -->

                <div class="form-group">

                    <label for="data_reserva">
                        Data da Reserva
                    </label>

                    <input
                        type="date"
                        id="data_reserva"
                        name="data_reserva"
                        min="<?= date('Y-m-d'); ?>"
                        required
                    >

                </div>


                <!-- BOTÃO -->

                <button
                    type="submit"
                    class="btn-submit"
                >
                    Confirmar Agendamento
                </button>

            </form>

        </div>

    <?php endif; ?>

</div>


<!-- IA CONCIERGE -->

<?php include '../includes/ia-widget.php'; ?>


</body>

</html>