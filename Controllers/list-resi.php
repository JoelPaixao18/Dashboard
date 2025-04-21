<?php
header('Content-Type: text/html; charset=utf-8');
require_once '../Config/conection.php';

try {
    $pagina = filter_input(INPUT_GET, "pagina", FILTER_VALIDATE_INT);
    $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

    if ($pagina === false || $pagina < 1) {
        $pagina = 1;
    }

    $qnt_result_pg = 20;
    $inicio = ($pagina - 1) * $qnt_result_pg;

    // Consulta principal
    $query_residencias = "SELECT 
                            id, typeResi, typology, location, price, status, 
                            houseSize, livingRoomCount, bathroomCount, kitchenCount, 
                            quintal, andares, garagem, hasWater, hasElectricity 
                          FROM residencia";
    
    $params = [];
    
    if (!empty($searchTerm)) {
        $query_residencias .= " WHERE typeResi LIKE :search 
                               OR typology LIKE :search 
                               OR location LIKE :search 
                               OR status LIKE :search";
        $params[':search'] = '%' . $searchTerm . '%';
    }
    
    $query_residencias .= " ORDER BY id DESC LIMIT :inicio, :qnt_result_pg";
    
    $result_residencias = $conn->prepare($query_residencias);
    
    // Bind dos parâmetros
    foreach ($params as $key => &$val) {
        $result_residencias->bindParam($key, $val);
    }
    
    $result_residencias->bindValue(':inicio', $inicio, PDO::PARAM_INT);
    $result_residencias->bindValue(':qnt_result_pg', $qnt_result_pg, PDO::PARAM_INT);
    
    if (!$result_residencias->execute()) {
        throw new PDOException("Erro ao executar consulta de residências");
    }

    // Construir a tabela HTML com classes adicionais para melhorar o layout
    $dados = '<div class="table-responsive" style="overflow-x: auto;">
    <table class="table table-striped table-bordered" style="width: 100%; min-width: 1200px;">
        <thead class="thead-dark">
            <tr>
                <th style="min-width: 50px;">ID</th>
                <th style="min-width: 100px;">Tipo</th>
                <th style="min-width: 90px;">Área (m²)</th>
                <th style="min-width: 200px;">Localização</th>
                <th style="min-width: 120px;">Valor (kz)</th>
                <th style="min-width: 100px;">Tipologia</th>
                <th style="min-width: 80px;">Salas</th>
                <th style="min-width: 100px;">Cozinhas</th>
                <th style="min-width: 100px;">Banheiros</th>
                <th style="min-width: 100px;">Quintal</th>
                <th style="min-width: 100px;">Andares</th>
                <th style="min-width: 100px;">Garagem</th>
                <th style="min-width: 80px;">Água</th>
                <th style="min-width: 100px;">Energia</th>
                <th style="min-width: 100px;">Status</th>
                <th style="min-width: 150px;">Ações</th>
            </tr>
        </thead>
        <tbody>';

    while ($row_residencia = $result_residencias->fetch(PDO::FETCH_ASSOC)) {
        // Sanitizar todos os dados antes de exibir
        $id = htmlspecialchars($row_residencia['id'], ENT_QUOTES, 'UTF-8');
        $typeResi = htmlspecialchars($row_residencia['typeResi'], ENT_QUOTES, 'UTF-8');
        $typology = htmlspecialchars($row_residencia['typology'], ENT_QUOTES, 'UTF-8');
        $location = htmlspecialchars($row_residencia['location'], ENT_QUOTES, 'UTF-8');
        
        // Formatar preço
        $price = 'N/A';
        if (is_numeric($row_residencia['price'])) {
            $price = 'kz ' . number_format($row_residencia['price'], 2, ',', '.');
        }
        
        $status = htmlspecialchars($row_residencia['status'], ENT_QUOTES, 'UTF-8');
        
        // Formatar área
        $houseSize = 'N/A';
        if (is_numeric($row_residencia['houseSize'])) {
            $houseSize = number_format($row_residencia['houseSize'], 1, ',', '.') . ' m²';
        }
        
        // Sanitizar os demais campos
        $livingRoomCount = htmlspecialchars($row_residencia['livingRoomCount'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
        $bathroomCount = htmlspecialchars($row_residencia['bathroomCount'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
        $kitchenCount = htmlspecialchars($row_residencia['kitchenCount'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
        $quintal = htmlspecialchars($row_residencia['quintal'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
        $andares = htmlspecialchars($row_residencia['andares'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
        $garagem = htmlspecialchars($row_residencia['garagem'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
        $hasWater = htmlspecialchars($row_residencia['hasWater'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
        $hasElectricity = htmlspecialchars($row_residencia['hasElectricity'] ?? 'N/A', ENT_QUOTES, 'UTF-8');

        // Linha da tabela com padding aumentado
        $dados .= "<tr>
                    <td style='padding: 12px 8px;'>{$id}</td>
                    <td style='padding: 12px 8px;'>{$typeResi}</td>
                    <td style='padding: 12px 8px;'>{$houseSize}</td>
                    <td style='padding: 12px 8px;'>{$location}</td>
                    <td style='padding: 12px 8px;'>{$price}</td>
                    <td style='padding: 12px 8px;'>{$typology}</td>
                    <td style='padding: 12px 8px;'>{$livingRoomCount}</td>
                    <td style='padding: 12px 8px;'>{$kitchenCount}</td>
                    <td style='padding: 12px 8px;'>{$bathroomCount}</td>
                    <td style='padding: 12px 8px;'>{$quintal}</td>
                    <td style='padding: 12px 8px;'>{$andares}</td>
                    <td style='padding: 12px 8px;'>{$garagem}</td>
                    <td style='padding: 12px 8px;'>{$hasWater}</td>
                    <td style='padding: 12px 8px;'>{$hasElectricity}</td>
                    <td style='padding: 12px 8px;'>{$status}</td>
                    <td style='padding: 12px 8px;'>
                        <div class='btn-group' role='group'>
                            <button id='{$id}' class='btn btn-primary btn-sm mr-1' onclick='visResidencia({$id})' title='Visualizar'>
                                <i class='fas fa-eye'></i>
                            </button>
                            <button id='{$id}' class='btn btn-warning btn-sm mr-1' onclick='editResidenciaDados({$id})' title='Editar'>
                                <i class='fas fa-edit'></i>
                            </button>
                            <button id='{$id}' class='btn btn-danger btn-sm' onclick='apagarResidenciaDados({$id})' title='Excluir'>
                                <i class='fas fa-trash'></i>
                            </button>
                        </div>
                    </td>
                </tr>";
    }

    $dados .= "</tbody></table></div>";

    // Paginação - contar o total de registros
    $query_pg = "SELECT COUNT(id) AS num_result FROM residencia";
    $result_pg = $conn->prepare($query_pg);
    
    if (!$result_pg->execute()) {
        throw new PDOException("Erro ao contar registros");
    }

    $row_pg = $result_pg->fetch(PDO::FETCH_ASSOC);
    $qnt_pg = ceil($row_pg['num_result'] / $qnt_result_pg);
    $max_links = 2;

    // Construir a paginação
    $dados .= '<nav aria-label="Page navigation example"><ul class="pagination justify-content-end">';

    // Link para primeira página
    $dados .= "<li class='page-item" . ($pagina <= 1 ? ' disabled' : '') . "'>
                <a href='#' class='page-link' onclick='listarResidencias(1)'>Primeira</a>
               </li>";

    // Links anteriores
    for ($pag_ant = $pagina - $max_links; $pag_ant <= $pagina - 1; $pag_ant++) {
        if ($pag_ant >= 1) {
            $dados .= "<li class='page-item'>
                        <a class='page-link' href='#' onclick='listarResidencias($pag_ant)'>{$pag_ant}</a>
                       </li>";
        }
    }

    // Página atual
    $dados .= "<li class='page-item active'>
                <a class='page-link' href='#'>{$pagina}</a>
               </li>";

    // Links posteriores
    for ($pag_dep = $pagina + 1; $pag_dep <= $pagina + $max_links; $pag_dep++) {
        if ($pag_dep <= $qnt_pg) {
            $dados .= "<li class='page-item'>
                        <a class='page-link' href='#' onclick='listarResidencias($pag_dep)'>{$pag_dep}</a>
                       </li>";
        }
    }

    // Link para última página
    $dados .= "<li class='page-item" . ($pagina >= $qnt_pg ? ' disabled' : '') . "'>
                <a class='page-link' href='#' onclick='listarResidencias($qnt_pg)'>Última</a>
               </li>";
    $dados .= '</ul></nav>';

    echo $dados;

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Erro no banco de dados: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>