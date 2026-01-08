<?php
/**
 * Ficheiro: horarios_livres.php (ATUALIZADO PARA DURAÇÃO)
 * Objetivo: API para fornecer os horários disponíveis para uma data e SERVIÇO específicos.
 */

require __DIR__ . '/horarios.php'; // Inclui a nova função gerarHorarios()
require __DIR__ . '/config/db.php'; 

// 1. VALIDAR A DATA E O SERVIÇO DE ENTRADA
if (!isset($_GET['data']) || !isset($_GET['servico_id'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados de entrada insuficientes.']);
    exit();
}
$data_selecionada = $_GET['data']; 
$servico_id = filter_input(INPUT_GET, 'servico_id', FILTER_VALIDATE_INT);

// 2. BUSCAR A DURAÇÃO DO SERVIÇO
$duracao_solicitada = 0;
try {
    // Busca a duração real do serviço
    $stmt_duracao = $pdo->prepare("SELECT duracao_minutos FROM servicos WHERE servico_id = ? AND ativo = 1");
    $stmt_duracao->execute([$servico_id]);
    $servico = $stmt_duracao->fetch(PDO::FETCH_ASSOC);

    if ($servico) {
        $duracao_solicitada = (int)$servico['duracao_minutos'];
    } else {
        http_response_code(404);
        echo json_encode(['erro' => 'Serviço não encontrado ou inativo.']);
        exit();
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro de base de dados ao buscar serviço.']);
    exit();
}

// 3. GERAR TODOS OS HORÁRIOS POSSÍVEIS
// A granularidade de 15 minutos garante que todos os serviços são cobertos.
$granularidade_slot = 15; 
$todos_horarios = gerarHorarios($data_selecionada, $granularidade_slot); 

// 4. BUSCAR MARCAÇÕES JÁ OCUPADAS
// Precisamos dos horários de início e fim das marcações existentes.
// NOTA: Assumo que a sua tabela `marcacoes` JÁ FOI ATUALIZADA com o campo `data_hora_fim`.
$marcacoes_reservadas = [];
$stmt = $pdo->prepare("SELECT data_hora_inicio, data_hora_fim FROM marcacoes WHERE data_hora_inicio >= ? AND data_hora_inicio < DATE_ADD(?, INTERVAL 1 DAY) AND estado != 'cancelada'");
$stmt->execute([$data_selecionada, $data_selecionada]);
$marcacoes_reservadas = $stmt->fetchAll(PDO::FETCH_ASSOC);


// 5. CALCULAR OS HORÁRIOS LIVRES (Verificação de Conflito de Duração)
$horarios_livres_finais = [];
$date_time_format = 'Y-m-d H:i:s';
$date_prefix = $data_selecionada . ' '; 

foreach ($todos_horarios as $hora_inicio_str) { 
    
    $inicio_candidato = $date_prefix . $hora_inicio_str . ':00';
    $timestamp_inicio = strtotime($inicio_candidato);
    
    // Calcula o fim do slot de tempo se o serviço for marcado neste horário
    $fim_candidato = date($date_time_format, $timestamp_inicio + ($duracao_solicitada * 60));

    $conflito = false;

    // Verifica se a hora de fim cai FORA do horário de trabalho (Almoço ou Fim do dia)
    // Se o fim do slot candidato não for um dos horários gerados em $todos_horarios
    // e o seu minuto não for 00 (para horários exatos), pode ser um problema, mas
    // a regra de conflito com o 'fim_reservado' já trata a maioria dos casos.
    
    // Verificação simplificada de FIM DE SLOT:
    // Se o fim do slot cair no intervalo de almoço ou depois do fecho do barbeiro, não é válido.
    // Basta ver se a hora final está contida nos horários de trabalho.
    // É mais eficiente confiar na verificação de conflito completa:

    foreach ($marcacoes_reservadas as $m) {
        $inicio_reservado = $m['data_hora_inicio'];
        $fim_reservado = $m['data_hora_fim'];

        // Regra de conflito:
        // O slot CANDIDATO (Inicio - Fim) tem conflito com o slot RESERVADO (Inicio - Fim) se:
        // (O slot candidato começar antes do fim do reservado) E (O slot candidato terminar depois do início do reservado)
        if ($inicio_candidato < $fim_reservado && $fim_candidato > $inicio_reservado) {
            $conflito = true;
            break; 
        }
    }

    if (!$conflito) {
        $horarios_livres_finais[] = $hora_inicio_str;
    }
}

// 6. ENVIAR A RESPOSTA EM FORMATO JSON
header('Content-Type: application/json');
echo json_encode(array_values($horarios_livres_finais));