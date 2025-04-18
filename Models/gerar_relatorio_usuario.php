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
$query_usuarios = "SELECT id, nome, email, role FROM usuario ORDER BY id ASC";
$result_usuarios = $conn->prepare($query_usuarios);
$result_usuarios->execute();

// Construção do HTML do relatório
$html = '<img src="../Views/imgs/logo_resi.png" alt="Logo">';
$html = '<h1 style="text-align: center;"> Usuários Cadastrados na API</h1>';
$html .= '<table border="1" width="100%" style="border-collapse: collapse;">
             <tr>
                 <th>ID</th>
                 <th>Nome</th>
                 <th>Email</th>
                 <th>Role</th>
             </tr>';

while ($row_usuario = $result_usuarios->fetch(PDO::FETCH_ASSOC)) {
    $html .= '<tr>
                 <td>' . $row_usuario['id'] . '</td>
                 <td>' . $row_usuario['nome'] . '</td>
                 <td>' . $row_usuario['email'] . '</td>
                 <td>' . $row_usuario['role'] . '</td>
              </tr>';
}

$html .= '</table>';

// Gerar o PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Exibir o PDF no navegador
$dompdf->stream("Relatorio_Usuarios.pdf", ["Attachment" => false]);
