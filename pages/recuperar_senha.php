<?php
// Formulário simples para solicitar recuperação de senha
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Recuperar senha</title>
    <link rel="stylesheet" href="../css/cadastro.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>
    <main>
        <div class="formulario">
            <figure>
                <figcaption><h2>Recuperar senha</h2></figcaption>
                <p>Digite seu e‑mail e, se existir, você receberá instruções para redefinir sua senha.</p>
                <form action="enviar_recuperacao.php" method="post">
                    <label for="email">E‑mail</label>
                    <input type="email" id="email" name="email" required>
                    <div class="botoes-linha">
                        <button type="submit" class="cadastro">Enviar instruções</button>
                    </div>
                </form>
            </figure>
        </div>
    </main>
    <script src="../js/session.js"></script>
</body>
</html>
<?php
// Processamento de pedido de redefinição de senha
$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "condominio_tech";

$conexao = new mysqli($host, $usuario, $senha, $banco);
if ($conexao->connect_error) {
    die('Erro de conexão com o banco: ' . $conexao->connect_error);
}

$conexao->query(
    "CREATE TABLE IF NOT EXISTS password_resets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        token VARCHAR(64) NOT NULL,
        expires_at DATETIME NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX(user_id),
        INDEX(token)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
);

$message = null;
$showLink = false;
$resetLink = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim(strtolower($_POST['email'] ?? ''));

    if (empty($email)) {
        $message = 'Informe o e-mail cadastrado para recuperação de senha.';
    } else {
        $stmt = $conexao->prepare('SELECT id, nome FROM usuarios WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();
        $stmt->close();

        // Mensagem genérica para segurança
        $message = 'Se este e-mail estiver cadastrado, você receberá um link para redefinir sua senha.';

        if ($usuario) {
            $token = bin2hex(random_bytes(16));
            $expiresAt = date('Y-m-d H:i:s', time() + 3600);

            $stmtInsert = $conexao->prepare(
                'INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)'
            );
            $stmtInsert->bind_param('iss', $usuario['id'], $token, $expiresAt);
            $stmtInsert->execute();
            $stmtInsert->close();

            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $path = dirname($_SERVER['PHP_SELF']);
            $resetLink = sprintf('%s://%s%s/reset_senha.php?token=%s', $scheme, $host, $path, urlencode($token));
            $showLink = true;

            $subject = 'Redefinição de senha - ReservAtiva';
            $body = "Olá {$usuario['nome']},\n\nPara redefinir sua senha, acesse o link abaixo:\n{$resetLink}\n\nEste link expira em 1 hora.\n\nSe você não solicitou essa redefinição, ignore esta mensagem.\n";
            $headers = 'From: no-reply@reservativa.local' . "\r\n";

            @mail($email, $subject, $body, $headers);
        }
    }
}

$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperação de Senha</title>
    <link rel="stylesheet" href="../css/cadastro.css">
    <link rel="icon" href="../img/logoReservaTech.png">
</head>
<body>
    <header class="header-unico">
        <div class="logo-container">
            <img src="../img/logoReservaTech.png" alt="logo da empresa" class="logo-img">
        </div>
        <h1 class="titulo-boas-vindas">Recuperação de Senha</h1>
        <nav class="botoes-menu">
            <a href="../index.html" class="btn-nav btn-login">Início</a>
            <a href="senha.html" class="btn-nav btn-cadastro">Voltar</a>
        </nav>
    </header>
    <main>
        <div class="formulario">
            <figure>
                <figcaption>
                    <h2>Pedido de redefinição enviado</h2>
                </figcaption>
                <p style="text-align:center; margin-bottom: 18px; color: #002A55; font-weight: 700;">
                    <?= htmlspecialchars($message ?? 'Preencha o formulário para recuperar sua senha.') ?>
                </p>
                <?php if ($showLink && $resetLink): ?>
                    <div style="background:#f4f8ff; border:1px solid #c6d7f5; padding:16px; border-radius:12px; margin-bottom:18px; word-break:break-word; font-family:Arial, sans-serif; color:#0a2f60;">
                        <p>Link de redefinição local (teste):</p>
                        <a href="<?= htmlspecialchars($resetLink) ?>" style="color:#005cbf; text-decoration:none;"><?= htmlspecialchars($resetLink) ?></a>
                    </div>
                <?php endif; ?>
                <div class="botoes-linha">
                    <a href="senha.html" class="cadastro" style="text-align:center; display:inline-flex; width:auto;">Voltar à recuperação</a>
                </div>
            </figure>
        </div>
    </main>
    <footer>
        &copy; 2026 ReservAtiva. Todos os direitos reservados.
    </footer>
</body>
</html>
