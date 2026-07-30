document.addEventListener('DOMContentLoaded', () => {
    // Certifique-se de que o <form> da sua caixa de login tenha o id="form-login"
    const formLogin = document.getElementById('form-login');

    if (formLogin) {
        formLogin.addEventListener('submit', function(event) {
            event.preventDefault();

            const dadosFormulario = new FormData();
            dadosFormulario.append('email', document.getElementById('loginEmail').value);
            dadosFormulario.append('senha', document.getElementById('loginSenha').value);

            const loginUrl = window.location.pathname.includes('/php/') ? 'login.php' : 'php/login.php';
            fetch(loginUrl, {
                method: 'POST',
                body: dadosFormulario
            })
            .then(resposta => resposta.json())
            .then(dados => {
                if (dados.status === 'sucesso') {
                    // Se o login for autorizado, muda de página automaticamente para onde ocorrem as reservas
                    window.location.href = dados.redirecionar; 
                } else {
                    // Mostra o erro "E-mail ou senha incorretos"
                    alert(dados.mensagem); 
                }
            })
            .catch(erro => {
                console.error('Erro no login:', erro);
                alert('Erro de ligação com o servidor local.');
            });
        });
    }
});