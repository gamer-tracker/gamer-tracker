// 1. Validação Visual do Bootstrap
(function () {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();

// 2. Interceptador Global de Formulários (A solução para o erro do JSON)
document.addEventListener('DOMContentLoaded', () => {
    const apiForms = document.querySelectorAll('form[action^="php/"]');
    
    apiForms.forEach(form => {
        form.addEventListener('submit', async (e) => {
            if (!form.checkValidity()) return;
            e.preventDefault();
            
            const formData = new FormData(form);
            const url = form.getAttribute('action');
            
            try {
                const response = await fetch(url, { method: 'POST', body: formData });
                const data = await response.json();
                
                if (data.status === 'success') {
                    alert('✅ ' + data.mensagem);
                    
                    // Lógica de redirecionamento universal
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else if (url.includes('registrar-user.php')) {
                        window.location.href = 'login.html';
                    } else if (url.includes('cadastro-jogo.php') || url.includes('editar.php')) {
                        window.location.href = 'biblioteca.html';
                    } else {
                        form.reset();
                        form.classList.remove('was-validated');
                    }
                } else {
                    alert('❌ ERRO: ' + data.mensagem);
                }
            } catch (error) {
                alert('Falha na conexão: ' + error.message);
            }
        });
    });
});

// 3. Função de Excluir Conta (Blindada)
async function confirmarExclusao() {
    if (!confirm("ATENÇÃO: Tem certeza que quer excluir sua conta? Tudo será perdido.")) return;
    
    const form = document.getElementById('formExcluirConta');
    const formData = new FormData(form);
    
    try {
        const response = await fetch(form.getAttribute('action'), { method: 'POST', body: formData });
        const data = await response.json();
        
        if (data.status === 'success') {
            alert('✅ Conta excluída!');
            window.location.href = 'index.html';
        } else {
            alert('❌ Erro: ' + data.mensagem);
        }
    } catch (error) {
        alert('Falha ao excluir.');
    }
}

// 4. Função de Excluir Jogo
async function excluirJogo(id) {
    if (!confirm("Remover este jogo da estante?")) return;
    try {
        const response = await fetch(`php/excluir.php?id=${id}`);
        const data = await response.json();
        if (data.status === 'success') {
            alert('✅ Removido!');
            location.reload();
        }
    } catch (error) {
        alert('Falha na comunicação.');
    }
}

// 5. Injeção de dados (Perfil e Biblioteca)
document.addEventListener('DOMContentLoaded', async () => {
    // Carrega Perfil
    const perfilPage = document.getElementById('perfil');
    if (perfilPage) {
        const res = await fetch('php/dados-perfil.php');
        const result = await res.json();
        if (result.status === 'success') {
            document.getElementById('nome-perfil').textContent = result.data.nome;
            document.getElementById('email-perfil').innerHTML = `<i class="bi bi-envelope"></i> ${result.data.email}`;
        }

        // Carrega as estatísticas (contagem de jogos)
        const resStats = await fetch('php/estatisticas.php');
        const stats = await resStats.json();
        if (stats.status === 'success') {
            document.getElementById('stat-total').textContent = stats.data.total;
            document.getElementById('stat-jogando').textContent = stats.data.jogando;
            document.getElementById('stat-zerado').textContent = stats.data.zerado;
            document.getElementById('stat-platinado').textContent = stats.data.platinado;
        }
    }
    
    // Carrega Biblioteca
    const container = document.getElementById('container-jogos');
    if (container) {
        const res = await fetch('php/listar.php');
        const result = await res.json();
        container.innerHTML = '';
        if (result.status === 'success') {
            result.data.forEach(jogo => {
                container.innerHTML += `
                    <div class="col">
                        <div class="card h-100 bg-dark text-white">
                            <div class="card-body">
                                <h5>${jogo.nome}</h5>
                                <p>Status: ${jogo.status_jogo}</p>
                                <div class="d-flex gap-2">
                                    <a href="editar_jogo.html?id=${jogo.id}" class="btn btn-sm btn-warning">Editar</a>
                                    <button onclick="excluirJogo(${jogo.id})" class="btn btn-sm btn-danger">Excluir</button>
                                </div>
                            </div>
                        </div>
                    </div>`;
            });
        }
    }
});

// 6. Mantém o estado de login visível na página inicial
document.addEventListener('DOMContentLoaded', async () => {
    const navConta = document.getElementById('nav-conta');
    if (!navConta) return;

    try {
        const res = await fetch('php/dados-perfil.php');
        const result = await res.json();
        if (result.status === 'success') {
            navConta.innerHTML = `<a class="nav-link" href="perfil.html"><i class="bi bi-person-circle"></i> Meu Perfil</a>`;
        }
    } catch (e) {
        // Sem sessão ativa: mantém "Entrar / Criar Conta"
    }
});