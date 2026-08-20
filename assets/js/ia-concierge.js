document.addEventListener('DOMContentLoaded', () => {
    const btnOpen = document.getElementById('btn-ia-open');
    const btnClose = document.getElementById('btn-ia-close');
    const chatBox = document.getElementById('ia-chat-box');
    const iaForm = document.getElementById('ia-form');
    const iaInput = document.getElementById('ia-input');
    const iaMessages = document.getElementById('ia-messages');

    // Toggle Chat visibility
    btnOpen.addEventListener('click', () => {
        chatBox.classList.toggle('hidden');
        if (!chatBox.classList.contains('hidden')) {
            iaInput.focus();
        }
    });

    btnClose.addEventListener('click', () => {
        chatBox.classList.add('hidden');
    });

    // Envioso de mensagens
    iaForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const texto = iaInput.value.trim();
        if (!texto) return;

        // Adiciona mensagem do usuário no chat
        appendMessage(texto, 'user');
        iaInput.value = '';

        // Indicador de "digitando..."
        const typingId = appendMessage('Pensando...', 'ia');

        try {
            const response = await fetch('../api/ia-chat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ pergunta: texto })
            });

            const data = await response.json();
            
            // Remove o "digitando..." e adiciona a resposta real
            document.getElementById(typingId).remove();
            appendMessage(data.resposta, 'ia');

        } catch (error) {
            document.getElementById(typingId).remove();
            appendMessage('Desculpe, tive um problema ao se conectar com o servidor. Tente novamente em instantes.', 'ia');
        }
    });

    function appendMessage(text, sender) {
        const msgDiv = document.createElement('div');
        const id = 'msg-' + Date.now();
        msgDiv.id = id;
        msgDiv.classList.add('msg', sender === 'user' ? 'msg-user' : 'msg-ia');
        msgDiv.innerHTML = text;
        iaMessages.appendChild(msgDiv);
        iaMessages.scrollTop = iaMessages.scrollHeight;
        return id;
    }
});