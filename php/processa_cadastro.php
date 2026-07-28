<?php
header('Content-Type: application/json');
session_start();

$host = "localhost";
$usuario = "root";
$senha = ""; 
$banco = "condominio_tech";

$conexao = new mysqli($host, $usuario, $senha, $banco);

if ($conexao->connect_error) {
    echo json_encode(["status" => "erro", "mensagem" => "Erro de conexão com o banco."]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim(strtolower($_POST['email'] ?? ''));
    $senha_pura = $_POST['senha'] ?? '';
    $senha_confirm = $_POST['senha_confirm'] ?? '';
    $bloco = $_POST['bloco'] ?? '';
    $apartamento = $_POST['apartamento'] ?? '';

    if (empty($nome) || empty($email) || empty($senha_pura) || empty($bloco) || empty($apartamento)) {
        echo json_encode(["status" => "erro", "mensagem" => "Preencha todos os campos do cadastro."]);
        exit;
    }

    if ($senha_pura !== $senha_confirm) {
        echo json_encode(["status" => "erro", "mensagem" => "As senhas não conferem."]);
        exit;
    }

    $stmt = $conexao->prepare('SELECT id FROM usuarios WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        echo json_encode([
            "status" => "erro",
            "mensagem" => "Este endereço de e-mail já está cadastrado!"
        ]);
        $stmt->close();
        exit;
    }
    $stmt->close();

    $senha_cripto = password_hash($senha_pura, PASSWORD_BCRYPT);

    $stmtSalva = $conexao->prepare(
        'INSERT INTO usuarios (nome, email, senha, bloco, apartamento) VALUES (?, ?, ?, ?, ?)'
    );
    $stmtSalva->bind_param('sssis', $nome, $email, $senha_cripto, $bloco, $apartamento);

    if ($stmtSalva->execute()) {
        // Recupera o ID do usuário recém-criado e inicia sessão (autologin)
        $usuario_id = $conexao->insert_id;
        $_SESSION['usuario_id'] = $usuario_id;
        $_SESSION['usuario_nome'] = $nome;
        $_SESSION['usuario_bloco'] = $bloco;
        $_SESSION['usuario_ap'] = $apartamento;

        $redirect = dirname($_SERVER['SCRIPT_NAME']) . '/reservas.php';
        echo json_encode([
            "status" => "sucesso",
            "mensagem" => "Conta criada com sucesso! Você foi autenticado.",
            "redirecionar" => $redirect
        ]);
    } else {
        echo json_encode([
            "status" => "erro",
            "mensagem" => "Erro ao criar conta: " . $stmtSalva->error
        ]);
    }

    $stmtSalva->close();
}
$conexao->close();
?>
