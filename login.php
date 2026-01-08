<?php
/**
 * Ficheiro: login.php
 * 
 * Objetivo: Gerir o processo de login dos clientes.
 * Valida as credenciais contra a base de dados e cria uma sessão se forem corretas.
 */

// Inicia a sessão para poder manipular as variáveis `$_SESSION`.
session_start();

// Se o utilizador já tiver uma sessão ativa (já está logado), redireciona-o para a página inicial.
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'barbeiro') {
        header("Location: agenda_barbeiro.php"); // Barbeiro vai para a sua agenda
    } else {
        header("Location: index.php"); // Cliente vai para a página inicial
    }
    exit();
}

// Inclui o ficheiro de conexão para poder comunicar com a base de dados.
include __DIR__ . '/config/db.php';

// Variável para armazenar mensagens de erro que serão mostradas ao utilizador.
$erro_login = "";

// Verifica se o pedido foi feito usando o método POST, o que indica que o formulário foi submetido.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se os campos de email e password não estão vazios.
    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        
        $email = $_POST['email'];
        $password_digitada = $_POST['password'];

        // Usa PDO com prepared statements para segurança contra injeção de SQL.
        $stmt = $pdo->prepare("SELECT id, nome, email, telefone, password_hash, tipo FROM utilizadores WHERE email = ?");
        $stmt->execute([$email]);
        $utilizador = $stmt->fetch();

        if ($utilizador && password_verify($password_digitada, $utilizador['password_hash'])) {
            // LOGIN BEM-SUCEDIDO: Regenera o ID da sessão para evitar session fixation.
            session_regenerate_id(true);

            // Armazena os dados essenciais na sessão.
            $_SESSION['user_id'] = $utilizador['id'];
            $_SESSION['user_name'] = $utilizador['nome'];
            $_SESSION['user_type'] = $utilizador['tipo'];

            // Redireciona com base no tipo de utilizador.
            if ($utilizador['tipo'] == 'barbeiro') {
                header("Location: agenda_barbeiro.php");
            } else {
                header("Location: index.php");
            }
            exit();
            
        } else {
            // MENSAGEM DE ERRO GENÉRICA: Não especifica se foi o utilizador ou a password que falhou.
            $erro_login = "Email ou palavra-passe inválidos.";
        }
    } else {
        $erro_login = "Por favor, preencha o email e a palavra-passe.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Souto Barbearia</title>
    <link rel="icon" type="image/x-icon" href="imagens/farol.jpg">
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
</head>
<body class="form-page">

    <div class="form-container">
        <a href="index.php" class="form-logo"><h1>Souto Barbearia</h1></a>
        <h2>Login</h2>

        <!-- Mensagem de sucesso mostrada se o utilizador for redirecionado da página de registo. -->
        <?php if (isset($_GET['registo']) && $_GET['registo'] == 'sucesso'): ?>
            <p class="form-success">
                Registo efetuado com sucesso! Faça login para continuar.
            </p>
        <?php endif; ?>

        <!-- Mostra a mensagem de erro, se existir alguma. -->
        <?php if (!empty($erro_login)): ?>
            <p class="form-error">
                <?php echo $erro_login; ?>
            </p>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Palavra-passe</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" required>
                    <span class="toggle-password">Mostrar</span>
                </div>
                <small id="caps-lock-warning" style="display: none; color: orange; margin-top: 5px;">Caps Lock está ativo!</small>
            </div>
            <button type="submit" class="cta-button">Entrar</button>
        </form>
        <p class="form-switch">Não tem conta? <a href="registo.php">Registe-se aqui</a>.</p>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const togglePassword = document.querySelector('.toggle-password');
        const capsLockWarning = document.getElementById('caps-lock-warning');

        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.textContent = type === 'password' ? 'Mostrar' : 'Esconder';
        });

        passwordInput.addEventListener('keyup', function (event) {
            if (event.getModifierState('CapsLock')) {
                capsLockWarning.style.display = 'block';
            } else {
                capsLockWarning.style.display = 'none';
            }
        });
    </script>
</body>
</html>