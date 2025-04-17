<?php
require_once '../Config/conection.php';
require_once '../dompdf/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Configuração do Dompdf
$options = new Options();
$options->set('defaultFont', 'Arial');
$dompdf = new Dompdf($options);

// Consulta ao banco de dados
$query_residencias = "SELECT id, typeResi, typology, location, price, status FROM residencia ORDER BY id ASC";
$result_residencias = $conn->prepare($query_residencias);
$result_residencias->execute();

// Construção do HTML do relatório
$html = '<h1 style="text-align: center;">Relatório dos Imóveis</h1>';
$html .= '<table border="1" width="100%" style="border-collapse: collapse;">
             <tr>
                 <th>ID</th>
                 <th>Tipo de Imóvel</th>
                 <th>Tipologia do Imóvel
                 <th>Localização</th>
                 <th>Valor Avaliado</th>
                <th>Status</th>
             </tr>';

while ($row_residencia = $result_residencias->fetch(PDO::FETCH_ASSOC)) {
    $html .= '<tr>
                 <td>' . $row_residencia['id'] . '</td>
                 <td>' . $row_residencia['typeResi'] . '</td>
                 <td>' . $row_residencia['typology'] . '</td>
                 <td>' . $row_residencia['location'] . '</td>
                 <td>' . $row_residencia['price'] . '</td>
                <td>' . $row_residencia['status'] . '</td>
              </tr>';
}

$html .= '</table>';

// Gerar o PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Exibir o PDF no navegador
$dompdf->stream("Relatorio_Residencias.pdf", ["Attachment" => false]);
