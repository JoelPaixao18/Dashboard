<?php
header('Content-Type: text/html; charset=utf-8');
require_once '../Config/conection.php';

try {
    $pagina = filter_input(INPUT_GET, "pagina", FILTER_VALIDATE_INT);
    $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

    if ($pagina === false || $pagina < 1) {
        $pagina = 1;
    }

    $qnt_result_pg = 5;
    $inicio = ($pagina - 1) * $qnt_result_pg;

    // Consulta principal com todos os campos
    $query_residencias = "SELECT 
                            id, typeResi, typology, location, price, status
                          FROM residencia";
    
    $params = [];
    
    if (!empty($searchTerm)) {
        $query_residencias .= " WHERE typeResi LIKE :search 
                               OR typology LIKE :search 
                               OR location LIKE :search 
                               OR status LIKE :search
                               OR houseSize LIKE :search
                               OR livingRoomCount LIKE :search
                               OR bathroomCount LIKE :search
                               OR kitchenCount LIKE :search
                               OR latitude LIKE :search
                               OR longitude LIKE :search";
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

    // Construir a tabela HTML com espaçamento adequado
    $dados = '<div class="table-container mt-4">
                <style>
                    .custom-table {
                        border-collapse: separate;
                        border-spacing: 0 12px;
                    }
                    .custom-table thead th {
                        background-color: #f8f9fa;
                        padding: 12px 15px;
                        border-bottom: 2px solid #dee2e6;
                    }
                    .custom-table tbody tr {
                        background-color: #fff;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                        transition: all 0.3s ease;
                    }
                    .custom-table tbody tr:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                    }
                    .custom-table td {
                        padding: 15px;
                        vertical-align: middle;
                        border-top: none;
                        border-bottom: 1px solid #f1f1f1;
                    }
                    .data-cell {
                        min-width: 80px;
                        padding-left: 15px;
                        padding-right: 15px;
                    }
                    .badge-cell {
                        min-width: 100px;
                    }
                    .action-cell {
                        min-width: 150px;
                    }
                </style>
                <div class="table-responsive">
                    <table class="table custom-table">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="ps-3">ID</th>
                                <th scope="col">Tipo</th>
                                <th scope="col">Tipologia</th>
                                <th scope="col">Localização</th>
                                <th scope="col">Valor</th>
                                <th scope="col" class="badge-cell">Status</th>
                                <th scope="col" class="action-cell text-center pe-3">Ações</th>
                            </tr>
                        </thead>
                        <tbody>';

    while ($row_residencia = $result_residencias->fetch(PDO::FETCH_ASSOC)) {
        // Sanitizar todos os dados
        $id = htmlspecialchars($row_residencia['id'], ENT_QUOTES, 'UTF-8');
        $typeResi = htmlspecialchars($row_residencia['typeResi'], ENT_QUOTES, 'UTF-8');
        $typology = htmlspecialchars($row_residencia['typology'], ENT_QUOTES, 'UTF-8');
        $location = htmlspecialchars($row_residencia['location'], ENT_QUOTES, 'UTF-8');
        /*$houseSize = htmlspecialchars($row_residencia['houseSize'], ENT_QUOTES, 'UTF-8');
        $livingRoomCount = htmlspecialchars($row_residencia['livingRoomCount'], ENT_QUOTES, 'UTF-8');
        $bathroomCount = htmlspecialchars($row_residencia['bathroomCount'], ENT_QUOTES, 'UTF-8');
        $kitchenCount = htmlspecialchars($row_residencia['kitchenCount'], ENT_QUOTES, 'UTF-8');
        $latitude = htmlspecialchars($row_residencia['latitude'], ENT_QUOTES, 'UTF-8');
        $longitude = htmlspecialchars($row_residencia['longitude'], ENT_QUOTES, 'UTF-8');*/
        
        // Formatar preço
        $price = 'N/A';
        if (is_numeric($row_residencia['price'])) {
            $price = 'Kz ' . number_format($row_residencia['price'], 2, ',', '.');
        }
        
        // Formatar status
        $status = htmlspecialchars($row_residencia['status'], ENT_QUOTES, 'UTF-8');
        $statusBadge = '';
        if ($status === 'Venda') {
            $statusBadge = '<span class="badge bg-warning text-dark px-3 py-1">' . $status . '</span>';
        } elseif ($status === 'Arrendamento') {
            $statusBadge = '<span class="badge bg-success px-3 py-1">' . $status . '</span>';
        } else {
            $statusBadge = '<span class="badge bg-secondary px-3 py-1">' . $status . '</span>';
        }
        
        // Formatar campos booleanos
        $formatBoolean = function($value) {
            return $value ? '<i class="fas fa-check-circle text-success fs-5"></i>' : '<i class="fas fa-times-circle text-danger fs-5"></i>';
        };
        
        /*$quintal = $formatBoolean($row_residencia['quintal']);
        $varanda = $formatBoolean($row_residencia['varanda']);
        $garagem = $formatBoolean($row_residencia['garagem']);
        $hasWater = $formatBoolean($row_residencia['hasWater']);
        $hasElectricity = $formatBoolean($row_residencia['hasElectricity']);
        
        // Formatar andares
        $andares = $row_residencia['andares'] > 0 ? $row_residencia['andares'] : '0';*/

        // Linha da tabela com espaçamento adequado
        $dados .= '<tr>
                    <td class="fw-bold ps-3">'.$id.'</td>
                    <td class="data-cell">'.$typeResi.'</td>
                    <td class="data-cell">'.$typology.'</td>
                    <td class="data-cell">'.$location.'</td>
                    <td class="data-cell fw-semibold">'.$price.'</td>
                    <td class="badge-cell">'.$statusBadge.'</td>
                    <td class="action-cell text-center pe-3">
                        <div class="btn-group btn-group-sm" role="group" aria-label="Ações">
                             <button id="'.$id.'" class="btn btn-outline-primary btn-sm mr-1" onclick="visResidencia('.$id.')" title="Visualizar" data-bs-toggle="tooltip">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button id="'.$id.'" class="btn btn-outline-warning px-3" onclick="editResidenciaDados('.$id.')" title="Editar" data-bs-toggle="tooltip">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="'.$id.'" class="btn btn-outline-danger px-3" onclick="apagarResidenciaDados('.$id.')" title="Excluir" data-bs-toggle="tooltip">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>';
    }

    // Se não houver resultados
    if ($result_residencias->rowCount() === 0) {
        $dados .= '<tr>
                    <td colspan="19" class="text-center py-5">
                        <div class="empty-table-message">
                            <i class="fas fa-home fa-3x mb-3 text-muted opacity-50"></i>
                            <h4 class="text-muted mb-2">Nenhum imóvel encontrado</h4>
                            <p class="text-muted">Cadastre um novo imóvel para começar</p>
                        </div>
                    </td>
                </tr>';
    }

    $dados .= '</tbody></table></div></div>';

    // Paginação - contar o total de registros
    $query_pg = "SELECT COUNT(id) AS num_result FROM residencia";
    if (!empty($searchTerm)) {
        $query_pg .= " WHERE typeResi LIKE :search 
                       OR typology LIKE :search 
                       OR location LIKE :search 
                       OR status LIKE :search
                       OR houseSize LIKE :search
                       OR livingRoomCount LIKE :search
                       OR bathroomCount LIKE :search
                       OR kitchenCount LIKE :search
                       OR latitude LIKE :search
                       OR longitude LIKE :search";
    }
    
    $result_pg = $conn->prepare($query_pg);
    
    if (!empty($searchTerm)) {
        $result_pg->bindValue(':search', '%' . $searchTerm . '%');
    }
    
    if (!$result_pg->execute()) {
        throw new PDOException("Erro ao contar registros");
    }

    $row_pg = $result_pg->fetch(PDO::FETCH_ASSOC);
    $qnt_pg = ceil($row_pg['num_result'] / $qnt_result_pg);
    $max_links = 2;

    // Construir a paginação
    if ($qnt_pg > 1) {
        $dados .= '<div class="d-flex justify-content-between align-items-center mt-4 mb-4">
                    <div class="text-muted small">
                        Mostrando <span class="fw-semibold">' . (($pagina - 1) * $qnt_result_pg + 1) . '</span> a <span class="fw-semibold">' . 
                        min($pagina * $qnt_result_pg, $row_pg['num_result']) . '</span> de <span class="fw-semibold">' . 
                        $row_pg['num_result'] . '</span> registros
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item'.($pagina <= 1 ? ' disabled' : '').'">
                                <a class="page-link px-3 py-2" href="#" onclick="listarResidencias(1)" aria-label="Primeira">
                                    <span aria-hidden="true">&laquo;&laquo;</span>
                                </a>
                            </li>
                            <li class="page-item'.($pagina <= 1 ? ' disabled' : '').'">
                                <a class="page-link px-3 py-2" href="#" onclick="listarResidencias('.($pagina - 1).')" aria-label="Anterior">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>';

        // Links numéricos
        $start = max(1, $pagina - $max_links);
        $end = min($qnt_pg, $pagina + $max_links);

        if ($start > 1) {
            $dados .= '<li class="page-item disabled"><a class="page-link px-3 py-2">...</a></li>';
        }

        for ($i = $start; $i <= $end; $i++) {
            $active = $i == $pagina ? ' active' : '';
            $dados .= '<li class="page-item'.$active.'">
                        <a class="page-link px-3 py-2" href="#" onclick="listarResidencias('.$i.')">'.$i.'</a>
                       </li>';
        }

        if ($end < $qnt_pg) {
            $dados .= '<li class="page-item disabled"><a class="page-link px-3 py-2">...</a></li>';
        }

        $dados .= '<li class="page-item'.($pagina >= $qnt_pg ? ' disabled' : '').'">
                    <a class="page-link px-3 py-2" href="#" onclick="listarResidencias('.($pagina + 1).')" aria-label="Próxima">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                   </li>
                   <li class="page-item'.($pagina >= $qnt_pg ? ' disabled' : '').'">
                    <a class="page-link px-3 py-2" href="#" onclick="listarResidencias('.$qnt_pg.')" aria-label="Última">
                        <span aria-hidden="true">&raquo;&raquo;</span>
                    </a>
                   </li>
                   </ul></nav></div>';
    }

    echo $dados;

} catch (PDOException $e) {
    echo '<div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-3 fs-4"></i>
                <div>
                    <h5 class="alert-heading mb-1">Erro no banco de dados</h5>
                    <p class="mb-0">' . htmlspecialchars($e->getMessage()) . '</p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
}
?>