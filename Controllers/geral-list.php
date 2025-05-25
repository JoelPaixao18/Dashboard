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

    // Consulta principal com JOIN entre usuário e residencia
    $query_geral = "SELECT 
                    u.id AS usuario_id, u.nome, u.email, u.tel, u.BI,
                    r.id AS residencia_id, r.typeResi, r.typology, r.location, 
                    r.price, r.status, r.houseSize, r.livingRoomCount,
                    r.bathroomCount, r.kitchenCount, r.quintal, r.andares,
                    r.garagem, r.varanda, r.hasWater, r.hasElectricity,
                    r.latitude, r.longitude
                  FROM usuario u
                  LEFT JOIN residencia r ON u.id = r.user_id";
    
    $params = [];
    
    if (!empty($searchTerm)) {
        $query_geral .= " WHERE u.nome LIKE :search 
                        OR u.email LIKE :search 
                        OR u.tel LIKE :search
                        OR u.BI LIKE :search
                        OR r.typeResi LIKE :search
                        OR r.typology LIKE :search
                        OR r.location LIKE :search
                        OR r.status LIKE :search";
        $params[':search'] = '%' . $searchTerm . '%';
    }
    
    $query_geral .= " ORDER BY u.nome ASC, r.id DESC LIMIT :inicio, :qnt_result_pg";
    
    $result_geral = $conn->prepare($query_geral);
    
    // Bind dos parâmetros
    foreach ($params as $key => &$val) {
        $result_geral->bindParam($key, $val);
    }
    
    $result_geral->bindValue(':inicio', $inicio, PDO::PARAM_INT);
    $result_geral->bindValue(':qnt_result_pg', $qnt_result_pg, PDO::PARAM_INT);
    
    if (!$result_geral->execute()) {
        throw new PDOException("Erro ao executar consulta geral");
    }

    // Construir a tabela HTML com estilo moderno e responsivo
    $dados = '<div class="table-container">
                <style>
                    .table-responsive {
                        overflow-x: auto;
                        -webkit-overflow-scrolling: touch;
                    }
                    .user-row {
                        background-color: #f8faff !important;
                        border-left: 4px solid #3a7bd5;
                    }
                    .property-row {
                        background-color: #f9f9f9;
                    }
                    .property-row:hover {
                        background-color: #f1f1f1;
                    }
                    .property-details {
                        max-width: 200px;
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                    }
                    .badge-cell {
                        min-width: 100px;
                    }
                    .bool-cell {
                        text-align: center;
                    }
                    .bool-true {
                        color: #28a745;
                    }
                    .bool-false {
                        color: #dc3545;
                    }
                    .empty-table-message {
                        padding: 2rem;
                        text-align: center;
                        color: #6c757d;
                    }
                    @media (max-width: 768px) {
                        .table-responsive {
                            border: 0;
                        }
                        .table-responsive table {
                            width: 100%;
                            margin-bottom: 1rem;
                            display: block;
                        }
                        .table-responsive thead {
                            display: none;
                        }
                        .table-responsive tbody {
                            display: block;
                        }
                        .table-responsive tr {
                            display: block;
                            margin-bottom: 1rem;
                            border: 1px solid #dee2e6;
                            border-radius: 0.25rem;
                        }
                        .table-responsive td {
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            padding: 0.75rem;
                            border-bottom: 1px solid #dee2e6;
                        }
                        .table-responsive td:before {
                            content: attr(data-label);
                            font-weight: bold;
                            margin-right: 1rem;
                            flex: 1;
                        }
                        .table-responsive td > div {
                            flex: 2;
                            text-align: right;
                        }
                        .user-row td:first-child {
                            background-color: #f8faff;
                            font-weight: bold;
                        }
                    }
                </style>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Usuário</th>
                                <th>Contato</th>
                                <th>Tipo</th>
                                <th>Tipologia</th>
                                <th>Localização</th>
                                <th>Valor</th>
                                <th class="badge-cell">Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>';

    $current_user = null;
    $has_data = false;

    while ($row = $result_geral->fetch(PDO::FETCH_ASSOC)) {
        $has_data = true;
        
        // Se é um novo usuário, mostra linha do usuário
        if ($current_user !== $row['usuario_id']) {
            $current_user = $row['usuario_id'];
            
            // Sanitizar dados do usuário
            $nome = htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8');
            $email = htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8');
            $tel = htmlspecialchars($row['tel'], ENT_QUOTES, 'UTF-8');
            $BI = htmlspecialchars($row['BI'], ENT_QUOTES, 'UTF-8');
            
            $dados .= '<tr class="user-row">
                        <td data-label="Usuário">
                            <div class="d-flex align-items-center">
                                <div class="ms-3">
                                    <div class="fw-semibold">'.$nome.'</div>
                                    <div class="small text-muted">'.$email.'</div>
                                </div>
                            </div>
                        </td>
                        <td data-label="Contato">
                            <div>'.$tel.'</div>
                            <div class="small text-muted">BI: '.$BI.'</div>
                        </td>';
            
            // Se não tem imóvel, completa a linha
            if (empty($row['residencia_id'])) {
                $dados .= '<td colspan="6" class="text-muted" data-label="Imóveis">Nenhum imóvel cadastrado</td></tr>';
                continue;
            }
        }
        
        // Se tem imóvel, mostra dados do imóvel
        if (!empty($row['residencia_id'])) {
            // Sanitizar dados do imóvel
            $typeResi = htmlspecialchars($row['typeResi'], ENT_QUOTES, 'UTF-8');
            $typology = htmlspecialchars($row['typology'], ENT_QUOTES, 'UTF-8');
            $location = htmlspecialchars($row['location'], ENT_QUOTES, 'UTF-8');
            
            // Formatar preço
            $price = 'Kz ' . number_format($row['price'], 2, ',', '.');
            
            // Formatar status
            $status = htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8');
            $statusBadge = '';
            if ($status === 'Venda') {
                $statusBadge = '<span class="badge bg-warning text-dark px-3 py-1">' . $status . '</span>';
            } elseif ($status === 'Arrendamento') {
                $statusBadge = '<span class="badge bg-success px-3 py-1">' . $status . '</span>';
            } else {
                $statusBadge = '<span class="badge bg-secondary px-3 py-1">' . $status . '</span>';
            }
            
            // Função para formatar valores booleanos
            $formatBool = function($value) {
                return $value === 'Sim' ? 
                    '<i class="fas fa-check bool-true"></i>' : 
                    '<i class="fas fa-times bool-false"></i>';
            };
            
            // Detalhes do imóvel (tooltip)
            $detalhes = '<button type="button" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="tooltip" 
                        data-bs-html="true" 
                        title="<b>Detalhes:</b><br>
                        Salas: '.$row['livingRoomCount'].'<br>
                        Banheiros: '.$row['bathroomCount'].'<br>
                        Cozinhas: '.$row['kitchenCount'].'<br>
                        Tamanho: '.$row['houseSize'].' m²<br>
                        Andares: '.$row['andares'].'<br>
                        Quintal: '.$formatBool($row['quintal']).'<br>
                        Garagem: '.$formatBool($row['garagem']).'<br>
                        Varanda: '.$formatBool($row['varanda']).'<br>
                        Água: '.$formatBool($row['hasWater']).'<br>
                        Energia: '.$formatBool($row['hasElectricity']).'">
                        <i class="fas fa-info-circle"></i>
                        </button>';
            
            // Botão para ver no mapa (se tiver coordenadas)
            $mapButton = '';
            if (!empty($row['latitude']) && !empty($row['longitude'])) {
                $mapButton = '<button class="btn btn-sm btn-outline-secondary" 
                              onclick="viewOnMap('.$row['latitude'].','.$row['longitude'].')">
                              <i class="bx bx-map"></i>
                              </button>';
            }
            
            $dados .= '<tr class="property-row">
                        <td data-label="Tipo">'.$typeResi.'</td>
                        <td data-label="Tipologia">'.$typology.'</td>
                        <td data-label="Localização" class="property-details">'.$location.'</td>
                        <td data-label="Valor">'.$price.'</td>
                        <td data-label="Status">'.$statusBadge.'</td>
                        <td data-label="Ações">
                            <div class="d-flex gap-2">
                                '.$detalhes.'
                                '.$mapButton.'
                            </div>
                        </td>
                      </tr>';
        }
    }

    // Se não houver resultados
    if (!$has_data) {
        $dados .= '<tr>
                    <td colspan="8">
                        <div class="empty-table-message">
                            <i class="fas fa-home fa-3x mb-3 text-muted opacity-50"></i>
                            <h4 class="text-muted mb-2">Nenhum dado encontrado</h4>
                            <p class="text-muted">Nenhum usuário com imóveis cadastrados</p>
                        </div>
                    </td>
                </tr>';
    }

    $dados .= '</tbody></table></div></div>';

    // Paginação - contar o total de registros
    $query_pg = "SELECT COUNT(DISTINCT u.id) AS num_result 
                FROM usuario u
                LEFT JOIN residencia r ON u.id = r.user_id";
    if (!empty($searchTerm)) {
        $query_pg .= " WHERE u.nome LIKE :search 
                      OR u.email LIKE :search 
                      OR u.tel LIKE :search
                      OR u.BI LIKE :search
                      OR r.typeResi LIKE :search
                      OR r.typology LIKE :search
                      OR r.location LIKE :search
                      OR r.status LIKE :search";
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
                        $row_pg['num_result'] . '</span> usuários
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item'.($pagina <= 1 ? ' disabled' : '').'">
                                <a class="page-link px-3 py-2" href="#" onclick="listarGeral(1)" aria-label="Primeira">
                                    <span aria-hidden="true">&laquo;&laquo;</span>
                                </a>
                            </li>
                            <li class="page-item'.($pagina <= 1 ? ' disabled' : '').'">
                                <a class="page-link px-3 py-2" href="#" onclick="listarGeral('.($pagina - 1).')" aria-label="Anterior">
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
                        <a class="page-link px-3 py-2" href="#" onclick="listarGeral('.$i.')">'.$i.'</a>
                       </li>';
        }

        if ($end < $qnt_pg) {
            $dados .= '<li class="page-item disabled"><a class="page-link px-3 py-2">...</a></li>';
        }

        $dados .= '<li class="page-item'.($pagina >= $qnt_pg ? ' disabled' : '').'">
                    <a class="page-link px-3 py-2" href="#" onclick="listarGeral('.($pagina + 1).')" aria-label="Próxima">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                   </li>
                   <li class="page-item'.($pagina >= $qnt_pg ? ' disabled' : '').'">
                    <a class="page-link px-3 py-2" href="#" onclick="listarGeral('.$qnt_pg.')" aria-label="Última">
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