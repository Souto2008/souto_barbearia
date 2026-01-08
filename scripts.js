console.log('scripts.js loaded and executing.');
/**
 * Ficheiro: scripts.js
 * Objetivo: Conter os scripts JavaScript gerais para o site Souto Barbearia,
 * controlando animações e interações dinâmicas.
 */

// Adiciona um "ouvinte" que espera que todo o conteúdo HTML da página (o DOM)
// seja carregado e processado pelo navegador antes de executar o nosso código.
// Isto garante que todos os elementos (botões, formulários, etc.) já existem.
document.addEventListener('DOMContentLoaded', function() {

    // --- FUNCIONALIDADE DO CABEÇALHO COM SCROLL ---
    const header = document.querySelector('header');

    // Se um elemento <header> não for encontrado, não fazemos nada.
    if (header) {
        let lastScrollY = window.scrollY; // Guarda a posição do último scroll
        
        const handleScroll = () => {
            const currentScrollY = window.scrollY;
            
            // Adiciona a classe 'header-scrolled' se o utilizador rolou mais de 50px para baixo.
            // Remove a classe se o utilizador voltou para o topo.
            // Isto ativa o fundo com blur que definimos no CSS.
            if (currentScrollY > 50) {
                header.classList.add('header-scrolled');
            } else {
                header.classList.remove('header-scrolled');
            }
            
            if (currentScrollY > lastScrollY && currentScrollY > 100) {
                header.classList.add('header-hidden'); // Exemplo de classe para esconder
            } else {
                header.classList.remove('header-hidden');
            }
            
            // Atualiza a última posição do scroll
            lastScrollY = currentScrollY;
        };
        
        // Adiciona um ouvinte ao evento de 'scroll' da janela, com a opção 'passive' para melhor performance.
        window.addEventListener('scroll', handleScroll, { passive: true });
        
        // Executa a função uma vez ao carregar a página para verificar a posição inicial do scroll
        handleScroll();
    }
    
    // --- MENU MOBILE (HAMBÚRGUER) ---
    // Cria e controla o menu lateral em dispositivos móveis
    const headerContent = document.querySelector('.header-content');
    const navUl = document.querySelector('header nav ul');

    // Só executa se encontrar o cabeçalho e o menu (evita erros em páginas sem menu)
    if (headerContent && navUl) {
        // 1. Criar o botão hambúrguer dinamicamente via JavaScript
        const mobileBtn = document.createElement('button');
        mobileBtn.className = 'mobile-menu-btn';
        mobileBtn.innerHTML = '<span></span><span></span><span></span>'; // As 3 linhas do ícone
        mobileBtn.setAttribute('aria-label', 'Abrir Menu');
        
        // Adiciona o botão ao cabeçalho (ficará ao lado do logo devido ao flexbox)
        headerContent.appendChild(mobileBtn);

        // 2. Evento de clique para abrir/fechar
        mobileBtn.addEventListener('click', function() {
            navUl.classList.toggle('active'); // Mostra/esconde o menu lateral
            mobileBtn.classList.toggle('active'); // Anima o ícone para um X
        });

        // 3. Fechar o menu automaticamente ao clicar num link
        navUl.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                navUl.classList.remove('active');
                mobileBtn.classList.remove('active');
            });
        });
    }

    
    // --- ATUALIZAÇÃO AUTOMÁTICA DO ANO NO RODAPÉ ---
    const yearSpan = document.getElementById('current-year');
    // Se o elemento com o ID 'current-year' for encontrado...
    if (yearSpan) {
        // ...define o seu texto para o ano atual.
        yearSpan.textContent = new Date().getFullYear();
    }

    // --- SISTEMA DE NOTIFICAÇÕES CUSTOMIZADO ---
    // Injeta o HTML do modal no corpo da página
    const modalHTML = `
        <div id="custom-modal-overlay" class="custom-modal-overlay">
            <div id="custom-modal" class="custom-modal">
                <h3 id="modal-title"></h3>
                <p id="modal-message"></p>
                <div class="modal-buttons">
                    <button id="modal-btn-cancel" class="cta-button" style="background-color: transparent; border: 1px solid var(--cor-texto-suave); color: var(--cor-texto-suave);">Cancelar</button>
                    <button id="modal-btn-confirm" class="cta-button">Confirmar</button>
                </div>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    const modalOverlay = document.getElementById('custom-modal-overlay');
    const modalTitle = document.getElementById('modal-title');
    const modalMessage = document.getElementById('modal-message');
    const btnConfirm = document.getElementById('modal-btn-confirm');
    const btnCancel = document.getElementById('modal-btn-cancel');

    let confirmCallback = null;

    function showCustomConfirm(title, message, callback) {
        modalTitle.textContent = title;
        modalMessage.textContent = message;
        confirmCallback = callback;
        modalOverlay.classList.add('active');
    }

    function hideModal() {
        modalOverlay.classList.remove('active');
        confirmCallback = null;
    }

    btnConfirm.addEventListener('click', () => {
        if (typeof confirmCallback === 'function') {
            confirmCallback();
        }
        hideModal();
    });

    btnCancel.addEventListener('click', hideModal);
    modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) {
            hideModal();
        }
    });


    // --- CONFIRMAÇÃO DE LOGOUT (CUSTOM) ---
    const logoutLink = document.getElementById('logout-link');
    if (logoutLink) {
        logoutLink.addEventListener('click', function(event) {
            event.preventDefault(); // Impede a navegação imediata
            showCustomConfirm(
                'Terminar Sessão',
                'Tem a certeza que quer dar logout?',
                () => {
                    window.location.href = logoutLink.href; // Navega se confirmar
                }
            );
        });
    }

    // --- CONFIRMAÇÃO DE CANCELAMENTO (CUSTOM) ---
    const formsCancelar = document.querySelectorAll('.form-cancelar');
    formsCancelar.forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Impede o envio imediato
            showCustomConfirm(
                'Cancelar Marcação',
                'Tem a certeza que deseja cancelar esta marcação? Esta ação não pode ser revertida.',
                () => {
                    form.submit(); // Submete o formulário se o user confirmar
                }
            );
        });
    });

    // --- LÓGICA DINÂMICA PARA A PÁGINA DE MARCAÇÃO (ATUALIZADA) ---
const inputData = document.getElementById('data');
const inputHora = document.getElementById('hora'); // Agora é um input hidden
const containerHorarios = document.getElementById('horarios-container'); // Novo container visual
// NOVO: Referência ao novo seletor de serviço
const selectServico = document.getElementById('servico'); 

// Se os campos existirem na página...
if (inputData && inputHora && containerHorarios && selectServico) {
    
    // Define o mínimo de data hoje para evitar marcações passadas no navegador
    inputData.min = new Date().toISOString().split('T')[0];

    // Função que carrega os horários disponíveis
    function carregarHorariosDisponiveis() {
        const dataSelecionada = inputData.value;
        const servicoSelecionado = selectServico.value;
        
        // Limpa o valor selecionado anteriormente
        inputHora.value = '';

        // 1. Verificação: Ambos os campos (Data e Serviço) são obrigatórios
        if (!dataSelecionada || !servicoSelecionado) {
            containerHorarios.innerHTML = '<div class="msg-horarios">Escolha o serviço e a data</div>';
            return; 
        }

        // 2. Validação de Data Passada (Melhorada no HTML, mas mantida no JS para garantia)
        if (inputData.validity.rangeUnderflow) {
            inputData.reportValidity();
            return;
        }

        // 3. Monta a URL para chamar a API, INCLUINDO O SERVIÇO
        const url = `horarios_livres.php?data=${dataSelecionada}&servico_id=${servicoSelecionado}`;

        // 4. Limpa e mostra estado de carregamento
        containerHorarios.innerHTML = '<div class="msg-horarios">A carregar horários...</div>';

        // 5. Chamada AJAX
        fetch(url)
            .then(response => {
                // Verifica se a resposta foi OK (código 2xx)
                if (!response.ok) {
                    // Tenta ler o JSON de erro do backend para uma mensagem mais específica
                    return response.json().then(errorData => {
                        // Se houver dados de erro (como 'Serviço não encontrado'), usa-os
                        throw new Error(errorData.erro || `Erro de rede: ${response.status}`);
                    }).catch(() => {
                        // Se não for JSON, usa a mensagem genérica
                        throw new Error(`Erro de rede: ${response.status} (Verifique a consola)`);
                    });
                }
                return response.json();
            })
            .then(horarios => {
                containerHorarios.innerHTML = ''; // Limpa a mensagem de carregamento

                // Obter a data e hora atuais
                const agora = new Date();
                const hoje = agora.toISOString().split('T')[0]; // Formato YYYY-MM-DD
                const horaAtual = agora.getHours();
                const minutoAtual = agora.getMinutes();

                let horariosFiltrados = horarios;

                // Se a data selecionada for hoje, filtrar horários que já passaram
                if (dataSelecionada === hoje) {
                    horariosFiltrados = horarios.filter(h => {
                        const [hora, minuto] = h.split(':').map(Number);
                        // Filtra se a hora for maior que a atual, ou se for a mesma hora mas o minuto for maior que a atual
                        return hora > horaAtual || (hora === horaAtual && minuto > minutoAtual);
                    });
                }

                if (horariosFiltrados.length === 0) {
                    containerHorarios.innerHTML = '<div class="msg-horarios">Nenhum horário disponível para este dia.</div>';
                    return;
                }

                // Gera os botões de horário
                horariosFiltrados.forEach(hora => {
                    const btn = document.createElement('button');
                    btn.type = 'button'; // Importante para não submeter o form
                    btn.className = 'horario-btn';
                    btn.textContent = hora;
                    
                    btn.addEventListener('click', function() {
                        // Remove a classe 'selected' de todos os botões
                        document.querySelectorAll('.horario-btn').forEach(b => b.classList.remove('selected'));
                        // Adiciona ao clicado
                        this.classList.add('selected');
                        // Atualiza o input hidden que será enviado no form
                        inputHora.value = hora;
                    });

                    containerHorarios.appendChild(btn);
                });
            })
            .catch(error => {
                console.error("Erro ao buscar horários:", error);
                // Exibe a mensagem de erro que veio do 'throw' (do backend ou de rede)
                containerHorarios.innerHTML = `<div class="msg-horarios">${error.message || 'Erro ao carregar horários.'}</div>`;
            });
    }

    // Ouve a mudança no Serviço OU na Data
    selectServico.addEventListener('change', carregarHorariosDisponiveis);
    inputData.addEventListener('change', carregarHorariosDisponiveis);
    
    // O seu código original para o ícone do calendário deve ser mantido aqui
    const calendarIcon = document.querySelector('.calendar-icon');
    if (calendarIcon) {
        calendarIcon.addEventListener('click', function() {
            if (typeof inputData.showPicker === 'function') {
                inputData.showPicker();
            } else {
                inputData.focus();
            }
        });
    }

} // Fim do if (inputData && inputHora && containerHorarios && selectServico)

    // --- INICIALIZAÇÃO DE BIBLIOTECAS DE ANIMAÇÃO ---
    // Inicializa a biblioteca Lenis para um efeito de scroll mais suave.    
    // Garante que a biblioteca Lenis foi carregada (verificando se 'Lenis' existe)
    if (typeof Lenis !== 'undefined') {
        const lenis = new Lenis();

        function raf(time) {
            lenis.raf(time);
            requestAnimationFrame(raf);
        }
        requestAnimationFrame(raf);
    } // Fim do if (typeof Lenis !== 'undefined')
}); // Fim do document.addEventListener('DOMContentLoaded', ...)