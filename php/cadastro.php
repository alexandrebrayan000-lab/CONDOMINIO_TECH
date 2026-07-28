<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastre-se</title>
    <link rel="stylesheet" href="../css/cadastro.css">
    <link rel="icon" href="../img/logoReservaTech.png">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>
    <main>
    <div class="formulario">
        <figure>
            <figcaption><h2>Crie sua conta na ReservAtiva!</h2></figcaption>
            <form action="#" method="post" class="texto" id="form-cadastro">
                <label for="cadNome">Nome</label>
                <input type="text" id="cadNome" name="nome" required>

                <label for="cadEmail">E-mail</label>
                <input type="email" id="cadEmail" name="email" required>

                <label for="cadSenha">Senha</label>
                <div class="input-com-olho">
                    <input type="password" id="cadSenha" name="senha" required>
                    <button type="button" class="toggle-password" data-target="cadSenha" aria-label="Mostrar senha">
                        <svg class="icon-eye" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" stroke="#002A55" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="12" cy="12" r="3" stroke="#002A55" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>

                <div class="senha-forca" id="forcaSenha">
                    <div class="barra-forca"><div class="barra-fill" id="forcaFill" style="width: 0%;"></div></div>
                    <small id="forcaTexto"></small>
                </div>

                <label for="cadSenhaRep">Repetir senha</label>
                <div class="input-com-olho">
                    <input type="password" id="cadSenhaRep" name="senha_confirm" required>
                    <button type="button" class="toggle-password" data-target="cadSenhaRep" aria-label="Mostrar senha">
                        <svg class="icon-eye" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" stroke="#002A55" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="12" cy="12" r="3" stroke="#002A55" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>

                <small id="senhaErro" class="senha-erro" aria-live="polite"></small>

                <label for="cadBloco">N° bloco</label>
                <input type="number" id="cadBloco" name="bloco" required min="1" max="99">

                <label for="cadAp">N° App</label>                    
                <input type="number" id="cadAp" name="apartamento" required min="1" max="999">

                <div class="botoes-linha">
                    <button class="cadastro" type="submit">Cadastrar-se</button>
                    <a href="https://accounts.google.com" target="_blank" rel="noopener noreferrer" class="google-btn">
                        <img src="../img/logoGoogle.png" alt="Google" class="google-icon">
                        Google
                    </a>
                </div>
                <p><b>Após o cadastro você será redirecionado à tela de início.</b></p>
            </form>
        </figure>
    </div>
    </main>
    <footer>
        &copy; 2026 ReservAtiva. Todos os direitos reservados.
    </footer>
    <script src="../js/cadastro.js?v=3"></script>
    <script src="../js/session.js"></script>
</body>
</html>
