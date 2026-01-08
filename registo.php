<?php
/**
 * Ficheiro: registo.php
 * 
 * Objetivo: Permitir que novos clientes criem uma conta.
 * Valida os dados, criptografa a senha e insere o novo cliente na base de dados.
 */

// 1. Inclui o ficheiro de conexão para interagir com a base de dados.
// Usamos require_once para garantir que o ficheiro é incluído apenas uma vez.
require_once __DIR__ . '/config/db.php';
$erro_formulario = ""; // Variável para armazenar mensagens de erro do formulário.

// 2. Verifica se o pedido foi feito via método POST, indicando uma submissão de formulário.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validação: Verifica se os campos essenciais (nome, email, password) não estão vazios.
    if (!empty($_POST['nome']) && !empty($_POST['email']) && !empty($_POST['telefone']) && !empty($_POST['password']) && !empty($_POST['confirm_password'])) {
        
        // 3. Recolhe e limpa os dados do formulário para segurança.
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $telefone = $_POST['telefone']; // Recolhe o telefone
        
        // 4. VERIFICAÇÃO: Verifica se o email já existe na base de dados para evitar duplicados.
        $stmt_verificar = $pdo->prepare("SELECT id FROM utilizadores WHERE email = ?");
        $stmt_verificar->execute([$email]);

        if ($stmt_verificar->fetch()) {
            // Se o email já existe, define uma mensagem de erro amigável.
            $erro_formulario = "Este email já está registado. Por favor, <a href='login.php'>faça login</a> ou use outro email.";
        } elseif ($password !== $confirm_password) {
            $erro_formulario = "As palavras-passe não coincidem.";
        } else {
            // 5. Se o email não existe, continua com o registo usando PDO.
            
            // Criptografa a senha. É um passo de segurança CRUCIAL.
            // password_hash() cria um hash seguro da senha.
            $password_hash_valor = password_hash($password, PASSWORD_DEFAULT);

            // 6. Prepara a consulta SQL para inserir os dados do novo utilizador na tabela `utilizadores`.
            $stmt_inserir = $pdo->prepare("INSERT INTO utilizadores (nome, email, telefone, password_hash, tipo) VALUES (?, ?, ?, ?, 'cliente')");

            // 7. Executa a consulta de inserção.
            if ($stmt_inserir->execute([$nome, $email, $telefone, $password_hash_valor])) {
                // Se a inserção for bem-sucedida, redireciona o utilizador para a página de login.
                // O parâmetro `?registo=sucesso` na URL permite mostrar uma mensagem de sucesso na página de login.
                header("Location: login.php?registo=sucesso");
                exit();
            } else {
                // Se a consulta de inserção falhar por outro motivo, guarda uma mensagem de erro genérica.
                $erro_formulario = "Ocorreu um erro ao criar a sua conta. Por favor, tente novamente.";
            }
        }
        
    } else {
        $erro_formulario = "Por favor, preencha todos os campos.";
    }
}

?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registo - Souto Barbearia</title>
    <link rel="icon" type="image/x-icon" href="imagens/farol.jpg">
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
</head>
<body class="form-page">

    <div class="form-container">
        <a href="index.php" class="form-logo"><h1>Souto Barbearia</h1></a>
        <h2>Criar Conta</h2>

        <!-- Se a variável de erro não estiver vazia, mostra a mensagem de erro. -->
        <?php if (!empty($erro_formulario)) { ?>
            <p class="form-error">
                <?php echo $erro_formulario; // Usamos echo aqui para permitir HTML na mensagem de erro (como o link para login) ?>
            </p>
        <?php } ?>

        <form action="registo.php" method="POST">
            <div class="form-group">
                <label for="name">Nome</label>
                <input type="text" id="name" name="nome" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="telefone">Telefone</label>
                <input type="tel" id="telefone" name="telefone" required>
            </div>
            <div class="form-group">
                <label for="password">Palavra-passe</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" required>
                    <span class="toggle-password">Mostrar</span>
                </div>
                <small id="caps-lock-warning" style="display: none; color: orange; margin-top: 5px;">Caps Lock está ativo!</small>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirmar Palavra-passe</label>
                <div class="password-wrapper">
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <span class="toggle-password">Mostrar</span>
                </div>
            </div>
            <button type="submit" class="cta-button">Registar</button>
        </form>
        <p class="form-switch">Já tem conta? <a href="login.php">Faça login aqui.</a></p>
    </div>

    <script>
        const togglePasswords = document.querySelectorAll('.toggle-password');
        const capsLockWarning = document.getElementById('caps-lock-warning');
        const passwordInput = document.getElementById('password');

        togglePasswords.forEach(toggle => {
            toggle.addEventListener('click', function () {
                const input = this.previousElementSibling;
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                this.textContent = type === 'password' ? 'Mostrar' : 'Esconder';
            });
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