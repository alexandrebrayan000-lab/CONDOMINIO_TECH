// Central session helper: fetch session info and inject username + logout link
document.addEventListener('DOMContentLoaded', function(){
    const candidates = ['php/session.php','../php/session.php','session.php','./session.php'];

    function tryFetch(list) {
        if (!list.length) return Promise.reject('no candidates');
        const url = list[0];
        return fetch(url).then(res => {
            if (!res.ok) return Promise.reject(url);
            return res.json();
        }).catch(()=> tryFetch(list.slice(1)));
    }

    tryFetch(candidates).then(data => {
        if (!data) return;
        const area = document.getElementById('sessionArea');
        if (!area) return;
        if (data.logged) {
            const nome = String(data.nome).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
            const logoutHref = data.logout || (window.location.pathname.includes('/php/') ? 'logout.php' : 'php/logout.php');
            const initial = nome.split(' ').map(s=>s.trim()).filter(Boolean).map(s=>s[0].toUpperCase()).slice(0,2).join('');
            area.innerHTML = '<div class="session-wrapper">\n                    <span class="avatar-initial" aria-hidden="true">' + initial + '</span>\n                    <span class="usuario-nome">' + nome + '</span>\n                    <button type="button" class="btn-logout" data-logout="' + logoutHref + '">Sair</button>\n                </div>';
            const btn = area.querySelector('.btn-logout');
            if (btn) {
                btn.addEventListener('click', () => {
                    window.location.href = btn.dataset.logout;
                });
            }
        } else {
            area.innerHTML = '';
        }
    }).catch(()=>{ /* silently fail */ });
});
