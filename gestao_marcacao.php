<?php
/**
 * Ficheiro: gestao_marcacao.php
 * Objetivo: API para confirmar ou cancelar uma marcação na base de dados,
 * com acesso restrito ao Barbeiro.
 */
header('Content-Type: application/json');

require __DIR__ . '/config/db.php';
session_start();

// 1. VERIFICAÇÃO DE AUTORIZAÇÃO
// Assegura que só o Barbeiro (ou utilizador com 'tipo' = 'barbeiro') pode executar esta ação.
if (!isset($_SESSION['user_id']) || (isset($_SESSION['tipo']) && $_SESSION['tipo'] !== 'barbeiro')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado.']);
    exit();
}

// 2. VALIDAÇÃO DE DADOS DE ENTRADA
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['id']) || !isset($_POST['estado'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dados de entrada insuficientes.']);
    exit();
}

$marcacao_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$novo_estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_SPECIAL_CHARS);

// Valida se o ID é válido e se o estado é um dos permitidos
if (!$marcacao_id || !in_array($novo_estado, ['confirmada', 'cancelada'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de marcação ou estado inválido.']);
    exit();
}

// 3. ATUALIZAÇÃO DA BASE DE DADOS
try {
    // A query SQL para atualizar o campo 'estado'
    $sql = "UPDATE marcacoes SET estado = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$novo_estado, $marcacao_id])) {
        // Sucesso na atualização
        echo json_encode(['success' => true, 'message' => 'Marcação atualizada com sucesso para ' . $novo_estado . '.']);
    } else {
        // Erro na execução
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erro ao executar a atualização na base de dados.']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro de Base de Dados: ' . $e->getMessage()]);
}

?>