<?php
/**
 * Ficheiro: header.php
 *
 * Objetivo: Cabeçalho reutilizável para todas as páginas.
 * Inicia a sessão, verifica o estado de autenticação e contém o HTML do topo da página.
 *
 * Utilização:
 * Definir a variável $page_title antes de incluir o ficheiro para um título de página customizado.
 * Definir a variável $header_style = 'minimal' para um cabeçalho simplificado (usado em galeria, serviços, etc.).
 */

// Inicia ou continua uma sessão PHP.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---- Definições de Variáveis ----
// Garante que as variáveis de login existem para evitar erros.
$is_logged_in = isset($_SESSION['user_id']);
$is_barbeiro = isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'barbeiro';
$is_cliente = isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'cliente';

// Define um título padrão se não for especificado na página.
$page_title = isset($page_title) ? $page_title : 'Souto Barbearia';

// Define o estilo do header. O padrão é 'full'.
$header_style = isset($header_style) ? $header_style : 'full';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="imagens/farol.jpg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="<?php echo isset($body_class) ? $body_class : ''; ?>">

<?php if ($header_style == 'minimal'): ?>
    <div class="servicos-header"> <!-- Usando a classe de servicos.php que é genérica -->
        <h1><?php echo htmlspecialchars($page_title); ?></h1>
        <a href="index.php" class="back-button">Voltar ao Início</a>
    </div>
<?php elseif ($header_style == 'barber'): ?>
    <header>
        <div class="header-content">
            <a href="index.php"><h1>Souto Barbearia</h1></a>
            <nav>
                <ul>
                    <li class="login-item"><a href="index.php" id="voltar-link">Voltar</a></li>
                </ul>
            </nav>
        </div>
    </header>
<?php else: // O estilo 'full' é o padrão ?>
    <header>
        <div class="header-content">
            <a href="index.php"><h1>Souto Barbearia</h1></a>
            <nav>
                <ul>
                    <li><a href="index.php#sobre">Sobre Nós</a></li>
                    <li><a href="index.php#galeria">Galeria</a></li>
                    <li><a href="index.php#contactos">Contacto</a></li>
                    <li><a href="servicos.php">Serviços</a></li>
                    <?php if ($is_logged_in): ?>
                        <li style="color: var(--cor-destaque); font-weight: bold; padding: 0.1rem 0;"><?php 
                            $full_name = $_SESSION['user_name'];
                            $first_name = explode(' ', $full_name)[0];
                            echo 'Olá, ' . htmlspecialchars($first_name); 
                        ?></li>
                    <?php endif; ?>
                    <?php if ($is_barbeiro): ?>
                        <li class="login-item"><a href="agenda_barbeiro.php">Agenda</a></li>
                        <li class="login-item"><a href="logout.php" id="logout-link">Logout</a></li>
                    <?php elseif ($is_logged_in): ?>
                        <li class="login-item"><a href="minhas_marcacoes.php">Marcações</a></li>
                        <li class="login-item"><a href="logout.php" id="logout-link">Logout</a></li>
                    <?php else: ?>
                        <li class="login-item"><a href="login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
<?php endif; ?>
