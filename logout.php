<?php
/**
 * Ficheiro: logout.php
 * 
 * Objetivo: Terminar a sessão do utilizador de forma segura.
 */

// 1. Inicia ou retoma a sessão existente.
// É necessário para poder aceder e modificar os dados da sessão atual.
session_start();

// 2. Limpa todas as variáveis da sessão.
// A função session_unset() remove todas as variáveis registadas na sessão (ex: $_SESSION['cliente_id']).
session_unset();

// 3. Destrói completamente a sessão.
// A função session_destroy() elimina o ficheiro da sessão no servidor.
session_destroy();

// 4. Redireciona o utilizador para a página inicial.
// Como a sessão foi destruída, o utilizador está efetivamente deslogado.
header("Location: index.php");
exit();