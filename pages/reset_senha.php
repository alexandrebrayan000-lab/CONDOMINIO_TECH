<?php
session_start();
$host = 'localhost'; $usuario = 'root'; $senha = ''; $banco = 'condominio_tech';
$mysqli = new mysqli($host, $usuario, $senha, $banco);
if ($mysqli->connect_errno) {
    die('Erro de conexão');
}

// Se for POST, processa a troca de senha
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $token = $_POST['token'] ?? '';
    $nova = $_POST['senha'] ?? '';
    if (!$email || !$token || strlen($nova) < 6) {
        $error = 'Dados inválidos ou senha muito curta.';
    } else {
        $token_hash = hash('sha256', $token);
        $stmt = $mysqli->prepare('SELECT expires_at FROM password_resets WHERE email = ? AND token_hash = ?');
        $stmt->bind_param('ss', $email, $token_hash);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows === 1) {
            $row = $res->fetch_assoc();
            if (strtotime($row['expires_at']) >= time()) {
                // atualiza senha
                $hash = password_hash($nova, PASSWORD_DEFAULT);
                $u = $mysqli->prepare('UPDATE usuarios SET senha = ? WHERE email = ?');
                $u->bind_param('ss', $hash, $email);
                $u->execute();
                $u->close();
                // remove token
                $d = $mysqli->prepare('DELETE FROM password_resets WHERE email = ?');
                $d->bind_param('s', $email);
                $d->execute();
                $d->close();
                $success = 'Senha atualizada com sucesso. Você pode entrar agora.';
            } else {
                $error = 'Token expirado.';
            }
        } else {
            $error = 'Token inválido.';
        }
        $stmt->close();
    }
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Redefinir senha</title>
    <link rel="stylesheet" href="../css/cadastro.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>
    <main>
        <div class="formulario">
            <figure>
                <figcaption><h2>Redefinir senha</h2></figcaption>
                <?php if (!empty($error)): ?>
                    <p style="color:#d9534f;"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
                <?php if (!empty($success)): ?>
                    <p style="color:#5cb85c;"><?php echo htmlspecialchars($success); ?></p>
                <?php else: ?>
                <form method="post">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
                    <label for="email">E‑mail</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>" required>
                    <label for="senha">Nova senha</label>
                    <input type="password" id="senha" name="senha" required>
                    <div class="botoes-linha">
                        <button type="submit" class="cadastro">Atualizar senha</button>
                    </div>
                </form>
                <?php endif; ?>
            </figure>
        </div>
    </main>
    <script src="../js/session.js"></script>
</body>
</html>
<?php
session_start();

$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "condominio_tech";

$conexao = new mysqli($host, $usuario, $senha, $banco);
if ($conexao->connect_error) {
    die('Erro de conexão com o banco: ' . $conexao->connect_error);
}

$message = null;
$token = $_GET['token'] ?? '';
$validToken = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $novaSenha = $_POST['novaSenha'] ?? '';
    $confirmarSenha = $_POST['confirmarSenha'] ?? '';

    if (empty($token) || empty($novaSenha) || empty($confirmarSenha)) {
        $message = 'Preencha todos os campos para redefinir sua senha.';
    } elseif ($novaSenha !== $confirmarSenha) {
        $message = 'As senhas não coincidem.';
    } elseif (strlen($novaSenha) < 6) {
        $message = 'A senha precisa ter pelo menos 6 caracteres.';
    } else {
        $stmt = $conexao->prepare(
            'SELECT user_id FROM password_resets WHERE token = ? AND expires_at >= NOW()'
        );
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $registro = $resultado->fetch_assoc();
        $stmt->close();

        if ($registro) {
            $senhaHash = password_hash($novaSenha, PASSWORD_BCRYPT);
            $update = $conexao->prepare('UPDATE usuarios SET senha = ? WHERE id = ?');
            $update->bind_param('si', $senhaHash, $registro['user_id']);
            $update->execute();
            $update->close();

            $delete = $conexao->prepare('DELETE FROM password_resets WHERE user_id = ?');
            $delete->bind_param('i', $registro['user_id']);
            $delete->execute();
            $delete->close();

            $message = 'Senha atualizada com sucesso! Agora você pode fazer login.';
            $validToken = false;
        } else {
            $message = 'O token é inválido ou expirou. Solicite um novo link de recuperação.';
        }
    }
}

if (!$message && !empty($token)) {
    $stmt = $conexao->prepare(
        'SELECT user_id FROM password_resets WHERE token = ? AND expires_at >= NOW()'
    );
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $validToken = $resultado->num_rows === 1;
    $stmt->close();
}

$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha</title>
    <link rel="stylesheet" href="../css/cadastro.css">
    <link rel="icon" href="../img/logoReservaTech.png">
</head>
<body>
    <header class="header-unico">
        <div class="logo-container">
            <img src="../img/logoReservaTech.png" alt="logo da empresa" class="logo-img">
        </div>
        <h1 class="titulo-boas-vindas">Redefinir Senha</h1>
        <nav class="botoes-menu">
            <a href="../index.html" class="btn-nav btn-login">Início</a>
            <a href="senha.html" class="btn-nav btn-cadastro">Recuperar senha</a>
        </nav>
    </header>
    <main>
        <div class="formulario">
            <figure>
                <figcaption>
                    <h2>Redefina sua senha</h2>
                </figcaption>
                <?php if ($message): ?>
                    <p style="text-align:center; margin-bottom: 18px; color: #002A55; font-weight: 700;">
                        <?= htmlspecialchars($message) ?>
                    </p>
                <?php endif; ?>

                <?php if ($validToken): ?>
                    <form action="reset_senha.php" method="post" class="texto">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                        <label for="novaSenha">Nova senha</label>
                        <input type="password" id="novaSenha" name="novaSenha" required>

                        <label for="confirmarSenha">Confirmar senha</label>
                        <input type="password" id="confirmarSenha" name="confirmarSenha" required>

                        <div class="botoes-linha">
                            <button class="cadastro" type="submit">Atualizar senha</button>
                        </div>
                    </form>
                <?php else: ?>
                    <p style="text-align:center; margin-bottom: 18px; color: #444;">
                        Se você não recebeu ou o link expirou, solicite um novo em <a href="senha.html">Recuperar senha</a>.
                    </p>
                <?php endif; ?>
            </figure>
        </div>
    </main>
    <footer>
        &copy; 2026 ReservAtiva. Todos os direitos reservados.
    </footer>
</body>
</html>
