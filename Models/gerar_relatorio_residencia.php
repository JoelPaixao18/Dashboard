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
$query_residencias = "SELECT id, 
                            typeResi, 
                            typology, 
                            location, 
                            price, 
                            houseSize, 
                            livingRoomCount, 
                            bathroomCount,
                            kitchenCount,
                            quintal,
                            andares,
                            garagem,
                            hasWater,
                            hasElectricity, 
                            status 
                        FROM residencia ORDER BY id ASC";
$result_residencias = $conn->prepare($query_residencias);
$result_residencias->execute();

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
        .price {
            text-align: right;
            font-family: "Courier New", monospace;
        }
        .status-active {
            color: #1cc88a;
            font-weight: bold;
        }
        .status-inactive {
            color: #e74a3b;
            font-weight: bold;
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
            <h1 class="title">Relatório de Imóveis</h1>
            <div class="subtitle">Inventário Completo de Propriedades</div>
            <div class="report-info">
                Gerado em: '.date('d/m/Y H:i:s').'<br>
                Total de imóveis: '.$result_residencias->rowCount().'
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th >Tipo</th>
                <th>Área (m²)</th>
                <th >Localização</th>
                <th >Valor (kz)</th>
                <th >Tipologia</th>
                <th>Salas</th>
                <th >Cozinhas</th>
                <th >Banheiros</th>
                <th >Quintal</th>
                <th >Andares</th>
                <th >Garagem</th>
                <th>Água</th>
                <th >Energia</th>
                <th >Status</th>
            </tr>
        </thead>
        <tbody>';

while ($row_residencia = $result_residencias->fetch(PDO::FETCH_ASSOC)) {
    // Formata o status com classe CSS diferente
    $status_class = ($row_residencia['status'] == 'Ativo') ? 'status-active' : 'status-inactive';
    
    // Formata o valor monetário
    $formatted_price = number_format($row_residencia['price'], 2, ',', '.') . ' kz';
    
    $html .= '<tr>
                <td>' . $row_residencia['id'] . '</td>
                <td>' . $row_residencia['typeResi'] . '</td>
                <td>' . ($row_residencia['houseSize'] ?? 'N/A') . '</td>
                <td>' . $row_residencia['location'] . '</td>
                <td class="price">' . $formatted_price . '</td>
                <td>' . $row_residencia['typology'] . '</td>
                <td>' . ($row_residencia['livingRoomCount'] ?? 'N/A') . '</td>
                <td>' . ($row_residencia['kitchenCount'] ?? 'N/A') . '</td>
                <td>' . ($row_residencia['bathroomCount'] ?? 'N/A') . '</td>
                                <td>' . ($row_residencia['quintal'] ?? 'N/A') . '</td>
                <td>' . ($row_residencia['andares'] ?? 'N/A') . '</td>
                <td>' . ($row_residencia['garagem'] ?? 'N/A') . '</td>
                <td>' . ($row_residencia['hasWater'] ?? 'N/A') . '</td>
                <td>' . ($row_residencia['hasElectricity'] ?? 'N/A') . '</td>
                <td class="' . $status_class . '">' . $row_residencia['status'] . '</td>
              </tr>';
}

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
$dompdf->setPaper('A2', 'portrait');
$dompdf->render();

// Exibir o PDF no navegador
$dompdf->stream("Relatorio_Residencias.pdf", ["Attachment" => false]);