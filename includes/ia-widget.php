<!-- Widget Flutuante da IA Concierge -->
<div id="ia-widget-container">
    <button id="btn-ia-open" aria-label="Abrir assistente virtual">
        🤖
    </button>

    <div id="ia-chat-box" class="hidden">
        <div class="ia-header">
            <div class="ia-title">
                <span class="status-dot"></span>
                <div>
                    <strong>IA Concierge</strong>
                    <small>Atendimento Inteligente 24/7</small>
                </div>
            </div>
            <button id="btn-ia-close">&times;</button>
        </div>

        <div id="ia-messages">
            <div class="msg msg-ia">
                Olá! Sou o assistente virtual do seu condomínio. Como posso te ajudar hoje? (Ex: <i>"Tenho encomendas?"</i>, <i>"Quero reservar a churrasqueira"</i>, <i>"2ª via do boleto"</i>)
            </div>
        </div>

        <form id="ia-form">
            <input type="text" id="ia-input" placeholder="Digite sua mensagem..." autocomplete="off" required>
            <button type="submit" id="ia-send-btn">➔</button>
        </form>
    </div>
</div>

<link rel="stylesheet" href="<?php echo BASE_URL ?? ''; ?>assets/css/ia-widget.css">
<script src="<?php echo BASE_URL ?? ''; ?>assets/js/ia-concierge.js" defer></script>