/*atualizar_dados_perfil*/
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

/*Cadastro_jogo.html*/
   // 1. Contador de Caracteres
    const textarea = document.getElementById('review_jogo');
    const contador = document.getElementById('contador');

    textarea.addEventListener('input', function() {
        const tamanhoAtual = this.value.length;
        contador.textContent = `${tamanhoAtual} / 300 caracteres`;
    });

    // 2. Validação do Formulário
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()


/*Login.html*/
(function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()

/* Perfil.html */
    function confirmarExclusao() {
        // Exibe um pop-up nativo do navegador perguntando se tem certeza
        const confirmacao = confirm("ATENÇÃO: Você tem certeza absoluta que deseja excluir sua conta?\nTodos os seus jogos e dados salvos serão perdidos permanentemente.");
        
        // Se o usuário clicar em "OK", o formulário é enviado para o PHP processar a exclusão
        if (confirmacao) {
            document.getElementById('formExcluirConta').submit();
        }
    }

/* registro.html*/

  (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
