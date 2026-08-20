<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/conexao.php';

$pergunta = trim($_POST['pergunta'] ?? '');
$usuario_id = $_SESSION['usuario_id'] ?? null;
$nome_usuario = $_SESSION['usuario_nome'] ?? 'morador';

if (empty($pergunta)) {
    echo json_encode(['resposta' => 'Por favor, digite alguma dúvida ou solicitação.']);
    exit;
}

$pergunta_lc = mb_strtolower($pergunta, 'UTF-8');
$resposta = '';
$intencao = 'geral';

// Motor de Intenções (Exemplo Prático de IA de Atendimento)
if (strpos($pergunta_lc, 'boleto') !== false || strpos($pergunta_lc, 'fatura') !== false || strpos($pergunta_lc, 'pagamento') !== false) {
    $intencao = 'financeiro';
    $resposta = "Olá, <b>{$nome_usuario}</b>! O boleto da sua taxa condominial deste mês está disponível. Você pode baixar a 2ª via atualizada com código de barras diretamente na área financeira ou solicitar via PIX com desconto até o vencimento.";
} 
elseif (strpos($pergunta_lc, 'encomenda') !== false || strpos($pergunta_lc, 'pacote') !== false || strpos($pergunta_lc, 'correio') !== false) {
    $intencao = 'portaria';
    $resposta = "Verifiquei no sistema da portaria: no momento não há novas encomendas pendentes de retirada para a sua unidade. Quando chegar um pacote, você receberá uma notificação instantânea!";
}
elseif (strpos($pergunta_lc, 'reserva') !== false || strpos($pergunta_lc, 'churrasqueira') !== false || strpos($pergunta_lc, 'salão') !== false || strpos($pergunta_lc, 'quadra') !== false) {
    $intencao = 'reservas';
    $resposta = "Você pode agendar o Salão de Festas, Quadra ou Espaço Gamer diretamente pela aba de <a href='../pages/reservas.php' style='color: var(--accent-blue); text-decoration: underline;'>Reservas de Espaços</a>. Deseja verificar datas disponíveis?";
}
elseif (strpos($pergunta_lc, 'visitante') !== false || strpos($pergunta_lc, 'qr code') !== false || strpos($pergunta_lc, 'convite') !== false) {
    $intencao = 'acesso';
    $resposta = "Para cadastrar um visitante, basta gerar um QR Code de acesso temporário na opção 'Controle de Visitantes' do aplicativo. O porteiro receberá a liberação automaticamente.";
}
elseif (strpos($pergunta_lc, 'barulho') !== false || strpos($pergunta_lc, 'reclamar') !== false || strpos($pergunta_lc, 'ocorrencia') !== false) {
    $intencao = 'zeladoria';
    $resposta = "Sua mensagem sobre ocorrências foi registrada. O horário de silêncio do condomínio é das 22h às 08h. Se precisar abrir um chamado oficial para a gestão, use o menu 'Ocorrências'.";
}
else {
    $resposta = "Entendi sua mensagem sobre <i>'" . htmlspecialchars($pergunta) . "'</i>. Como sou o assistente digital, posso te ajudar a emitir boletos, conferir encomendas, agendar churrasqueira ou liberar visitantes. Como prefere prosseguir?";
}

// Salva a interação na tabela de logs da IA
try {
    $stmt = $pdo->prepare("INSERT INTO ia_interacoes (usuario_id, pergunta, resposta_ia, intencao) VALUES (:uid, :perg, :resp, :inte)");
    $stmt->execute([
        ':uid' => $usuario_id,
        ':perg' => $pergunta,
        ':resp' => $resposta,
        ':inte' => $intencao
    ]);
} catch (Exception $e) {
    // Silencioso em produção para não quebrar a resposta
}

echo json_encode([
    'resposta' => $resposta,
    'intencao' => $intencao
]);