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
$query_proprietarios = "SELECT id, nome, email, tel, BI, endereco FROM proprietario ORDER BY id ASC";
$result_proprietarios = $conn->prepare($query_proprietarios);
$result_proprietarios->execute();

// Construção do HTML do relatório
$html = '<h1 style="text-align: center;">Relatório de Proprietários</h1>';
$html .= '<table border="1" width="100%" style="border-collapse: collapse;">
             <tr>
                 <th>ID</th>
                 <th>Nome</th>
                 <th>Email</th>
                 <th>Contato</th>
                <th>BI</th>
                <th>Endereço</th>
             </tr>';

while ($row_proprietario = $result_proprietarios->fetch(PDO::FETCH_ASSOC)) {
    $html .= '<tr>
                 <td>' . $row_proprietario['id'] . '</td>
                 <td>' . $row_proprietario['nome'] . '</td>
                 <td>' . $row_proprietario['email'] . '</td>
                 <td>' . $row_proprietario['tel'] . '</td>
                <td>' . $row_proprietario['BI'] . '</td>
                <td>' . $row_proprietario['endereco'] . '</td>
              </tr>';
}

$html .= '</table>';

// Gerar o PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Exibir o PDF no navegador
$dompdf->stream("Relatorio_Proprietarios.pdf", ["Attachment" => false]);
