<?php
session_start();
require_once __DIR__ . '/../config/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ocorrências e Chamados | Condomínio Tech</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <style>
        .form-container { max-width: 600px; margin: 3rem auto; padding: 2rem; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); }
        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; margin-bottom: 0.4rem; font-size: 0.85rem; color: var(--text-secondary); }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; padding: 0.75rem 1rem; background: var(--bg-input); border: 1px solid var(--border-color);
            border-radius: var(--radius-sm); color: var(--text-primary); font-size: 0.95rem; outline: none;
        }
        .btn-submit { width: 100%; padding: 0.85rem; background: var(--accent-blue); color: #000; font-weight: bold; border-radius: var(--radius-sm); cursor: pointer; }
        .btn-submit:hover { background: var(--accent-hover); }
        .alert-success { padding: 0.75rem; background: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); color: var(--accent-green); border-radius: var(--radius-sm); margin-bottom: 1rem; text-align: center; }
    </style>
</head>
<body>

    <div class="form-container">
        <h1 style="font-size: 1.5rem; margin-bottom: 0.5rem;">💬 Registrar Ocorrência ou Chamado</h1>
        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1.5rem;">Envie relatos sobre barulho, pedidos de manutenção ou sugestões diretamente ao síndico.</p>

        <?php if (isset($_GET['sucesso'])): ?>
            <div class="alert-success">Sua ocorrência foi enviada com sucesso para a gestão!</div>
        <?php endif; ?>

        <form action="../api/processa_feedback.php" method="POST">
            <div class="form-group">
                <label for="titulo">Assunto / Título</label>
                <input type="text" id="titulo" name="titulo" placeholder="Ex: Barulho após às 22h" required>
            </div>

            <div class="form-group">
                <label for="categoria">Categoria</label>
                <select id="categoria" name="categoria" required>
                    <option value="barulho">Barulho / Perturbação</option>
                    <option value="manutencao">Manutenção / Reparo</option>
                    <option value="sugestao">Sugestão / Elogio</option>
                    <option value="outro">Outro assunto</option>
                </select>
            </div>

            <div class="form-group">
                <label for="descricao">Descrição detalhada</label>
                <textarea id="descricao" name="descricao" rows="4" placeholder="Descreva com detalhes o ocorrido..." required></textarea>
            </div>

            <button type="submit" class="btn-submit">Enviar Registro</button>
        </form>

        <a href="../index.php" style="display: block; text-align: center; margin-top: 1.5rem; color: var(--text-secondary); text-decoration: none; font-size: 0.88rem;">← Voltar ao painel inicial</a>
    </div>

    <?php include '../includes/ia-widget.php'; ?>

</body>
</html>