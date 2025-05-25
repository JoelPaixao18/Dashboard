<?php
require_once '../Config/conection.php';
require_once '../vendor/autoload.php'; // Inclua o autoload do TCPDF

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: ../Views/index.php");
    exit();
}

// Criar novo documento PDF
//$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);

// Informações do documento
$pdf->SetCreator('Sistema Residências');
$pdf->SetAuthor('Administrador');
$pdf->SetTitle('Relatório Geral de Usuários e Imóveis');
$pdf->SetSubject('Relatório PDF');
$pdf->SetKeywords('TCPDF, PDF, relatório, imóveis, usuários');

// Adicionar uma página
$pdf->AddPage();

// Logo e título
$pdf->Image('../Views/Dashboard-main/img/logo_resi.png', 10, 10, 30, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 15, 'Relatório Geral - Usuários e Imóveis', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 10, 'Emitido em: ' . date('d/m/Y H:i'), 0, 1, 'C');
$pdf->Ln(10);

// Consulta para obter os dados
$query = "SELECT 
            u.id AS usuario_id, u.nome, u.email, u.tel, u.BI,
            r.id AS residencia_id, r.typeResi, r.typology, r.location, 
            r.price, r.status, r.houseSize, r.data_cadastro AS data_cadastro_residencia
          FROM usuario u
          LEFT JOIN residencia r ON u.id = r.usuario_id
          ORDER BY u.nome ASC, r.data_cadastro DESC";

$result = $conn->prepare($query);
$result->execute();

// Variável para controlar o usuário atual
$current_user = null;

// Definir estilos
$pdf->SetFillColor(240, 240, 240);
$pdf->SetFont('helvetica', 'B', 10);

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    // Se é um novo usuário, mostra cabeçalho
    if ($current_user !== $row['usuario_id']) {
        $current_user = $row['usuario_id'];
        
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Usuário: ' . $row['nome'], 0, 1, 'L', true);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 6, 'Email: ' . $row['email'], 0, 1);
        $pdf->Cell(0, 6, 'Telefone: ' . $row['tel'], 0, 1);
        $pdf->Cell(0, 6, 'BI: ' . $row['BI'], 0, 1);
        $pdf->Ln(3);
        
        // Cabeçalho da tabela de imóveis
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(30, 7, 'Tipo', 1, 0, 'C');
        $pdf->Cell(30, 7, 'Tipologia', 1, 0, 'C');
        $pdf->Cell(50, 7, 'Localização', 1, 0, 'C');
        $pdf->Cell(30, 7, 'Valor', 1, 0, 'C');
        $pdf->Cell(30, 7, 'Status', 1, 0, 'C');
        $pdf->Cell(30, 7, 'Tamanho', 1, 0, 'C');
        $pdf->Cell(40, 7, 'Data Cadastro', 1, 1, 'C');
    }
    
    // Se tem imóvel, mostra dados
    if (!empty($row['residencia_id'])) {
        $pdf->SetFont('helvetica', '', 9);
        
        // Formatar dados
        $price = is_numeric($row['price']) ? 'Kz ' . number_format($row['price'], 2, ',', '.') : 'N/A';
        $houseSize = !empty($row['houseSize']) ? $row['houseSize'] . ' m²' : 'N/A';
        $data_cadastro = !empty($row['data_cadastro_residencia']) ? 
            date('d/m/Y H:i', strtotime($row['data_cadastro_residencia'])) : 'N/A';
        
        $pdf->Cell(30, 7, $row['typeResi'], 1, 0, 'L');
        $pdf->Cell(30, 7, $row['typology'], 1, 0, 'C');
        $pdf->Cell(50, 7, $row['location'], 1, 0, 'L');
        $pdf->Cell(30, 7, $price, 1, 0, 'R');
        $pdf->Cell(30, 7, $row['status'], 1, 0, 'C');
        $pdf->Cell(30, 7, $houseSize, 1, 0, 'C');
        $pdf->Cell(40, 7, $data_cadastro, 1, 1, 'C');
    } else {
        $pdf->Cell(0, 7, 'Nenhum imóvel cadastrado', 1, 1, 'C');
    }
    
    $pdf->Ln(5);
}

// Saída do PDF
$pdf->Output('relatorio_geral_usuarios_imoveis.pdf', 'I');
?>