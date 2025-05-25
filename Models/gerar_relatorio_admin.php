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
$query_admins = "SELECT id, nome, email, tel, BI, role FROM administrador ORDER BY id ASC";
$result_admins = $conn->prepare($query_admins);
$result_admins->execute();

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
            margin-top: 20px;
        }
        th {
            background-color: #4e73df;
            color: white;
            padding: 10px;
            text-align: left;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f8f9fc;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img class="logo" src="../Views/Dashboard-main/img/logo_resi.png" alt="Logo">
        <div>
            <h1 class="title">Relatório de Administradores</h1>
            <div class="report-info">
                Gerado em: '.date('d/m/Y H:i:s').'
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Contato</th>
                <th>Nº de BI</th>
                <th>Perfil</th>
            </tr>
        </thead>
        <tbody>';

while ($row_admin = $result_admins->fetch(PDO::FETCH_ASSOC)) {
    $html .= '<tr>
                 <td>' . $row_admin['id'] . '</td>
                 <td>' . $row_admin['nome'] . '</td>
                 <td>' . $row_admin['email'] . '</td>
                 <td>' . $row_admin['tel'] . '</td>
                 <td>' . $row_admin['BI'] . '</td>
                 <td>' . $row_admin['role'] . '</td>
              </tr>';
}

$html .= '
        </tbody>
    </table>

    <div class="footer">
        Sistema de Gestão - © ' . date('Y') . ' Resi. Todos os direitos reservados.
    </div>
</body>
</html>';

// Gerar o PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A3', 'portrait');
$dompdf->render();

// Exibir o PDF no navegador
$dompdf->stream("Relatorio_Admins.pdf", ["Attachment" => false]);