<?php
/**
 * Ficheiro: minhas_marcacoes.php
 * Objetivo: Página para o cliente visualizar e gerir as suas marcações (cancelar).
 */
require __DIR__ . '/config/db.php';
session_start();

// VERIFICAÇÃO DE AUTORIZAÇÃO: Assegura que só Clientes têm acesso.
if (!isset($_SESSION['user_id']) || (isset($_SESSION['tipo']) && $_SESSION['tipo'] !== 'cliente')) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";

// Lógica de Cancelamento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancelar') {
    $id_marcacao = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id_marcacao > 0) {
        // Correção: id_cliente alterado para utilizador_id conforme a sua tabela
        $stmt_check = $pdo->prepare("SELECT id FROM marcacoes WHERE id = ? AND utilizador_id = ? AND estado IN ('pendente', 'confirmada')");
        $stmt_check->execute([$id_marcacao, $user_id]);

        if ($stmt_check->fetch()) {
            $stmt_update = $pdo->prepare("UPDATE marcacoes SET estado = 'cancelada' WHERE id = ?");
            
            if ($stmt_update->execute([$id_marcacao])) {
                $msg = "<div class='alert alert-success'>Marcação cancelada com sucesso.</div>";
            } else {
                $msg = "<div class='alert alert-danger'>Erro ao cancelar a marcação.</div>";
            }
        } else {
            $msg = "<div class='alert alert-warning'>Não foi possível cancelar. A marcação pode já ter ocorrido ou não existe.</div>";
        }
    }
}

// Consulta corrigida: 'title' -> 'nome', 'start' -> 'data_hora_inicio', 'id_cliente' -> 'utilizador_id'
$query = "SELECT m.id, s.nome AS servico_nome, m.data_hora_inicio, m.estado 
          FROM marcacoes m 
          JOIN servicos s ON m.servico_id = s.servico_id 
          WHERE m.utilizador_id = ? 
          ORDER BY m.data_hora_inicio DESC";

$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$marcacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
$page_title = 'Minhas Marcações - Souto Barbearia';
$body_class = 'agenda-body-background';
$header_style = 'barber';
require_once 'templates/header.php';
?>

<div class="marcacoes-container">
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 class="mb-4" style="color: #fff; text-shadow: 2px 2px 4px rgba(0,0,0,0.8);">As Minhas Marcações</h2>
    </div>
    
    <?php echo $msg; ?>

    <div class="marcacoes-panel">
        <?php if (count($marcacoes) > 0): ?>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Data e Hora</th>
                            <th>Serviço</th>
                            <th>Estado</th>
                            <th style="text-align: center;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($marcacoes as $m): 
                            $data = new DateTime($m['data_hora_inicio']);
                            $pode_cancelar = in_array($m['estado'], ['pendente', 'confirmada']);
                            
                            // Define a classe CSS baseada no estado
                            $status_class = 'status-' . $m['estado'];
                        ?>
                        <tr>
                            <td data-label="Data e Hora">
                                <span style="font-weight: bold; font-size: 1.1em;"><?php echo $data->format('d/m/Y'); ?></span><br>
                                <span style="font-size: 0.9em; opacity: 0.8;"><?php echo $data->format('H:i'); ?></span>
                            </td>
                            <td data-label="Serviço" style="font-family: 'Montserrat', sans-serif;"><?php echo htmlspecialchars($m['servico_nome']); ?></td>
                            <td data-label="Estado">
                                <span class="status-badge <?php echo $status_class; ?>">
                                    <?php echo ucfirst($m['estado']); ?>
                                </span>
                            </td>
                            <td data-label="Ações" style="text-align: center;">
                                <?php if ($pode_cancelar): ?>
                                    <form method="POST" class="form-cancelar" style="display:inline;">
                                        <input type="hidden" name="action" value="cancelar">
                                        <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
                                        <button type="submit" class="btn-cancelar">Cancelar</button>
                                    </form>
                                <?php else: ?> 
                                    <span style="opacity: 0.3;">-</span> 
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p style="color: #fff; text-align: center; padding: 20px;">Ainda não tem marcações efetuadas.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>