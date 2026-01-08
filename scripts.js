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
    

    
    // --- ATUALIZAÇÃO AUTOMÁTICA DO ANO NO RODAPÉ ---
    const yearSpan = document.getElementById('current-year');
    // Se o elemento com o ID 'current-year' for encontrado...
    if (yearSpan) {
        // ...define o seu texto para o ano atual.
        yearSpan.textContent = new Date().getFullYear();
    }

    // --- CONFIRMAÇÃO DE LOGOUT ---
    const logoutLink = document.getElementById('logout-link');
    // Se o link de logout for encontrado...
    if (logoutLink) {
        // ...adiciona um ouvinte ao evento de 'click'.
        logoutLink.addEventListener('click', function(event) {
            // Mostra uma caixa de confirmação. Se o utilizador clicar em "Cancelar"...
            if (!confirm('Tem a certeza que quer dar logout?')) {
                // ...impede a ação padrão do link (que seria navegar para logout.php).
                event.preventDefault(); 
            }
        });
    }



    // --- LÓGICA DINÂMICA PARA A PÁGINA DE MARCAÇÃO (ATUALIZADA) ---
const inputData = document.getElementById('data');
const selectHora = document.getElementById('hora');
// NOVO: Referência ao novo seletor de serviço
const selectServico = document.getElementById('servico'); 

// Se os campos existirem na página...
if (inputData && selectHora && selectServico) {
    
    // Define o mínimo de data hoje para evitar marcações passadas no navegador
    inputData.min = new Date().toISOString().split('T')[0];

    // Função que carrega os horários disponíveis
    function carregarHorariosDisponiveis() {
        const dataSelecionada = inputData.value;
        const servicoSelecionado = selectServico.value;
        
        // 1. Verificação: Ambos os campos (Data e Serviço) são obrigatórios
        if (!dataSelecionada || !servicoSelecionado) {
            // Se faltar algum valor, limpa o select da hora e mostra a mensagem padrão
            selectHora.innerHTML = '<option value="" disabled selected>Escolha o serviço e a data</option>';
            selectHora.disabled = true;
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
        selectHora.innerHTML = '<option value="" disabled selected>A carregar horários...</option>';
        selectHora.disabled = true;

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
                selectHora.disabled = false;
                selectHora.innerHTML = ''; // Limpa as opções de carregamento

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
                    selectHora.innerHTML = '<option value="" disabled selected>Nenhum horário disponível para este dia.</option>';
                    return;
                }

                // Adiciona a opção padrão
                let defaultOption = document.createElement('option');
                defaultOption.value = "";
                defaultOption.text = "Selecione o horário";
                defaultOption.disabled = true;
                defaultOption.selected = true;
                selectHora.appendChild(defaultOption);

                // Preenche com os horários disponíveis
                horariosFiltrados.forEach(hora => {
                    let option = document.createElement('option');
                    option.value = hora;
                    option.text = hora;
                    selectHora.appendChild(option);
                });
            })
            .catch(error => {
                console.error("Erro ao buscar horários:", error);
                // Exibe a mensagem de erro que veio do 'throw' (do backend ou de rede)
                selectHora.innerHTML = `<option value="" disabled selected>${error.message || 'Erro ao carregar horários.'}</option>`;
                selectHora.disabled = true;
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

} // Fim do if (inputData && selectHora && selectServico)

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