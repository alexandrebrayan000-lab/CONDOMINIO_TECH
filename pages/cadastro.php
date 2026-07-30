<?php
session_start();
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
    <title>Cadastre sua Unidade | Condomínio Tech</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <style>
        .cadastro-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
        }
        .cadastro-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 2.5rem;
            width: 100%;
            max-width: 500px;
            box-shadow: var(--shadow);
        }
        .cadastro-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .form-group {
            margin-bottom: 1.1rem;
        }
        .form-group.full {
            grid-column: span 2;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.4rem;
            font-size: 0.85rem;
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
        .form-group input:focus, .form-group select:focus {
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
            grid-column: span 2;
        }
        .btn-submit:hover {
            background-color: var(--accent-hover);
        }
        .cadastro-footer {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.875rem;
            color: var(--text-secondary);
        }
        .alert-message {
            padding: 0.75rem;
            border-radius: var(--radius-sm);
            font-size: 0.875rem;
            margin-bottom: 1.25rem;
            text-align: center;
        }
        .alert-error {
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid #ef4444;
            color: #f87171;
        }
        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid var(--accent-green);
            color: var(--accent-green);
        }
    </style>
</head>
<body>

    <div class="cadastro-container">
        <div class="cadastro-card">
            <div class="cadastro-header">
                <h1>✦ Criar Conta</h1>
                <p style="color: var(--text-secondary); font-size: 0.9rem;">Cadastre-se para acessar o ecossistema</p>
            </div>

            <?php if (isset($_GET['erro'])): ?>
                <div class="alert-message alert-error">
                    <?php 
                        if($_GET['erro'] == 'email_existente') echo 'Este e-mail já está cadastrado!';
                        elseif($_GET['erro'] == 'campos_vazios') echo 'Por favor, preencha todos os campos obrigatórios.';
                        else echo 'Ocorreu um erro. Tente novamente.';
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['sucesso'])): ?>
                <div class="alert-message alert-success">
                    Cadastro realizado com sucesso! <a href="login.php" style="color: var(--accent-green); text-decoration: underline;">Clique para entrar</a>.
                </div>
            <?php endif; ?>

            <form action="../api/processa_cadastro.php" method="POST" class="form-grid">
                <div class="form-group full">
                    <label for="nome">Nome Completo</label>
                    <input type="text" id="nome" name="nome" placeholder="Ex: Maria Silva" required>
                </div>

                <div class="form-group full">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" placeholder="seuemail@condominio.com" required>
                </div>

                <div class="form-group">
                    <label for="bloco">Bloco / Torre</label>
                    <input type="text" id="bloco" name="bloco" placeholder="Ex: Bloco A">
                </div>

                <div class="form-group">
                    <label for="apartamento">Apartamento / Unidade</label>
                    <input type="text" id="apartamento" name="apartamento" placeholder="Ex: Apt 102">
                </div>

                <div class="form-group full">
                    <label for="perfil">Perfil de Acesso</label>
                    <select id="perfil" name="perfil" required>
                        <option value="morador">Morador / Unidade</option>
                        <option value="sindico">Síndico / Gestão</option>
                        <option value="porteiro">Portaria / Segurança</option>
                        <option value="zelador">Zeladoria / Manutenção</option>
                    </select>
                </div>

                <div class="form-group full">
                    <label for="senha">Senha de Acesso</label>
                    <input type="password" id="senha" name="senha" placeholder="Mínimo 6 caracteres" required>
                </div>

                <button type="submit" class="btn-submit">Concluir Cadastro</button>
            </form>

            <div class="cadastro-footer">
                Já possui uma conta? <a href="login.php">Fazer Login</a>
            </div>
        </div>
    </div>

</body>
</html>