<?php
session_start();
require_once __DIR__ . '/../config/conexao.php';

// Apenas usuários com perfil 'sindico' podem acessar
if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_perfil'] ?? '') !== 'sindico') {
    header("Location: ../index.php");
    exit;
}

// 1. Contagem total de moradores
$total_moradores = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE perfil = 'morador'")->fetchColumn();

// 2. Total de reservas ativas (de hoje em diante)
$total_reservas = $pdo->query("SELECT COUNT(*) FROM reservas WHERE data_reserva >= CURDATE() AND status = 'confirmado'")->fetchColumn();

// 3. Últimas interações gravadas com a IA
$stmt_ia = $pdo->query("
    SELECT i.*, u.nome as usuario_nome 
    FROM ia_interacoes i 
    LEFT JOIN usuarios u ON i.usuario_id = u.id 
    ORDER BY i.criado_em DESC 
    LIMIT 5
");
$interacoes_ia = $stmt_ia->fetchAll();

// 4. Próximas reservas registradas
$stmt_res = $pdo->query("
    SELECT r.data_reserva, e.nome as espaco_nome, u.nome as usuario_nome, u.apartamento, u.bloco
    FROM reservas r
    JOIN espacos e ON r.espaco_id = e.id
    JOIN usuarios u ON r.usuario_id = u.id
    WHERE r.data_reserva >= CURDATE() AND r.status = 'confirmado'
    ORDER BY r.data_reserva ASC
    LIMIT 5
");
$reservas = $stmt_res->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel da Gestão | Condomínio Tech</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <style>
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1.5rem; }
        .header { margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; }
        .grid-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background: var(--bg-card); border: 1px solid var(--border-color); padding: 1.5rem; border-radius: var(--radius-md); }
        .stat-card h3 { font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem; }
        .stat-card .number { font-size: 2rem; font-weight: bold; color: var(--accent-blue); }
        
        .grid-sections { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        @media (max-width: 900px) { .grid-sections { grid-template-columns: 1fr; } }
        
        .card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem; }
        .card h2 { font-size: 1.1rem; margin-bottom: 1rem; color: var(--text-primary); }
        
        .table { width: 100%; border-collapse: collapse; font-size: 0.88rem; }
        .table th, .table td { padding: 0.75rem 0.5rem; text-align: left; border-bottom: 1px solid var(--border-color); }
        .table th { color: var(--text-secondary); font-weight: 500; }
        .badge { display: inline-block; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem; background: var(--bg-input); }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <div>
                <h1>⚡ Painel do Síndico</h1>
                <p style="color: var(--text-secondary);">Visão geral da administração e interações com moradores</p>
            </div>
            <a href="../index.php" style="color: var(--accent-blue); text-decoration: none;">← Voltar ao App</a>
        </div>

        <div class="grid-stats">
            <div class="stat-card">
                <h3>MORADORES CADASTRADOS</h3>
                <div class="number"><?= $total_moradores; ?></div>
            </div>
            <div class="stat-card">
                <h3>RESERVAS ATIVAS</h3>
                <div class="number"><?= $total_reservas; ?></div>
            </div>
            <div class="stat-card">
                <h3>SISTEMA IA</h3>
                <div class="number" style="color: var(--accent-green);">Ativo 24/7</div>
            </div>
        </div>

        <div class="grid-sections">
            <!-- Tabela: Histórico da IA Concierge -->
            <div class="card">
                <h2>🤖 Perguntas Recentes para a IA Concierge</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Morador</th>
                            <th>Pergunta / Intenção</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($interacoes_ia as $ia): ?>
                            <tr>
                                <td><b><?= htmlspecialchars($ia['usuario_nome'] ?? 'Visitante'); ?></b></td>
                                <td>
                                    <?= htmlspecialchars($ia['pergunta']); ?><br>
                                    <span class="badge"><?= htmlspecialchars($ia['intencao']); ?></span>
                                </td>
                                <td><?= date('d/m H:i', strtotime($ia['criado_em'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Tabela: Agenda de Reservas -->
            <div class="card">
                <h2>📅 Próximas Reservas de Espaços</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Espaço</th>
                            <th>Morador</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservas as $res): ?>
                            <tr>
                                <td><b><?= htmlspecialchars($res['espaco_nome']); ?></b></td>
                                <td><?= htmlspecialchars($res['usuario_nome']); ?> (<?= htmlspecialchars($res['bloco'] . '-' . $res['apartamento']); ?>)</td>
                                <td><?= date('d/m/Y', strtotime($res['data_reserva'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>