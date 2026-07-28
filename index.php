<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>ReservAtiva</title>

    <!-- Carrega variáveis CSS do tema + JS global -->
    <?php include 'includes/head.php'; ?>

    <link rel="stylesheet" href="css/index.css">
    <link rel="icon" href="img/logoReservaTech.png">
</head>
<body>

    <!-- Header modular com o botão de tema escuro/claro incluído -->
    <?php include 'includes/header.php'; ?>

    <main>
        <div class="texto">
            <h2>Reserve seu horário de forma fácil e rápida com a ReservAtiva!</h2>
        </div><br>

        <div class="reservas">
            <a href="#login">
                <figure class="fig-inline">
                    <img src="img/salaoFestas.jpeg" alt="salão de festas">
                    <figcaption class="legenda">
                        <h3>Salão de festas</h3><br>
                        <p>Perfeito para celebrações, aniversários e eventos especiais.</p>
                    </figcaption>
                </figure>
            </a>
            <a href="#login">
                <figure class="fig-inline">
                    <img src="img/quadraEsportiva.jpg" alt="quadra esportiva">
                    <figcaption class="legenda">
                        <h3>Campo society ou quadra</h3><br>
                        <p>Ideal para jogos de futebol, vôlei, basquete e outras atividades esportivas.</p>
                    </figcaption>
                </figure>
            </a>
        </div>

        <div class="reservas">
            <a href="#login">
                <figure class="fig-inline">
                    <img src="img/salaJogos.jpg" alt="sala de reunião">
                    <figcaption class="legenda">
                        <h3>Sala de jogos</h3><br>
                        <p>Excelente local para passar seu tempo e levar os filhos.</p>
                    </figcaption>
                </figure>
            </a>    
        </div>

        <section id="login">
            <figure>
                <figcaption><h2>Fazer login</h2><br></figcaption>

                <form id="form-login" action="php/login.php" method="post">
                    <div class="campo-grupo">
                        <label for="loginEmail">E-mail</label>
                        <input type="email" id="loginEmail" name="email" required>
                    </div>

                    <div class="campo-grupo">
                        <label for="loginSenha">Senha</label>
                        <input type="password" id="loginSenha" name="senha" required>
                    </div>

                    <div class="botoes-linha">
                        <button type="submit" class="cadastro">Entrar</button>
                        <a href="https://accounts.google.com" target="_blank" rel="noopener noreferrer" class="google">
                            <img src="img/logoGoogle.png" alt="Google" class="google">
                        </a>
                    </div>

                    <p class="ajuda" style="margin-top: 15px;">
                        Não tem conta? <a href="php/cadastro.php">Crie sua conta</a> · <a href="php/recuperar_senha.php">Esqueci minha senha</a>
                    </p>
                </form>
            </figure>
        </section>

        <section class="sobre">
            <button class="sobreSite">
                <!-- Apontando para sobre.php que organizamos -->
                <a href="pages/sobre.php">𝒊</a>
            </button>
            <button class="feedback">
                <a href="pages/feedback.html">✉</a>
            </button>
        </section>    
    </main>

    <footer>
        &copy; 2026 ReservAtiva. Todos os direitos reservados.
    </footer> 

    <script src="js/login.js"></script>
    <script src="js/session.js"></script>
</body>
</html>