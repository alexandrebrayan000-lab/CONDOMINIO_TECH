<?php
session_start();

// Detecta se a requisição é AJAX (fetch/XHR) para retornar JSON, caso contrário redireciona
$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false);

$response = ['status' => 'erro', 'mensagem' => 'Erro desconhecido'];

$host = 'localhost';
$usuario = 'root';
$senha = '';
$banco = 'condominio_tech';

$mysqli = new mysqli($host, $usuario, $senha, $banco);
if ($mysqli->connect_errno) {
    $response = ['status'=>'erro','mensagem'=>'Erro de conexão ao banco'];
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode($response); } else { header('Location: recuperar_enviada.php'); }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response = ['status'=>'erro','mensagem'=>'Método inválido'];
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode($response); } else { header('Location: recuperar_enviada.php'); }
    exit;
}

$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
if (!$email) {
    $response = ['status'=>'ok','mensagem'=>'Se o e‑mail existir, enviamos instruções.'];
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode($response); } else { header('Location: recuperar_enviada.php'); }
    exit;
}

// RATE LIMIT: registra e limita tentativas por IP e por e-mail (1 hora)
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$limitWindow = 3600; // segundos
$maxPerIp = 10; // limite por IP por janela
$maxPerEmail = 5; // limite por e-mail por janela

// cria tabela password_reset_requests caso não exista não-fatal (CREATE TABLE IF NOT EXISTS)
$mysqli->query("CREATE TABLE IF NOT EXISTS password_reset_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    ip VARCHAR(45) NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX(email), INDEX(ip), INDEX(created_at)
)");

// conta requisições recentes
$since = date('Y-m-d H:i:s', time() - $limitWindow);
$cntIpStmt = $mysqli->prepare('SELECT COUNT(*) AS c FROM password_reset_requests WHERE ip = ? AND created_at >= ?');
$cntIpStmt->bind_param('ss', $ip, $since);
$cntIpStmt->execute();
$cntIp = $cntIpStmt->get_result()->fetch_assoc()['c'] ?? 0;
$cntIpStmt->close();

$cntEmailStmt = $mysqli->prepare('SELECT COUNT(*) AS c FROM password_reset_requests WHERE email = ? AND created_at >= ?');
$cntEmailStmt->bind_param('ss', $email, $since);
$cntEmailStmt->execute();
$cntEmail = $cntEmailStmt->get_result()->fetch_assoc()['c'] ?? 0;
$cntEmailStmt->close();

if ($cntIp >= $maxPerIp || $cntEmail >= $maxPerEmail) {
    // registra a tentativa (para manter histórico) e responde genericamente
    $ins = $mysqli->prepare('INSERT INTO password_reset_requests (email, ip, created_at) VALUES (?, ?, ?)');
    $now = date('Y-m-d H:i:s');
    $ins->bind_param('sss', $email, $ip, $now);
    $ins->execute();
    $ins->close();

    $response = ['status'=>'ok','mensagem'=>'Se o e‑mail existir, enviamos instruções.'];
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode($response); } else { header('Location: recuperar_enviada.php'); }
    exit;
}

// registra a requisição atual
$ins = $mysqli->prepare('INSERT INTO password_reset_requests (email, ip, created_at) VALUES (?, ?, ?)');
$now = date('Y-m-d H:i:s');
$ins->bind_param('sss', $email, $ip, $now);
$ins->execute();
$ins->close();

// Gera token seguro e salva apenas o hash
$token = bin2hex(random_bytes(32));
$token_hash = hash('sha256', $token);
$expires = date('Y-m-d H:i:s', time() + 3600); // 1 hora

// Insere em tabela password_resets (crie se não existir)
$mysqli->query("CREATE TABLE IF NOT EXISTS password_resets (
    email VARCHAR(255) PRIMARY KEY,
    token_hash VARCHAR(128) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL
)");
$stmt = $mysqli->prepare("INSERT INTO password_resets (email, token_hash, expires_at, created_at) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE token_hash = VALUES(token_hash), expires_at = VALUES(expires_at), created_at = VALUES(created_at)");
$created_at = date('Y-m-d H:i:s');
$stmt->bind_param('ssss', $email, $token_hash, $expires, $created_at);
$stmt->execute();
$stmt->close();

// Monta link de reset baseado na localização atual
$host = $_SERVER['HTTP_HOST'];
$path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$link = sprintf('http://%s%s/reset_senha.php?token=%s&email=%s', $host, $path, $token, urlencode($email));

// Envio de e‑mail (use PHPMailer em produção). Aqui usamos mail() como fallback.
// Envio de e‑mail: tenta usar PHPMailer (via Composer autoload), senão usa mail()
$subject = 'Recuperação de senha - ReservAtiva';
$message = "Olá,\n\nRecebemos um pedido para redefinir sua senha. Clique no link abaixo (válido por 1 hora):\n\n" . $link . "\n\nSe você não pediu a recuperação, ignore este e‑mail.\n";

$sent = false;
$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
    if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
        try {
            $mail = new PHPMailer\\PHPMailer\\PHPMailer(true);
            // Ajuste abaixo com as credenciais SMTP reais
            $mail->isSMTP();
            $mail->Host = 'smtp.example.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'seu_usuario';
            $mail->Password = 'sua_senha';
            $mail->SMTPSecure = PHPMailer\\PHPMailer\\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('no-reply@' . $_SERVER['HTTP_HOST'], 'ReservAtiva');
            $mail->addAddress($email);
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->AltBody = $message;
            $mail->send();
            $sent = true;
        } catch (Exception $e) {
            // fallback para mail()
            $sent = false;
        }
    }
}

if (!$sent) {
    $headers = 'From: no-reply@' . $_SERVER['HTTP_HOST'] . "\r\n" . 'Content-Type: text/plain; charset=utf-8';
    @mail($email, $subject, $message, $headers);
}

// Resposta genérica para evitar vazamento de existência de e‑mail
$response = ['status'=>'ok','mensagem'=>'Se o e‑mail existir, enviamos instruções.'];

$mysqli->close();

if ($isAjax) {
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    header('Location: recuperar_enviada.php');
}

?>