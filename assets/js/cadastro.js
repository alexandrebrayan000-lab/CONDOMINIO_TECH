document.addEventListener('DOMContentLoaded', () => {
    // Certifique-se de que o <form> do cadastro tenha o id="form-cadastro"
    const formCadastro = document.getElementById('form-cadastro');

    if (formCadastro) {
        formCadastro.addEventListener('submit', function(event) {
            event.preventDefault(); // Impede a página de recarregar
            // Validação: checar se as senhas conferem
            const senha = document.getElementById('cadSenha').value;
            const senhaRep = document.getElementById('cadSenhaRep').value;

            if (senha !== senhaRep) {
                alert('As senhas não conferem. Verifique e tente novamente.');
                return;
            }

            // Captura os dados digitados nos inputs
            const dadosFormulario = new FormData();
            dadosFormulario.append('nome', document.getElementById('cadNome').value);
            dadosFormulario.append('email', document.getElementById('cadEmail').value);
            dadosFormulario.append('senha', senha);
            dadosFormulario.append('senha_confirm', senhaRep);
            dadosFormulario.append('bloco', document.getElementById('cadBloco').value);
            dadosFormulario.append('apartamento', document.getElementById('cadAp').value);

            // Dispara para o PHP no Apache
            fetch('cadastrar.php', {
                method: 'POST',
                body: dadosFormulario
            })
            .then(async resposta => {
                const texto = await resposta.text();
                try {
                    return JSON.parse(texto);
                } catch (err) {
                    throw new Error('Resposta inválida do servidor: ' + texto);
                }
            })
            .then(dados => {
                alert(dados.mensagem); // Mostra o aviso de sucesso ou se o e-mail já existe
                
                if (dados.status === 'sucesso') {
                    // Usa o campo 'redirecionar' enviado pelo backend, se houver
                    // limpa campos de senha da memória
                    const cadSenhaEl = document.getElementById('cadSenha');
                    const cadSenhaRepEl = document.getElementById('cadSenhaRep');
                    if (cadSenhaEl) cadSenhaEl.value = '';
                    if (cadSenhaRepEl) cadSenhaRepEl.value = '';

                    if (dados.redirecionar) {
                        window.location.href = dados.redirecionar;
                    } else {
                        window.location.href = '../index.html';
                    }
                }
            })
            .catch(erro => {
                console.error('Erro no cadastro:', erro);
                alert('Erro ao comunicar com o servidor local: ' + (erro.message || erro));
            });
        });
    }

    // Função para alternar visibilidade das senhas (olhinho) e atualizar ícone SVG
    const eyeOpenSVG = '<svg class="icon-eye" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">\n                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" stroke="#002A55" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>\n                            <circle cx="12" cy="12" r="3" stroke="#002A55" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>\n                        </svg>';
    const eyeClosedSVG = '<svg class="icon-eye" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">\n                            <path d="M17.94 17.94A10.94 10.94 0 0112 19c-7 0-11-7-11-7a20.52 20.52 0 014.06-5.17" stroke="#002A55" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>\n                            <path d="M1 1l22 22" stroke="#002A55" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>\n                        </svg>';

    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', () => {
            const targetId = btn.dataset.target;
            const input = document.getElementById(targetId);
            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                btn.innerHTML = eyeClosedSVG;
                btn.setAttribute('aria-label', 'Ocultar senha');
            } else {
                input.type = 'password';
                btn.innerHTML = eyeOpenSVG;
                btn.setAttribute('aria-label', 'Mostrar senha');
            }
        });
    });

    // Mensagem inline quando senhas não conferem
    const senhaErroEl = document.getElementById('senhaErro');
    const cadSenha = document.getElementById('cadSenha');
    const cadSenhaRep = document.getElementById('cadSenhaRep');

    function checarSenhasInline() {
        if (!cadSenha || !cadSenhaRep) return;
        if (cadSenhaRep.value === '') {
            senhaErroEl.textContent = '';
            return;
        }
        if (cadSenha.value !== cadSenhaRep.value) {
            senhaErroEl.textContent = 'As senhas não conferem.';
        } else {
            senhaErroEl.textContent = '';
        }
    }

    cadSenhaRep && cadSenhaRep.addEventListener('input', checarSenhasInline);
    cadSenha && cadSenha.addEventListener('input', () => { checarSenhasInline(); atualizarForca(cadSenha.value); });

    // Indicador de força da senha
    const forcaFill = document.getElementById('forcaFill');
    const forcaTexto = document.getElementById('forcaTexto');

    function avaliarForca(senha) {
        let score = 0;
        if (!senha) return score;
        if (senha.length >= 8) score += 2;
        if (/[0-9]/.test(senha)) score += 1;
        if (/[a-z]/.test(senha) && /[A-Z]/.test(senha)) score += 1;
        if (/[^A-Za-z0-9]/.test(senha)) score += 1;
        if (senha.length >= 12) score += 1;
        return score; // 0..6
    }

    function atualizarForca(senha) {
        const score = avaliarForca(senha);
        const percent = Math.min(100, Math.round((score / 6) * 100));
        if (forcaFill) {
            forcaFill.style.width = percent + '%';
            forcaFill.style.background = score <= 2 ? '#d9534f' : (score <= 4 ? '#f0ad4e' : '#5cb85c');
        }
        if (forcaTexto) {
            if (!senha) forcaTexto.textContent = '';
            else if (score <= 2) forcaTexto.textContent = 'Fraca';
            else if (score <= 4) forcaTexto.textContent = 'Média';
            else forcaTexto.textContent = 'Forte';
        }
    }

    // Inicializa estado do ícone (abrir olho) para todos os botões
    document.querySelectorAll('.toggle-password').forEach(btn => btn.innerHTML = eyeOpenSVG);
});