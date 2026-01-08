<?php
/**
 * Ficheiro: api_eventos.php
 * Objetivo: Fornecer eventos de marcações em formato JSON para o FullCalendar.
 */
header('Content-Type: application/json');
session_start(); 

// 1. INCLUIR FICHEIROS NECESSÁRIOS
require __DIR__ . '/config/db.php'; 

// 2. BUSCAR DADOS DA AGENDA
try {
    // Consulta para buscar marcações (incluindo o nome do cliente e o serviço)
    $sql = "
        SELECT 
            m.id AS marcacao_id, 
            m.data_hora_inicio, 
            m.data_hora_fim, 
            m.estado,
            u.nome AS nome_cliente,
            s.nome AS nome_servico
        FROM marcacoes m
        JOIN utilizadores u ON m.utilizador_id = u.id
        JOIN servicos s ON m.servico_id = s.servico_id
        WHERE m.estado IN ('pendente', 'confirmada') 
        ORDER BY m.data_hora_inicio ASC
    ";
    
    $stmt = $pdo->query($sql);
    $marcacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro na Base de Dados: ' . $e->getMessage()]);
    exit();
}

// 3. FORMATAR EVENTOS PARA O FULLCALENDAR
$eventos = [];
foreach ($marcacoes as $m) {
    
    // Define a cor com base no estado da marcação
    $cor_fundo = '#2079c6'; // Azul Padrão (Confirmada/Pendente)
    if ($m['estado'] === 'pendente') {
        $cor_fundo = '#f39c12'; // Laranja para Pendente (destacar)
    }

    $eventos[] = [
        'id' => $m['marcacao_id'],
        // Título que aparece no calendário
        'title' => $m['nome_cliente'] . ' - ' . $m['nome_servico'],
        // Horário de Início
        'start' => $m['data_hora_inicio'],
        'end' => $m['data_hora_fim'],
        'backgroundColor' => $cor_fundo,
        'borderColor' => $cor_fundo,
        'extendedProps' => [ 
            'estado' => $m['estado'],
            'cliente' => $m['nome_cliente'], // Usado no eventClick
            'servico' => $m['nome_servico']  // Usado no eventClick
        ]
    ];
}

echo json_encode($eventos);