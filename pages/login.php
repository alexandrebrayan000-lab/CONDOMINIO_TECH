<?php
session_start();
// Se o usuário já estiver logado, redireciona para a página principal
if (isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Condomínio Tech</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .login-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            box-shadow: var(--shadow);
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header h1 {
            font-size: 1.6rem;
            margin-bottom: 0.5rem;
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
        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            background-color: var(--bg-input);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-size: 0.95rem;
            outline: none;
        }
        .form-group input:focus {
            border-color: var(--accent-blue);
        }
        .btn-submit {
            width: 100%;
            padding: 0.85rem;
            background-color: var(--accent-blue);
            color: #000;
            font-weight: 700;
            border-radius: var(--radius-sm);
            font-size: 1rem;
            margin-top: 0.5rem;
        }
        .btn-submit:hover {
            background-color: var(--accent-hover);
        }
        .login-footer {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.875rem;
            color: var(--text-secondary);
        }
        .alert-error {
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid #ef4444;
            color: #f87171;
            padding: 0.75rem;
            border-radius: var(--radius-sm);
            font-size: 0.875rem;
            margin-bottom: 1.25rem;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>✦ Condomínio<span style="color: var(--accent-blue);">Tech</span></h1>
                <p style="color: var(--text-secondary); font-size: 0.9rem;">Acesse seu ecossistema inteligente</p>
            </div>

            <?php if (isset($_GET['erro'])): ?>
                <div class="alert-error">
                    E-mail ou senha incorretos!
                </div>
            <?php endif; ?>

            <form action="../api/auth.php" method="POST">
                <div class="form-group">
                    <label for="email">E-mail cadastrado</label>
                    <input type="email" id="email" name="email" placeholder="seu@email.com" required>
                </div>

                <div class="form-group">
                    <label for="senha">Senha de acesso</label>
                    <input type="password" id="senha" name="senha" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn-submit">Acessar Plataforma</button>
            </form>

            <div class="login-footer">
                Não tem uma conta? <a href="cadastro.php">Cadastrar unidade</a><br>
                <a href="recuperar_senha.php" style="font-size: 0.8rem; margin-top: 0.5rem; display: inline-block;">Esqueci minha senha</a>
            </div>
        </div>
    </div>

</body>
</html>