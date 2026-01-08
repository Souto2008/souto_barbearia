<?php
/**
 * Ficheiro: horarios.php (ATUALIZADO)
 * Objetivo: Fornecer uma função que gera uma lista de todos os horários de trabalho
 * possíveis para um dia, com base em períodos e intervalos definidos,
 * considerando dias de folga.
 */

include 'config/db.php';

/**
 * Gera uma lista de todos os horários de trabalho (slots) para um dia.
 * * @param string $data_selecionada A data no formato 'YYYY-MM-DD'.
 * @param int $intervalo_minutos O intervalo de geração de slots (granularidade). Sugerido: 15.
 * @return array Uma lista de strings no formato "HH:MM". Retorna array vazio se for dia de folga.
 */
function gerarHorarios(string $data_selecionada, int $intervalo_minutos = 15): array {
    
    // 1. DADOS DE CONFIGURAÇÃO (HORÁRIO FIXO E FOLGAS)
    
    // Obtém o número do dia da semana (1 = Segunda, 7 = Domingo)
    $dia_semana = date('N', strtotime($data_selecionada));

    // Mapa de horários de trabalho por dia da semana
    $horarios_semanais = [
        // 'Dia' => [ ['Inicio Manhã', 'Fim Manhã'], ['Inicio Tarde', 'Fim Tarde'] ]
        1 => [['10:00', '13:00'], ['14:00', '19:00']], // Segunda
        2 => [['10:00', '13:00'], ['14:00', '19:00']], // Terça
        3 => [['10:00', '13:00'], ['14:00', '19:00']], // Quarta
        4 => [['10:00', '13:00'], ['14:00', '19:00']], // Quinta
        5 => [['10:00', '13:00'], ['14:00', '20:00']], // Sexta
        6 => [['09:00', '13:00'], ['14:00', '18:00']], // Sábado
        7 => [], // Domingo (Dia de Folga - vazio)
    ];

    // Se for dia de folga (ou não definido), retorna array vazio.
    if (!isset($horarios_semanais[$dia_semana]) || empty($horarios_semanais[$dia_semana])) {
        return [];
    }
    
    // Pega os períodos de trabalho para o dia selecionado
    $periodos = $horarios_semanais[$dia_semana];
    
    // Inicializa o array de horários
    $horarios = []; 
    
    // Gera os slots de acordo com o intervalo (granularidade)
    foreach ($periodos as $p) {
        $inicio = strtotime($p[0]);
        $fim = strtotime($p[1]);

        // Gera os horários dentro do período atual, avançando pelo intervalo definido.
        for ($h = $inicio; $h < $fim; $h = strtotime("+$intervalo_minutos minutes", $h)) {
            $horarios[] = date("H:i", $h);
        }
    }

    // 2. RETORNA a lista de todos os slots de tempo possíveis para este dia.
    return $horarios;
}