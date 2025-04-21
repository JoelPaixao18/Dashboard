<?php
require_once '../Config/conection.php';
require_once '../dompdf/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Configuração do Dompdf
$options = new Options();
$options->set('defaultFont', 'Arial');
$options->set('isRemoteEnabled', true); // Permite carregar imagens externas
$dompdf = new Dompdf($options);

// Consulta ao banco de dados
$query_proprietarios = "SELECT id, nome, email, tel, BI, endereco FROM proprietario ORDER BY id ASC";
$result_proprietarios = $conn->prepare($query_proprietarios);
$result_proprietarios->execute();

// Construção do HTML do relatório
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #4e73df;
            padding-bottom: 10px;
        }
        .logo {
            height: 60px;
        }
        .title {
            text-align: center;
            color: #2e59d9;
            margin: 10px 0;
            font-size: 22px;
        }
        .subtitle {
            text-align: center;
            color: #4e73df;
            font-size: 16px;
            margin-bottom: 15px;
        }
        .report-info {
            text-align: right;
            font-size: 12px;
            color: #666;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 14px;
        }
        th {
            background-color: #4e73df;
            color: white;
            padding: 12px 10px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f8f9fc;
        }
        .contact-info {
            white-space: nowrap;
        }
        .document {
            font-family: "Courier New", monospace;
            font-size: 13px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .total-count {
            background-color: #f8f9fc;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <img class="logo" src="../Views/Dashboard-main/img/logo_resi.png" alt="Logo">
        <div>
            <h1 class="title">Relatório de Proprietários</h1>
            <div class="subtitle">Cadastro Completo de Proprietários</div>
            <div class="report-info">
                Gerado em: '.date('d/m/Y H:i:s').'<br>
                Total de proprietários: '.$result_proprietarios->rowCount().'
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome Completo</th>
                <th>E-mail</th>
                <th>Contato</th>
                <th>Documento</th>
                <th>Endereço</th>
            </tr>
        </thead>
        <tbody>';

while ($row_proprietario = $result_proprietarios->fetch(PDO::FETCH_ASSOC)) {
    $html .= '<tr>
                 <td>' . $row_proprietario['id'] . '</td>
                 <td>' . $row_proprietario['nome'] . '</td>
                 <td>' . $row_proprietario['email'] . '</td>
                 <td class="contact-info">' . $row_proprietario['tel'] . '</td>
                 <td class="document">' . $row_proprietario['BI'] . '</td>
                 <td>' . $row_proprietario['endereco'] . '</td>
              </tr>';
}

// Linha com total de registros
$html .= '<tr class="total-count">
             <td colspan="6" style="text-align: center;">
                 Total de proprietários listados: '.$result_proprietarios->rowCount().'
             </td>
          </tr>';

$html .= '
        </tbody>
    </table>

    <div class="footer">
        Sistema de Gestão Imobiliária - © ' . date('Y') . ' Resi. Todos os direitos reservados.
    </div>
</body>
</html>';

// Gerar o PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Exibir o PDF no navegador
$dompdf->stream("Relatorio_Proprietarios.pdf", ["Attachment" => false]);