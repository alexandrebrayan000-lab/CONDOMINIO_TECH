/* ===================================================
   GERENCIADOR DE TEMA GLOBAL (CLARO / ESCURO)
   =================================================== */

// 1. Aplicação imediata do tema (executa antes da árvore DOM desenhar)
(function carregarTemaSalvo() {
  const temaSalvo = localStorage.getItem('theme');
  if (temaSalvo === 'dark') {
    document.documentElement.classList.add('dark-theme');
  }
})();

// 2. Vincula os eventos após o carregamento dos elementos na tela
document.addEventListener('DOMContentLoaded', () => {
  inicializarBotaoTema();
});

/**
 * Configura o botão de alternância do tema escuro/claro
 */
function inicializarBotaoTema() {
  const toggleBtn = document.getElementById('theme-toggle');

  if (!toggleBtn) return;

  // Atualiza o texto do botão com base no estado inicial do tema
  const estaEscuro = document.documentElement.classList.contains('dark-theme');
  atualizarTextoBotao(toggleBtn, estaEscuro);

  // Escuta o clique para alternar o tema
  toggleBtn.addEventListener('click', () => {
    // Alterna a classe no HTML root
    const modoEscuroAtivo = document.documentElement.classList.toggle('dark-theme');

    // Salva a preferência do usuário no navegador
    localStorage.setItem('theme', modoEscuroAtivo ? 'dark' : 'light');

    // Atualiza o texto/ícone do botão
    atualizarTextoBotao(toggleBtn, modoEscuroAtivo);
  });
}

/**
 * Atualiza o rótulo do botão
 * @param {HTMLElement} btn 
 * @param {boolean} isDark 
 */
function atualizarTextoBotao(btn, isDark) {
  btn.textContent = isDark ? '☀️ Modo Claro' : '🌙 Modo Escuro';
  btn.setAttribute('aria-label', isDark ? 'Mudar para modo claro' : 'Mudar para modo escuro');
}