<?php
session_start();
require_once __DIR__ . '/config/conexao.php';

// Redireciona para o login caso o morador não esteja autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: pages/login.php");
    exit;
}

$usuario_id   = $_SESSION['usuario_id'];
$usuario_nome = $_SESSION['usuario_nome'];
$bloco        = $_SESSION['usuario_bloco'] ?? 'N/A';
$apartamento  = $_SESSION['usuario_apto'] ?? 'N/A';

// Busca as próximas reservas confirmadas do usuário
$stmt_reservas = $pdo->prepare("
    SELECT r.data_reserva, e.nome AS espaco_nome 
    FROM reservas r
    JOIN espacos e ON r.espaco_id = e.id
    WHERE r.usuario_id = :uid AND r.data_reserva >= CURDATE() AND r.status = 'confirmado'
    ORDER BY r.data_reserva ASC
    LIMIT 3
");
$stmt_reservas->execute([':uid' => $usuario_id]);
$minhas_reservas = $stmt_reservas->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Morador | Condomínio Tech</title>
    <link rel="stylesheet" href="assets/css/global.css">
    <style>
        .navbar {
            background-color: var(--bg-card);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar .logo {
            font-size: 1.3rem;
            font-weight: bold;
        }
        .navbar .user-info {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            font-size: 0.9rem;
        }
        .btn-logout {
            color: #ef4444;
            text-decoration: none;
            font-weight: 500;
        }
        .dashboard-container {
            max-width: 1100px;
            margin: 2.5rem auto;
            padding: 0 1.5rem;
        }
        .welcome-card {
            background: linear-gradient(135deg, rgba(0, 180, 216, 0.15), rgba(15, 23, 42, 0.6));
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .welcome-card h1 {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        .grid-dashboard {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
        }
        @media (max-width: 768px) {
            .grid-dashboard { grid-template-columns: 1fr; }
        }
        .section-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 1.5rem;
        }
        .section-card h2 {
            font-size: 1.1rem;
            margin-bottom: 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 1rem;
        }
        .action-btn {
            background-color: var(--bg-input);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 1.2rem 1rem;
            text-align: center;
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }
        .action-btn:hover {
            border-color: var(--accent-blue);
            transform: translateY(-2px);
        }
        .action-btn span {
            font-size: 1.5rem;
        }
        .reserva-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.8rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        .reserva-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <nav class="navbar">
        <div class="logo">
            ✦ Condomínio<span style="color: var(--accent-blue);">Tech</span>
        </div>
        <div class="user-info">
            <span><b><?= htmlspecialchars($usuario_nome); ?></b> (<?= htmlspecialchars($bloco) . ' - ' . htmlspecialchars($apartamento); ?>)</span>
            <a href="api/logout.php" class="btn-logout">Sair ➔</a>
        </div>
    </nav>

    <div class="dashboard-container">
        
        <!-- Boas-vindas -->
        <div class="welcome-card">
            <h1>Olá, <?= htmlspecialchars($usuario_nome); ?>! 👋</h1>
            <p style="color: var(--text-secondary);">Seja bem-vindo ao seu ecossistema residencial. O que gostaria de fazer hoje?</p>
        </div>

        <div class="grid-dashboard">
            
            <!-- Ações Rápidas & Modulos principais -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <div class="section-card">
                    <h2>⚡ Acesso Rápido</h2>
                    <div class="quick-actions">
                        <a href="pages/reservas.php" class="action-btn">
                            <span>🗓️</span>
                            <small>Reservas</small>
                        </a>
                        <a href="#" onclick="alert('Módulo de 2ª via enviado para solicitação.'); return false;" class="action-btn">
                            <span>📄</span>
                            <small>2ª Via Boleto</small>
                        </a>
                        <a href="#" onclick="alert('Recurso em breve: Liberação de visitantes com QR Code!'); return false;" class="action-btn">
                            <span>🔑</span>
                            <small>Visitantes</small>
                        </a>
                        <a href="pages/feedback.php" class="action-btn">
                            <span>💬</span>
                            <small>Ocorrências</small>
                        </a>
                    </div>
                </div>

                <!-- Quadro de Avisos -->
                <div class="section-card">
                    <h2>📢 Avisos do Condomínio</h2>
                    <div style="font-size: 0.9rem; color: var(--text-secondary); line-height: 1.5;">
                        <p style="margin-bottom: 0.8rem;">• <b>Manutenção dos Elevadores:</b> Agendada para a próxima terça-feira das 09h às 12h.</p>
                        <p>• <b>Limpeza da Caixa d'Água:</b> Concluída com sucesso na última semana.</p>
                    </div>
                </div>
            </div>

            <!-- Coluna Lateral: Minhas Reservas -->
            <div class="section-card">
                <h2>📅 Suas Próximas Reservas</h2>
                <?php if (count($minhas_reservas) > 0): ?>
                    <?php foreach ($minhas_reservas as $res): ?>
                        <div class="reserva-item">
                            <div>
                                <strong><?= htmlspecialchars($res['espaco_nome']); ?></strong><br>
                                <small style="color: var(--text-secondary);">
                                    <?= date('d/m/Y', strtotime($res['data_reserva'])); ?>
                                </small>
                            </div>
                            <span style="color: var(--accent-green); font-size: 0.8rem;">Confirmado</span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: var(--text-secondary); font-size: 0.88rem;">Você não tem reservas agendadas no momento.</p>
                    <a href="pages/reservas.php" style="color: var(--accent-blue); font-size: 0.85rem; display: inline-block; margin-top: 1rem;">+ Agendar um espaço</a>
                <?php endif; ?>
            </div>

        </div>

    </div>

    <!-- Inclui o Chat Flutuante da IA Concierge -->
    <?php include 'includes/ia-widget.php'; ?>

</body>
</html>