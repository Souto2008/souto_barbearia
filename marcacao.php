<?php
/**
 * Ficheiro: marcacao.php
 * Objetivo: Apresentar o formulário de marcação para clientes que já fizeram login.
 */

// 1. INICIAR SESSÃO E VERIFICAR AUTENTICAÇÃO
// Inicia a sessão para poder aceder a variáveis como $_SESSION['id'].
session_start();

// Se o ID do utilizador não estiver na sessão, significa que não está logado.
// Redireciona-o para a página de login com uma mensagem de erro.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?erro=acesso_negado");
    exit(); // Termina o script para segurança.
}

// 2. Inclui os ficheiros necessários.
require __DIR__ . '/config/db.php'; // Para a variável $pdo

?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Marcação de Serviços - Souto Barbearia</title>

<link rel="stylesheet" href="styles.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

</head>
<body class="form-page">

    <div class="form-container" style="max-width: 600px;">
        <a href="index.php" class="form-logo"><h1>Souto Barbearia</h1></a>
        <h2>Nova Marcação</h2>

        <?php // 3. EXIBIÇÃO DE MENSAGENS DE SUCESSO OU ERRO ?>
        <?php // Verifica se existem parâmetros 'sucesso' ou 'erro' na URL (enviados pelo processa_marcacao.php). ?>
        <?php if (isset($_GET['sucesso']) || isset($_GET['erro'])): ?>
            <div class="alert <?php echo isset($_GET['sucesso']) ? 'alert-success' : 'alert-danger'; ?>">
                <?php 
                    if (isset($_GET['sucesso'])) {
                        // Mensagem de sucesso.
                        echo "Marcação agendada com sucesso!";
                    } elseif (isset($_GET['erro'])) {
                        $erro = $_GET['erro'];
                        switch ($erro) {
                            case 'ocupado':
                                echo "O horário selecionado já não está disponível. Por favor, escolha outro.";
                                break;
                            case 'db':
                                echo "Ocorreu um erro ao guardar a sua marcação. Tente novamente.";
                                break;
                            case 'data_passada':
                                echo "Não é possível agendar uma marcação para essa data.";
                                break;
                            case 'hora_passada':
                                echo "Não é possível agendar uma marcação para essa hora.";
                                break;
                            default:
                                echo "Ocorreu um erro inesperado.";
                                break;
                        }
                    }
                ?>
            </div>
        <?php endif; ?>

        <form action="processa_marcacao.php" method="POST">

            <?php
            // 4. BUSCAR SERVIÇOS ATIVOS (com a duração e ID)
            // Este bloco foi movido para cima para que o campo "Serviço" apareça primeiro.
            try {
                $stmt_servicos = $pdo->query("SELECT servico_id, nome, duracao_minutos, preco FROM servicos WHERE ativo = 1 ORDER BY nome ASC");
                $servicos = $stmt_servicos->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                // Em caso de erro, avisa e usa um array vazio
                echo "<p class='form-error'>Erro ao carregar os serviços: " . htmlspecialchars($e->getMessage()) . "</p>";
                $servicos = [];
            }
            ?>

            <div class="form-group">
                <label class="center-label">Selecione o Serviço:</label>
<br>
                <!-- Menu de Categorias -->
                <div class="categoria-tabs">
                    <button type="button" class="categoria-btn active" onclick="mostrarCategoria('cabelo', this)">Cabelo</button>
                    <button type="button" class="categoria-btn" onclick="mostrarCategoria('barba', this)">Barba</button>
                    <button type="button" class="categoria-btn" onclick="mostrarCategoria('combos', this)">Combos</button>
                </div>

                <?php
                // Organizar serviços por categoria baseada no nome
                $cats = ['cabelo' => [], 'barba' => [], 'combos' => []];
                foreach ($servicos as $s) {
                    $nome = mb_strtolower($s['nome']);
                    if (strpos($nome, '+') !== false || strpos($nome, 'combo') !== false) {
                        $cats['combos'][] = $s;
                    } elseif (strpos($nome, 'barba') !== false || strpos($nome, 'navalha') !== false) {
                        $cats['barba'][] = $s;
                    } else {
                        $cats['cabelo'][] = $s;
                    }
                }
                ?>

                <!-- Listas de Serviços (Cards) -->
                <?php foreach ($cats as $key => $items): ?>
                    <div id="lista-<?php echo $key; ?>" class="servicos-lista <?php echo $key === 'cabelo' ? 'active' : ''; ?>">
                        <?php foreach ($items as $item): ?>
                            <div class="servico-card" onclick="selecionarServico(<?php echo $item['servico_id']; ?>, this)">
                                <h4 style="font-size: 0.95rem; margin-bottom: 5px; color: var(--cor-secundaria);"><?php echo htmlspecialchars($item['nome']); ?></h4>
                                <div style="color: var(--cor-destaque); font-weight: bold;"><?php echo number_format($item['preco'], 2); ?> €</div>
                                <small style="color: #888;"><?php echo $item['duracao_minutos']; ?> min</small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>

                <!-- Select Original (Escondido para manter compatibilidade com scripts.js) -->
                <select name="servico_id" id="servico" required style="display: none;">
                    <option value="" disabled selected>Escolha o serviço desejado</option>
                    <?php foreach ($servicos as $servico): ?>
                        <option 
                            value="<?php echo htmlspecialchars($servico['servico_id']); ?>" 
                            data-duracao="<?php echo htmlspecialchars($servico['duracao_minutos']); ?>">
                            <?php echo htmlspecialchars($servico['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <script>
                function mostrarCategoria(cat, btn) {
                    document.querySelectorAll('.servicos-lista').forEach(el => el.classList.remove('active'));
                    document.querySelectorAll('.categoria-btn').forEach(el => el.classList.remove('active'));
                    document.getElementById('lista-' + cat).classList.add('active');
                    btn.classList.add('active');
                }

                function selecionarServico(id, card) {
                    document.querySelectorAll('.servico-card').forEach(el => el.classList.remove('selected'));
                    card.classList.add('selected');
                    const select = document.getElementById('servico');
                    select.value = id;
                    select.dispatchEvent(new Event('change')); // Dispara evento para o scripts.js atualizar as horas
                }
            </script>

            <?php // 4. CAMPOS DO FORMULÁRIO ?>
            <div class="form-group">
                <label for="data">Data:</label>
                <div class="date-wrapper">
                    <input type="date" name="data_marcacao" id="data" required>
                    <svg class="calendar-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </div>
            </div>

            <div class="form-group">
                <label for="hora">Horário:</label>
                <!-- Input escondido para enviar o valor no formulário -->
                <input type="hidden" name="hora_marcacao" id="hora" required>
                <!-- Container onde os botões de horário vão aparecer -->
                <div id="horarios-container" class="horarios-grid">
                    <div class="msg-horarios">Escolha o serviço e a data para ver os horários.</div>
                </div>
            </div>

            <button type="submit" class="cta-button">Agendar</button>
        </form>
        <p class="form-switch">Voltar à <a href="index.php">página inicial</a>.</p>
    </div>
    
    <?php // Inclui o ficheiro JavaScript que controla a lógica dinâmica desta página. ?>
    <script src="scripts.js"></script>

</body>
</html>