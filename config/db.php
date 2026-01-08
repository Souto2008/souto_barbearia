<?php
/**
 * Ficheiro: config/db.php
 * Objetivo: Estabelecer a conexão com a base de dados MySQL usando PDO.
 *           Este ficheiro é incluído em todos os outros que precisam de aceder à BD.
 */

// 1. DEFINIÇÃO DAS CREDENCIAIS DA BASE DE DADOS
// Estas variáveis guardam as informações necessárias para a conexão.
$DB_HOST = "localhost";   // O endereço do servidor da base de dados (normalmente 'localhost' em desenvolvimento).
$DB_NAME = "soutobarber"; // O nome da base de dados que queremos usar.
$DB_USER = "root";        // O nome de utilizador para aceder à base de dados.
$DB_PASS = "";            // A password do utilizador (em desenvolvimento com XAMPP, 'root' geralmente não tem password).

// 2. TENTATIVA DE CONEXÃO COM PDO
// O bloco `try...catch` é usado para tentar executar um código que pode gerar um erro (uma exceção).
// Se um erro ocorrer dentro do `try`, a execução é interrompida e o bloco `catch` é executado.
try {
    // Cria uma nova instância da classe PDO (PHP Data Objects) para estabelecer a conexão.
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", // DSN (Data Source Name): tipo de BD, host, nome da BD e charset. `utf8mb4` é recomendado para suportar todos os caracteres, incluindo emojis.
        $DB_USER, // Utilizador da BD.
        $DB_PASS, // Password da BD.
        [
            // 3. CONFIGURAÇÃO DAS OPÇÕES DO PDO
            // ATTR_ERRMODE: Define como o PDO reporta erros.
            // ERRMODE_EXCEPTION: Lança exceções (erros) que podem ser apanhadas pelo bloco `catch`. É a forma mais robusta de lidar com erros.
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
            
            // ATTR_DEFAULT_FETCH_MODE: Define o modo como os resultados das consultas são devolvidos.
            // FETCH_ASSOC: Devolve os resultados como um array associativo, onde as chaves são os nomes das colunas da tabela (ex: $linha['nome']).
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
            
            // ATTR_EMULATE_PREPARES: Desativa a emulação de prepared statements.
            // `false`: Força o PDO a usar prepared statements nativos do MySQL, o que oferece uma camada extra de proteção contra injeção de SQL.
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    // Se a conexão falhar, o bloco `catch` é executado.
    // `die()` termina a execução do script e exibe uma mensagem de erro, impedindo que o site tente operar sem uma conexão à BD.
    die("Erro ao ligar à Base de Dados: " . $e->getMessage());
}
?>