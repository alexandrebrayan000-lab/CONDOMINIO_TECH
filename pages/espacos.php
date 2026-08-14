```php
<?php

session_start();
require_once __DIR__ . '/../config/conexao.php';

/*
|--------------------------------------------------------------------------
| PROTEÇÃO
|--------------------------------------------------------------------------
*/

// Apenas síndicos podem acessar esta página
if (
    !isset($_SESSION['usuario_id']) ||
    ($_SESSION['usuario_perfil'] ?? '') !== 'sindico'
) {
    header("Location: ../index.php");
    exit;
}

// Condomínio do usuário logado
$condominio_id = $_SESSION['usuario_condominio_id'] ?? null;

if (!$condominio_id) {
    die("Erro: condomínio do usuário não identificado.");
}

/*
|--------------------------------------------------------------------------
| BUSCA OS ESPAÇOS DO CONDOMÍNIO
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        id,
        nome,
        descricao,
        capacidade,
        ativo,
        criado_em
    FROM espacos
    WHERE condominio_id = :condominio_id
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

    <title>Gerenciar Espaços | Condomínio Tech</title>

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

        .btn-voltar {
            color: var(--accent-blue);
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-voltar:hover {
            text-decoration: underline;
        }

        .grid {
            display: grid;
            grid-template-columns: 360px 1fr;
            gap: 1.5rem;
            align-items: start;
        }

        @media (max-width: 900px) {

            .grid {
                grid-template-columns: 1fr;
            }

        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 1.5rem;
        }

        .card h2 {
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            color: var(--text-primary);
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .form-group input,
        .form-group textarea {

            width: 100%;
            padding: 0.75rem 1rem;

            background: var(--bg-input);

            border: 1px solid var(--border-color);

            border-radius: var(--radius-sm);

            color: var(--text-primary);

            font-size: 0.95rem;

            outline: none;

            box-sizing: border-box;
        }

        .form-group textarea {

            min-height: 110px;

            resize: vertical;
        }

        .form-group input:focus,
        .form-group textarea:focus {

            border-color: var(--accent-blue);
        }

        .checkbox-group {

            display: flex;

            align-items: center;

            gap: 0.6rem;

            margin-bottom: 1.2rem;
        }

        .checkbox-group input {

            width: auto;
        }

        .checkbox-group label {

            color: var(--text-secondary);

            font-size: 0.875rem;
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

            padding: 0.8rem 0.6rem;

            text-align: left;

            border-bottom: 1px solid var(--border-color);
        }

        .table th {

            color: var(--text-secondary);

            font-weight: 500;
        }

        .table td {

            color: var(--text-primary);
        }

        .badge {

            display: inline-block;

            padding: 0.25rem 0.6rem;

            border-radius: 4px;

            font-size: 0.75rem;
        }

        .badge-ativo {

            background: rgba(16, 185, 129, 0.1);

            border: 1px solid var(--accent-green);

            color: var(--accent-green);
        }

        .badge-inativo {

            background: rgba(239, 68, 68, 0.1);

            border: 1px solid #ef4444;

            color: #f87171;
        }

        .empty {

            text-align: center;

            padding: 2rem;

            color: var(--text-secondary);
        }

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

        .info {

            margin-top: 1rem;

            padding: 0.8rem;

            background: var(--bg-input);

            border: 1px solid var(--border-color);

            border-radius: var(--radius-sm);

            color: var(--text-secondary);

            font-size: 0.8rem;

            line-height: 1.5;
        }

    </style>

</head>

<body>

<div class="container">

    <!-- CABEÇALHO -->

    <div class="header">

        <div>

            <h1>🏢 Gerenciamento de Espaços</h1>

            <p>
                Cadastre e gerencie as áreas comuns do condomínio.
            </p>

        </div>

        <a
            href="dashboard-sindico.php"
            class="btn-voltar"
        >
            ← Voltar ao Painel
        </a>

    </div>


    <!-- MENSAGENS -->

    <?php if ($sucesso === '1'): ?>

        <div class="alert alert-success">
            Espaço cadastrado com sucesso!
        </div>

    <?php endif; ?>


    <?php if ($erro === 'nome_obrigatorio'): ?>

        <div class="alert alert-error">
            Informe o nome do espaço.
        </div>

    <?php elseif ($erro === 'nome_longo'): ?>

        <div class="alert alert-error">
            O nome do espaço não pode ultrapassar 100 caracteres.
        </div>

    <?php elseif ($erro === 'capacidade_invalida'): ?>

        <div class="alert alert-error">
            A capacidade precisa ser um número maior que zero.
        </div>

    <?php elseif ($erro === 'espaco_existente'): ?>

        <div class="alert alert-error">
            Já existe um espaço com esse nome neste condomínio.
        </div>

    <?php elseif ($erro === 'condominio_nao_identificado'): ?>

        <div class="alert alert-error">
            Não foi possível identificar o condomínio do usuário.
        </div>

    <?php elseif ($erro === 'falha_cadastro'): ?>

        <div class="alert alert-error">
            Não foi possível cadastrar o espaço.
        </div>

    <?php endif; ?>


    <!-- CONTEÚDO -->

    <div class="grid">


        <!-- FORMULÁRIO -->

        <div class="card">

            <h2>➕ Novo Espaço</h2>

            <form
                action="../api/espacos.php"
                method="POST"
            >

                <input
                    type="hidden"
                    name="acao"
                    value="cadastrar"
                >


                <div class="form-group">

                    <label for="nome">
                        Nome do espaço
                    </label>

                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        maxlength="100"
                        placeholder="Ex.: Salão de Festas"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="descricao">
                        Descrição
                    </label>

                    <textarea
                        id="descricao"
                        name="descricao"
                        placeholder="Descreva o espaço..."
                    ></textarea>

                </div>


                <div class="form-group">

                    <label for="capacidade">
                        Capacidade
                    </label>

                    <input
                        type="number"
                        id="capacidade"
                        name="capacidade"
                        min="1"
                        placeholder="Ex.: 50"
                    >

                </div>


                <div class="checkbox-group">

                    <input
                        type="checkbox"
                        id="ativo"
                        name="ativo"
                        value="1"
                        checked
                    >

                    <label for="ativo">
                        Espaço disponível para reservas
                    </label>

                </div>


                <button
                    type="submit"
                    class="btn-submit"
                >
                    Cadastrar Espaço
                </button>

            </form>


            <div class="info">

                💡 Espaços desativados continuam cadastrados,
                mas não ficam disponíveis para novas reservas.

            </div>

        </div>


        <!-- LISTA DE ESPAÇOS -->

        <div class="card">

            <h2>📋 Espaços Cadastrados</h2>

            <div class="table-wrapper">

                <?php if (count($espacos) > 0): ?>

                    <table class="table">

                        <thead>

                            <tr>

                                <th>
                                    Espaço
                                </th>

                                <th>
                                    Capacidade
                                </th>

                                <th>
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php foreach ($espacos as $espaco): ?>

                                <tr>

                                    <td>

                                        <strong>
                                            <?= htmlspecialchars($espaco['nome']); ?>
                                        </strong>


                                        <?php if (!empty($espaco['descricao'])): ?>

                                            <br>

                                            <small style="color: var(--text-secondary);">

                                                <?= htmlspecialchars($espaco['descricao']); ?>

                                            </small>

                                        <?php endif; ?>

                                    </td>


                                    <td>

                                        <?php if ($espaco['capacidade'] !== null): ?>

                                            👥
                                            <?= (int)$espaco['capacidade']; ?>

                                        <?php else: ?>

                                            —

                                        <?php endif; ?>

                                    </td>


                                    <td>

                                        <?php if ((int)$espaco['ativo'] === 1): ?>

                                            <span class="badge badge-ativo">
                                                Ativo
                                            </span>

                                        <?php else: ?>

                                            <span class="badge badge-inativo">
                                                Inativo
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                <?php else: ?>

                    <div class="empty">

                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">
                            🏢
                        </div>

                        <div>
                            Nenhum espaço cadastrado ainda.
                        </div>

                        <small>
                            Use o formulário ao lado para cadastrar o primeiro espaço.
                        </small>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

</div>

</body>

</html>
```
