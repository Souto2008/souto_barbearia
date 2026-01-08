<?php
/**
 * Ficheiro: agenda_barbeiro.php
 * Objetivo: Apresentar o calendário de marcações para o Barbeiro usando FullCalendar v5.
 */
require __DIR__ . '/config/db.php';
session_start();

// VERIFICAÇÃO DE AUTORIZAÇÃO: Assegura que só o Barbeiro tem acesso.
if (!isset($_SESSION['user_id']) || (isset($_SESSION['tipo']) && $_SESSION['tipo'] !== 'barbeiro')) {
    header("Location: login.php?erro=nao_autorizado");
    exit();
}

$page_title = 'Agenda do Barbeiro - Souto Barbearia';
$body_class = 'agenda-body-background';
$header_style = 'barber';
require_once 'templates/header.php';
?>

<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />

<style>
    /* Estilo "Clean & Premium" para a Agenda */
    #calendario {
        max-width: 95%;
        margin: 5rem;
        padding: 2rem;
        background: rgba(18, 18, 18, 0.95);
        border-radius: 15px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.6);
        border: 1px solid rgba(197, 164, 126, 0.15); /* Borda dourada muito subtil */
        font-family: 'Montserrat', sans-serif;
    }

    .page-header-container h2 {
        font-weight: 300;
        letter-spacing: 2px;
        text-transform: uppercase;
        font-size: 2rem;
        margin-bottom: 1rem;
    }

    /* Toolbar (Botões e Título do Mês) */
    .fc .fc-toolbar {
        margin-bottom: 2rem !important;
        align-items: center;
    }
    
    .fc .fc-toolbar-title {
        font-size: 1.5rem !important;
        font-weight: 400;
        letter-spacing: 1px;
        color: #c5a47e;
        text-transform: uppercase;
    }

    /* Botões do Calendário */
    .fc .fc-button-primary {
        background-color: transparent !important;
        border: 1px solid rgba(197, 164, 126, 0.3) !important;
        color: #c5a47e !important;
        font-weight: 500;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 1px;
        padding: 0.5rem 1rem;
        border-radius: 5px;
        transition: all 0.3s;
        box-shadow: none !important;
    }

    .fc .fc-button-primary:hover, .fc .fc-button-primary:not(:disabled).fc-button-active {
        background-color: #c5a47e !important;
        color: #121212 !important;
        border-color: #c5a47e !important;
        transform: translateY(-1px);
    }

    /* Linhas da Grelha */
    .fc-theme-standard td, 
    .fc-theme-standard th {
        border-color: rgba(255, 255, 255, 0.05); /* Linhas muito subtis */
    }

    .fc .fc-col-header-cell-cushion {
        color: #a0a0a0; /* Dias da semana em cinza suave */
        font-weight: 500;
        text-transform: uppercase;
        font-size: 0.85rem;
        padding: 10px 0;
        text-decoration: none;
    }

    .fc .fc-timegrid-slot-label-cushion {
        color: #666; /* Horas mais discretas */
        font-size: 0.8rem;
        font-family: 'Montserrat', sans-serif;
    }

    .fc .fc-daygrid-day-number {
        color: #c5a47e;
        font-weight: 600;
        padding: 8px;
        text-decoration: none;
    }

    /* Eventos (Marcações) */
    .fc-event {
        border: none !important;
        border-radius: 4px;
        padding: 2px 4px;
        font-size: 0.85rem;
        font-weight: 500;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
</style>

<div id="calendario"></div>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js'></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendario');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek', // Vista semanal com horários
            locale: 'pt', // Define idioma para Português
            slotMinTime: '09:00:00', // Início do turno
            slotMaxTime: '20:00:00', // Fim do turno
            allDaySlot: false,
            slotDuration: '00:15:00', // Intervalos de 15 min para precisão
            
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },

            // FONTE DE DADOS: O teu ficheiro PHP que gera o JSON
            events: 'api_eventos.php',

            // Lógica ao clicar numa marcação
            eventClick: function(info) {
                var evento = info.event;
                var props = evento.extendedProps; 
                var estadoAtual = props.estado;
                
                var mensagem = `Marcação de: ${props.cliente}\n` +
                               `Serviço: ${props.servico}\n` +
                               `Hora: ${evento.start.toLocaleTimeString('pt-PT', {hour: '2-digit', minute:'2-digit'})}\n` +
                               `Estado: ${estadoAtual.toUpperCase()}`;

                if (estadoAtual === 'pendente') {
                    if (confirm(mensagem + '\n\nClique em OK para CONFIRMAR esta marcação.')) {
                        gerirEstadoMarcacao(evento.id, 'confirmada', calendar);
                    } else if (confirm('Deseja CANCELAR esta marcação?')) {
                        gerirEstadoMarcacao(evento.id, 'cancelada', calendar);
                    }
                } else {
                    if (confirm(mensagem + '\n\nDeseja CANCELAR esta marcação já confirmada?')) {
                        gerirEstadoMarcacao(evento.id, 'cancelada', calendar);
                    }
                }
                
                info.jsEvent.preventDefault(); 
            }
        });

        calendar.render();
    });

    // Função para atualizar o estado via AJAX
    function gerirEstadoMarcacao(id, novoEstado, calendar) {
        fetch('gestao_marcacao.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}&estado=${novoEstado}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Agenda atualizada com sucesso!');
                calendar.refetchEvents(); // Recarrega os dados sem dar refresh na página
            } else {
                alert('Erro: ' + (data.message || 'Não foi possível atualizar.'));
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro de comunicação com o servidor.');
        });
    }
</script>

<?php
require_once 'templates/footer.php';
?>