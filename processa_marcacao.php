<?php
/**
 * Ficheiro: processa_marcacao.php (CORRIGIDO PARA DURAÇÃO E CONFLITO)
 * Objetivo: Recebe os dados do formulário de marcação, valida-os e insere
 * uma nova marcação na base de dados, garantindo que não há sobreposição.
 */

require __DIR__ . '/config/db.php';
session_start();    

// VERIFICAÇÃO DE AUTENTICAÇÃO
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// PROCESSAMENTO DO FORMULÁRIO
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. RECEBER E VALIDAR DADOS DO FORMULÁRIO
    if (
        !isset($_POST['data_marcacao']) || 
        !isset($_POST['hora_marcacao']) || 
        !isset($_POST['servico_id']) || // NOVO: Campo de serviço
        empty($_POST['data_marcacao']) || 
        empty($_POST['hora_marcacao']) ||
        empty($_POST['servico_id'])
    ) {
        header("Location: marcacao.php?erro=dados_em_falta");
        exit();
    }

    $utilizador_id = $_SESSION['user_id'];
    
    // Filtra todos os dados do POST de uma vez
    $data_marcacao = filter_input(INPUT_POST, 'data_marcacao', FILTER_SANITIZE_SPECIAL_CHARS);
    $hora_marcacao = filter_input(INPUT_POST, 'hora_marcacao', FILTER_SANITIZE_SPECIAL_CHARS);
    $servico_id = filter_input(INPUT_POST, 'servico_id', FILTER_VALIDATE_INT);
    
    // 1.1. CONVERTER PARA DATETIME E VERIFICAR DURAÇÃO
    $data_hora_inicio_str = $data_marcacao . ' ' . $hora_marcacao . ':00';
    $timestamp_inicio = strtotime($data_hora_inicio_str);
    
    // Se a conversão falhar, abortar
    if ($timestamp_inicio === false) {
        header("Location: marcacao.php?erro=data_invalida");
        exit();
    }

    // 1.2. Validar Data/Hora Passada (Hoje)
    $agora = time();
    if ($timestamp_inicio < $agora) {
        // Se a data já passou (ou é hoje e a hora já passou)
        header("Location: marcacao.php?erro=" . ($data_marcacao < date('Y-m-d') ? 'data_passada' : 'hora_passada'));
        exit();
    }

    // 1.3. BUSCAR DURAÇÃO DO SERVIÇO
    $duracao_solicitada = 0;
    try {
        $stmt_duracao = $pdo->prepare("SELECT duracao_minutos FROM servicos WHERE servico_id = ? AND ativo = 1");
        $stmt_duracao->execute([$servico_id]);
        $servico = $stmt_duracao->fetch(PDO::FETCH_ASSOC);

        if (!$servico) {
            header("Location: marcacao.php?erro=servico_invalido");
            exit();
        }
        $duracao_solicitada = (int)$servico['duracao_minutos'];

    } catch (PDOException $e) {
        header("Location: marcacao.php?erro=db");
        exit();
    }
    
    // 1.4. CALCULAR DATA_HORA_FIM
    $timestamp_fim = $timestamp_inicio + ($duracao_solicitada * 60);
    $data_hora_fim_str = date('Y-m-d H:i:s', $timestamp_fim);

    // 2. VERIFICAÇÃO DE CONFLITO DE HORÁRIO (LÓGICA DE SOBREPOSIÇÃO)
    // Verifica se o slot do candidato (data_hora_inicio a data_hora_fim) se sobrepõe
    // com qualquer slot existente (data_hora_inicio_reservado a data_hora_fim_reservado).
    $sql_conflito = "
        SELECT COUNT(*) 
        FROM marcacoes 
        WHERE 
            estado != 'cancelada' AND
            (data_hora_inicio < ? AND data_hora_fim > ?)
    "; //     
    $stmt_conflito = $pdo->prepare($sql_conflito);
    // Bind: 1. data_hora_fim_candidato, 2. data_hora_inicio_candidato
    $stmt_conflito->execute([$data_hora_fim_str, $data_hora_inicio_str]);
    $conflito = $stmt_conflito->fetchColumn();

    if ($conflito > 0) {
        // Redireciona o utilizador de volta para a página de marcação com uma mensagem de erro.
        header("Location: marcacao.php?erro=ocupado");
        exit();
    }
    
    // 3. INSERÇÃO NA BASE DE DADOS (USANDO DATETIME E SERVIÇO)
    // Agora insere data_hora_inicio, data_hora_fim e o servico_id
    $sql_insert = "
        INSERT INTO marcacoes (utilizador_id, servico_id, data_hora_inicio, data_hora_fim, estado) 
        VALUES (?, ?, ?, ?, 'confirmada')
    ";
    $stmt_insert = $pdo->prepare($sql_insert);

    // Executa a inserção
    if ($stmt_insert->execute([$utilizador_id, $servico_id, $data_hora_inicio_str, $data_hora_fim_str])) {
        header("Location: marcacao.php?sucesso=agendado");
        exit();
    } else {
        header("Location: marcacao.php?erro=db");
        exit();
    }
}
// Fim do POST
?>