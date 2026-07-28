<?php
session_start();
$usuario_nome = $_SESSION['usuario_nome'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas - ReservAtiva</title>
    <link rel="stylesheet" href="../css/reservas.css">
    <link rel="icon" href="../img/logoReservaTech.png">
</head>
<body>
    <header class="header-unico">
        <div class="logo-container">
            <img src="../img/logoReservaTech.png" alt="ReservAtiva Logo" class="logo-img">
        </div>
        <h1 class="titulo-boas-vindas">Reservas</h1>
        <nav class="botoes-menu">
            <a href="../index.html" class="btn-nav btn-login">Início</a>
            <span id="sessionArea"></span>
        </nav>
    </header>
    
    <main>
        <?php if ($usuario_nome): ?>
            <p style="text-align:center; margin-top:12px; color:#ffffff; font-weight:700;">Olá, <?php echo htmlspecialchars($usuario_nome); ?>!</p>
        <?php endif; ?>
        <section class="opcoes">
            <div class="link-opcao reserva-item" data-resource="Salão de festas">
                <figure class="fig-inline">
                    <img src="../img/salaoFestas.jpeg" alt="salão de festas">
                    <figcaption class="legenda">
                        <h3>Salão de festas</h3><br>
                    </figcaption>
                </figure>
            </div>

            <div class="link-opcao reserva-item" data-resource="Campo society ou quadra">
                <figure class="fig-inline">
                    <img src="../img/quadraEsportiva.jpg" alt="quadra esportiva">
                    <figcaption class="legenda">
                        <h3>Campo society ou quadra</h3><br>
                    </figcaption>
                </figure>
            </div>

            <div class="link-opcao reserva-item" data-resource="Sala de jogos">
                <figure class="fig-inline">
                    <img src="../img/salaJogos.jpg" alt="sala de jogos">
                    <figcaption class="legenda">
                        <h3>Sala de jogos</h3><br>
                    </figcaption>
                </figure>
            </div>
        </section>

        <div class="modal-overlay" id="bookingModal" aria-hidden="true">
            <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
                <button class="modal-close" id="closeModal" aria-label="Fechar modal">×</button>
                <h2 id="modalTitle">Reservar</h2>
                <p class="modal-resource">Recurso: <strong id="resourceName"></strong></p>
                
                <form id="bookingForm">
                    <label for="bookingDate">Data</label>
                    <input type="date" id="bookingDate" name="bookingDate" required>

                    <label for="bookingInterval">Intervalo</label>
                    <select id="bookingInterval" name="bookingInterval">
                        <option value="30">30 minutos</option>
                        <option value="60">1 hora</option>
                    </select>

                    <label for="bookingTime">Horário</label>
                    <select id="bookingTime" name="bookingTime" required></select>

                    <label for="bookingPeople">Pessoas</label>
                    <input type="number" id="bookingPeople" name="bookingPeople" min="1" max="50" placeholder="Número de pessoas" required>

                    <label for="bookingName">Seu nome</label>
                    <input type="text" id="bookingName" name="bookingName" placeholder="Digite seu nome" value="<?php echo htmlspecialchars($usuario_nome); ?>" <?php echo $usuario_nome ? 'readonly' : ''; ?> required>

                    <button type="submit" class="btn-submit">Confirmar reserva</button>
                </form>
                <div class="booking-feedback" id="bookingFeedback"></div>
            </div>
        </div>
    </main>

    <footer>
        &copy; 2026 ReservAtiva. Todos os direitos reservados.
    </footer>

    <script src="../js/reservas.js"></script>
    <script src="../js/session.js"></script>
</body>
</html>
