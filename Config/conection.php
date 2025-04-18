<?php

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "resingola";

try {
    $conn = new PDO("mysql:host=$host;dbname=" . $dbname, $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Boa prática
} catch (PDOException $err) {
    die("Erro: Conexão Falhou - " . $err->getMessage());
}

// Função para contar registros
function contar($conn, $tabela, $condicao = null) {
    $sql = "SELECT COUNT(*) FROM $tabela";
    if ($condicao) $sql .= " WHERE $condicao";
    return (int)$conn->query($sql)->fetchColumn();
}

// Coletando dados do banco de dados
$usuarios = contar($conn, 'usuario');
$venda = contar($conn, 'residencia', "status = 'venda'");
$renda = contar($conn, 'residencia', "status = 'arrendamento'");

// Calculando percentuais
$totalGeral = $usuarios + $venda + $renda;
$totalResi = $venda + $renda;
$percentUsuarios = $totalGeral > 0 ? ($usuarios / $totalGeral) * 100 : 0;
$percentVenda = $totalResi > 0 ? ($venda / $totalResi) * 100 : 0;
$percentRenda = $totalResi > 0 ? ($renda / $totalResi) * 100 : 0;

?>