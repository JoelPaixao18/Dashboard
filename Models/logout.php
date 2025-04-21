<?php
session_start();

// Limpa TODOS os dados da sessão
$_SESSION = array();

// Destrói a sessão completamente
session_destroy();

// Redireciona para a página de login
header("Location: ../Views/index.php");
exit();
?>