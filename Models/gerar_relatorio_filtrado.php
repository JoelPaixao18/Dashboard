<?php
require_once '../Config/conection.php';
require_once '../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: ../Views/index.php");
    exit();
}

// Processar filtros
$where = "1=1";
$params = [];
$filtrosAtivos = false;

if (!empty($_GET['tipo_imovel'])) {
    $where .= " AND r.typeResi = :tipo_imovel";
    $params[':tipo_imovel'] = $_GET['tipo_imovel'];
    $filtrosAtivos = true;
}
if (!empty($_GET['status'])) {
    $where .= " AND r.status = :status";
    $params[':status'] = $_GET['status'];
    $filtrosAtivos = true;
}
if (!empty($_GET['preco_min']) && is_numeric($_GET['preco_min'])) {
    $where .= " AND CAST(REPLACE(REPLACE(r.price, '.', ''), ',', '.') AS DECIMAL(10,2)) >= :preco_min";
    $params[':preco_min'] = floatval($_GET['preco_min']);
    $filtrosAtivos = true;
}
if (!empty($_GET['preco_max']) && is_numeric($_GET['preco_max'])) {
    $where .= " AND CAST(REPLACE(REPLACE(r.price, '.', ''), ',', '.') AS DECIMAL(10,2)) <= :preco_max";
    $params[':preco_max'] = floatval($_GET['preco_max']);
    $filtrosAtivos = true;
}
if (!empty($_GET['usuario'])) {
    $where .= " AND (u.nome LIKE :usuario OR a.nome LIKE :usuario)";
    $params[':usuario'] = '%' . $_GET['usuario'] . '%';
    $filtrosAtivos = true;
}

// Construir a consulta base dependendo do tipo de usuário selecionado
$baseQuery = "";
if (!empty($_GET['tipo_usuario'])) {
    $tipo = strtolower($_GET['tipo_usuario']);
    if ($tipo === 'admin' || $tipo === 'administrador') {
        $baseQuery = "
            SELECT DISTINCT 
                r.*,
                a.nome as nome_usuario,
                'Administrador' as tipo_usuario
            FROM residencia r 
            LEFT JOIN administrador a ON r.user_id = a.id
            WHERE " . $where;
    } else {
        $baseQuery = "
            SELECT DISTINCT 
                r.*,
                u.nome as nome_usuario,
                'Usuário' as tipo_usuario
            FROM residencia r 
            LEFT JOIN usuario u ON r.user_id = u.id
            WHERE " . $where . " AND u.role = 'user'";
    }
    $filtrosAtivos = true;
} else {
    // Se nenhum tipo de usuário for selecionado, buscar de ambas as tabelas
    $baseQuery = "
        SELECT DISTINCT 
            r.*,
            COALESCE(u.nome, a.nome) as nome_usuario,
            CASE 
                WHEN a.id IS NOT NULL THEN 'Administrador'
                ELSE 'Usuário'
            END as tipo_usuario
        FROM residencia r 
        LEFT JOIN usuario u ON r.user_id = u.id AND u.role = 'user'
        LEFT JOIN administrador a ON r.user_id = a.id
        WHERE " . $where;
}

try {
    // Adicionar ordenação
    $sql = $baseQuery . " ORDER BY r.id DESC";

    $stmt = $conn->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $residencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Se não houver resultados, mostrar mensagem
    if (empty($residencias)) {
        echo "Nenhum resultado encontrado para os filtros selecionados.";
        exit();
    }

    // Configurar DOMPDF
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);
    $options->set('chroot', realpath('../../'));
    $dompdf = new Dompdf($options);

    // Caminho absoluto para o logo
    $logoPath = realpath('../../AGVRR/Views/Dashboard-main/img/logo_resi.png');
    $logoData = base64_encode(file_get_contents($logoPath));

    // Preparar critérios do filtro para exibição
    $criterios = [];
    if (!empty($_GET['tipo_imovel'])) $criterios[] = "Tipo de Imóvel: " . $_GET['tipo_imovel'];
    if (!empty($_GET['status'])) $criterios[] = "Status: " . $_GET['status'];
    if (!empty($_GET['preco_min'])) $criterios[] = "Preço Mínimo: " . number_format($_GET['preco_min'], 2, ',', '.') . " Kz";
    if (!empty($_GET['preco_max'])) $criterios[] = "Preço Máximo: " . number_format($_GET['preco_max'], 2, ',', '.') . " Kz";
    if (!empty($_GET['usuario'])) $criterios[] = "Proprietário: " . $_GET['usuario'];
    if (!empty($_GET['tipo_usuario'])) $criterios[] = "Tipo de Usuário: " . $_GET['tipo_usuario'];

    // Gerar HTML
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Relatório de Imóveis</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
            }
            .header {
                text-align: center;
                margin-bottom: 30px;
            }
            .logo {
                max-width: 150px;
                margin-bottom: 20px;
            }
            .filtros {
                margin: 20px 0;
                padding: 10px;
                background: #f8f9fa;
                border-radius: 5px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
                font-size: 12px;
            }
            th {
                background-color: #4e73df;
                color: white;
            }
            tr:nth-child(even) {
                background-color: #f8f9fc;
            }
            .status-badge {
                padding: 3px 8px;
                border-radius: 12px;
                font-size: 11px;
            }
            .status-venda {
                background: #e8f5e9;
                color: #2e7d32;
            }
            .status-arrendamento {
                background: #e3f2fd;
                color: #1565c0;
            }
            .tipo-badge {
                padding: 3px 8px;
                border-radius: 12px;
                font-size: 11px;
            }
            .tipo-admin {
                background: #fff3e0;
                color: #e65100;
            }
            .tipo-user {
                background: #e8eaf6;
                color: #283593;
            }
            .footer {
                margin-top: 30px;
                text-align: center;
                font-size: 12px;
                color: #666;
            }
            .filtro-info {
                text-align: left;
                margin: 10px 0;
                padding: 10px;
                background-color: #f8f9fa;
                border-radius: 5px;
                font-size: 14px;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <img src="data:image/png;base64,' . $logoData . '" class="logo">
            <h2>Relatório de Imóveis</h2>
            <p>Data de Geração: ' . date('d/m/Y H:i') . '</p>
        </div>';

    if (!empty($criterios)) {
        $html .= '
        <div class="filtros">
            <h3>Critérios do Filtro:</h3>
            <ul>
                <li>' . implode('</li><li>', $criterios) . '</li>
            </ul>
        </div>';
    }

    $html .= '
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipo</th>
                    <th>Tipologia</th>
                    <th>Localização</th>
                    <th>Preço</th>
                    <th>Status</th>
                    <th>Proprietário</th>
                    <th>Tipo Usuário</th>
                </tr>
            </thead>
            <tbody>';

    foreach ($residencias as $row) {
        $statusClass = $row['status'] == 'venda' ? 'status-venda' : 'status-arrendamento';
        $tipoClass = $row['tipo_usuario'] == 'Administrador' ? 'tipo-admin' : 'tipo-user';
        
        $html .= "
            <tr>
                <td>{$row['id']}</td>
                <td>{$row['typeResi']}</td>
                <td>{$row['typology']}</td>
                <td>{$row['location']}</td>
                <td>" . number_format($row['price'], 2, ',', '.') . " Kz</td>
                <td><span class='status-badge {$statusClass}'>{$row['status']}</span></td>
                <td>{$row['nome_usuario']}</td>
                <td><span class='tipo-badge {$tipoClass}'>{$row['tipo_usuario']}</span></td>
            </tr>";
    }

    $html .= '
            </tbody>
        </table>
        <div class="footer">
            <p>Total de Registros: ' . count($residencias) . '</p>
            <p>Sistema de Gestão Imobiliária - © ' . date('Y') . ' Resi. Todos os direitos reservados.</p>
        </div>
    </body>
    </html>';

    // Gerar PDF
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();

    // Gerar nome do arquivo com data e hora
    $filename = 'Relatorio_Imoveis_' . date('Y-m-d_H-i-s') . '.pdf';

    // Output do PDF
    $dompdf->stream($filename, array("Attachment" => false));

} catch (Exception $e) {
    echo "Erro ao gerar relatório: " . $e->getMessage();
    error_log("Erro no relatório: " . $e->getMessage());
}
?> 