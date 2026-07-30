<?php
session_start();
require_once __DIR__ . '/../config/conexao.php';

// Proteção: exige login para acessar
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Busca os espaços disponíveis no banco
$stmt = $pdo->query("SELECT * FROM espacos WHERE status = 'disponivel'");
$espacos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva de Espaços | Condomínio Tech</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <style>
        .page-container {
            max-width: 1100px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }
        .page-header {
            margin-bottom: 2rem;
        }
        .grid-espacos {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
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
        }
        .card-espaco h3 {
            color: var(--accent-blue);
            margin-bottom: 0.5rem;
        }
        .card-espaco p {
            color: var(--text-secondary);
            font-size: 0.9rem;
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
            margin-bottom: 1rem;
        }
        .form-reserva {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 2rem;
            max-width: 600px;
            margin: 0 auto;
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
        .form-group input, .form-group select {
            width: 100%;
            padding: 0.75rem 1rem;
            background-color: var(--bg-input);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-size: 0.95rem;
            outline: none;
        }
        .btn-submit {
            width: 100%;
            padding: 0.85rem;
            background-color: var(--accent-blue);
            color: #000;
            font-weight: 700;
            border-radius: var(--radius-sm);
            font-size: 1rem;
            cursor: pointer;
        }
        .btn-submit:hover {
            background-color: var(--accent-hover);
        }
        .alert {
            padding: 0.75rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .alert-success { background: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); color: var(--accent-green); }
        .alert-error { background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #f87171; }
    </style>
</head>
<body>

    <div class="page-container">
        <div class="page-header">
            <h1>🗓️ Agendamento de Espaços</h1>
            <p style="color: var(--text-secondary);">Escolha a área comum ideal para o seu evento e garanta sua reserva.</p>
        </div>

        <?php if (isset($_GET['sucesso'])): ?>
            <div class="alert alert-success">Reserva realizada com sucesso!</div>
        <?php endif; ?>

        <?php if (isset($_GET['erro'])): ?>
            <div class="alert alert-error">
                <?php 
                    if ($_GET['erro'] == 'data_ocupada') echo "Este espaço já está reservado na data selecionada.";
                    else echo "Preencha todos os campos corretamente.";
                ?>
            </div>
        <?php endif; ?>

        <div class="grid-espacos">
            <?php foreach ($espacos as $espaco): ?>
                <div class="card-espaco">
                    <div>
                        <h3><?= htmlspecialchars($espaco['nome']); ?></h3>
                        <p><?= htmlspecialchars($espaco['descricao']); ?></p>
                    </div>
                    <div>
                        <span class="capacidade-badge">👥 Até <?= $espaco['capacidade']; ?> pessoas</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Formulário de Agendamento -->
        <div class="form-reserva">
            <h2 style="margin-bottom: 1.5rem; font-size: 1.3rem;">Solicitar Agendamento</h2>
            <form action="../api/reservas.php" method="POST">
                <div class="form-group">
                    <label for="espaco_id">Selecione o Espaço</label>
                    <select id="espaco_id" name="espaco_id" required>
                        <option value="">-- Escolha uma opção --</option>
                        <?php foreach ($espacos as $espaco): ?>
                            <option value="<?= $espaco['id']; ?>"><?= htmlspecialchars($espaco['nome']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="data_reserva">Data da Reserva</label>
                    <input type="date" id="data_reserva" name="data_reserva" min="<?= date('Y-m-d'); ?>" required>
                </div>

                <button type="submit" class="btn-submit">Confirmar Agendamento</button>
            </form>
        </div>
    </div>

    <!-- Inclui o Widget da IA Concierge em todas as páginas do sistema -->
    <?php include '../includes/ia-widget.php'; ?>

</body>
</html>