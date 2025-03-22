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
$query_residencias = "SELECT id, zonamento, localizacao, preco, status, descricao FROM residencia ORDER BY id ASC";
$result_residencias = $conn->prepare($query_residencias);
$result_residencias->execute();

// Construção do HTML do relatório
$html = '<h1 style="text-align: center;">Relatório das Residências</h1>';
$html .= '<table border="1" width="100%" style="border-collapse: collapse;">
             <tr>
                 <th>ID</th>
                 <th>Tipo de Residencia</th>
                 <th>Localização</th>
                 <th>Valor Avaliado</th>
                <th>Status</th>
                <th>Descrição</th>
             </tr>';

while ($row_residencia = $result_residencias->fetch(PDO::FETCH_ASSOC)) {
    $html .= '<tr>
                 <td>' . $row_residencia['id'] . '</td>
                 <td>' . $row_residencia['zonamento'] . '</td>
                 <td>' . $row_residencia['localizacao'] . '</td>
                 <td>' . $row_residencia['preco'] . '</td>
                <td>' . $row_residencia['status'] . '</td>
                <td>' . $row_residencia['descricao'] . '</td>
              </tr>';
}

$html .= '</table>';

// Gerar o PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Exibir o PDF no navegador
$dompdf->stream("Relatorio_Residencias.pdf", ["Attachment" => false]);
