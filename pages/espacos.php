<?php
session_start();
require_once __DIR__ . '/../config/conexao.php';

if (
    !isset($_SESSION['usuario_id']) ||
    ($_SESSION['usuario_perfil'] ?? '') !== 'sindico'
) {
    header("Location: ../index.php");
    exit;
}

// Busca o condomínio do síndico
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

// Busca os espaços
$stmt = $pdo->prepare("
    SELECT id, nome, descricao, capacidade, ativo
    FROM espacos
    WHERE condominio_id = :condominio_id
    ORDER BY nome ASC
");

$stmt->execute([
    ':condominio_id' => $condominio_id
]);

$espacos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Espaços | Condomínio Tech</title>

    <link rel="stylesheet" href="../assets/css/global.css">

    <style>

        .container {
            max-width: 1100px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .header p {
            color: var(--text-secondary);
        }

        .acoes {
            display: flex;
            gap: 0.7rem;
        }

        .btn {
            display: inline-block;
            padding: 0.7rem 1rem;
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-weight: 600;
            border: none;
            cursor: pointer;
        }

        .btn-voltar {
            color: var(--accent-blue);
        }

        .btn-novo {
            background: var(--accent-blue);
            color: #000;
        }

        .form-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .form-card h2 {
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.4rem;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            background: var(--bg-input);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-size: 0.95rem;
            box-sizing: border-box;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .btn-cadastrar {
            background: var(--accent-blue);
            color: #000;
            width: 100%;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 1.5rem;
        }

        .card h2 {
            margin-bottom: 0.5rem;
        }

        .descricao {
            color: var(--text-secondary);
            margin-bottom: 1rem;
        }

        .capacidade {
            display: inline-block;
            padding: 0.35rem 0.6rem;
            border-radius: var(--radius-sm);
            background: var(--bg-input);
            font-size: 0.85rem;
        }

        .status {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.35rem 0.6rem;
            border-radius: var(--radius-sm);
            font-size: 0.8rem;
        }

        .ativo {
            color: var(--accent-green);
            background: rgba(16, 185, 129, 0.1);
        }

        .inativo {
            color: #f87171;
            background: rgba(239, 68, 68, 0.1);
        }

        .acoes-card {
            margin-top: 1rem;
        }

        .btn-status {
            background: var(--bg-input);
            color: var(--text-primary);
            font-size: 0.8rem;
        }

        .alert {
            padding: 0.8rem 1rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1.5rem;
        }

        .sucesso {
            color: var(--accent-green);
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid var(--accent-green);
        }

        .erro {
            color: #f87171;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid #ef4444;
        }

        @media (max-width: 700px) {

            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .acoes {
                width: 100%;
            }

        }

    </style>

</head>

<body>

<div class="container">

    <div class="header">

        <div>
            <h1>🏢 Espaços Cadastrados</h1>
            <p>Gerencie os espaços disponíveis no condomínio.</p>
        </div>

        <div class="acoes">

            <a href="dashboard-sindico.php" class="btn btn-voltar">
                ← Voltar
            </a>

            <a href="#novo-espaco" class="btn btn-novo">
                + Novo Espaço
            </a>

        </div>

    </div>


    <!-- MENSAGENS -->

    <?php if (isset($_GET['sucesso'])): ?>

        <div class="alert sucesso">
            Espaço cadastrado com sucesso!
        </div>

    <?php endif; ?>


    <?php if (isset($_GET['erro'])): ?>

        <div class="alert erro">

            <?php

            if ($_GET['erro'] === 'nome') {
                echo "Informe o nome do espaço.";
            }

            elseif ($_GET['erro'] === 'capacidade') {
                echo "A capacidade deve ser um número maior que zero.";
            }

            elseif ($_GET['erro'] === 'existente') {
                echo "Já existe um espaço com esse nome.";
            }

            else {
                echo "Não foi possível realizar a operação.";
            }

            ?>

        </div>

    <?php endif; ?>


    <!-- FORMULÁRIO -->

    <div class="form-card" id="novo-espaco">

        <h2>➕ Cadastrar Novo Espaço</h2>

        <form action="../api/espacos.php" method="POST">

            <div class="form-group">

                <label for="nome">
                    Nome do espaço
                </label>

                <input
                    type="text"
                    id="nome"
                    name="nome"
                    maxlength="100"
                    placeholder="Ex.: Academia"
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
                    Capacidade máxima
                </label>

                <input
                    type="number"
                    id="capacidade"
                    name="capacidade"
                    min="1"
                    placeholder="Ex.: 20"
                >

            </div>


            <button
                type="submit"
                class="btn btn-cadastrar"
            >
                Cadastrar Espaço
            </button>

        </form>

    </div>


    <!-- ESPAÇOS -->

    <div class="grid">

        <?php foreach ($espacos as $espaco): ?>

            <div class="card">

                <h2>
                    <?= htmlspecialchars($espaco['nome']); ?>
                </h2>

                <div class="descricao">

                    <?= htmlspecialchars(
                        $espaco['descricao'] ?? 'Sem descrição.'
                    ); ?>

                </div>


                <?php if ($espaco['capacidade'] !== null): ?>

                    <span class="capacidade">
                        👥 Até <?= (int)$espaco['capacidade']; ?> pessoas
                    </span>

                <?php endif; ?>


                <br>


                <?php if ($espaco['ativo']): ?>

                    <span class="status ativo">
                        ● Ativo
                    </span>

                <?php else: ?>

                    <span class="status inativo">
                        ● Inativo
                    </span>

                <?php endif; ?>


                <div class="acoes-card">

                    <?php if ($espaco['ativo']): ?>

                        <a
                            href="../api/espacos.php?acao=desativar&id=<?= $espaco['id']; ?>"
                            class="btn btn-status"
                        >
                            Desativar
                        </a>

                    <?php else: ?>

                        <a
                            href="../api/espacos.php?acao=ativar&id=<?= $espaco['id']; ?>"
                            class="btn btn-status"
                        >
                            Ativar
                        </a>

                    <?php endif; ?>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</div>

</body>
</html>