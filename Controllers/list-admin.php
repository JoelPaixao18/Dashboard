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

    // Consulta principal para administradores apenas
    $query_admins = "SELECT id, nome, email, role, tel, BI FROM administrador";
    
    $params = [];
    
    if (!empty($searchTerm)) {
        $query_admins .= " WHERE (nome LIKE :search 
                            OR email LIKE :search 
                            OR tel LIKE :search
                            OR BI LIKE :search)";
        $params[':search'] = '%' . $searchTerm . '%';
    }
    
    $query_admins .= " ORDER BY id DESC LIMIT :inicio, :qnt_result_pg";
    
    $result_admins = $conn->prepare($query_admins);
    
    // Bind dos parâmetros
    foreach ($params as $key => &$val) {
        $result_admins->bindParam($key, $val);
    }
    
    $result_admins->bindValue(':inicio', $inicio, PDO::PARAM_INT);
    $result_admins->bindValue(':qnt_result_pg', $qnt_result_pg, PDO::PARAM_INT);
    
    if (!$result_admins->execute()) {
        throw new PDOException("Erro ao executar consulta de administradores");
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
                        font-weight: 600;
                        color: #495057;
                    }
                    .custom-table tbody tr {
                        background-color: #fff;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                        transition: all 0.3s ease;
                        border-radius: 8px;
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
                        min-width: 100px;
                        padding-left: 15px;
                        padding-right: 15px;
                    }
                    .badge-cell {
                        min-width: 100px;
                    }
                    .action-cell {
                        min-width: 160px;
                    }
                    .user-info {
                        line-height: 1.4;
                    }
                    .user-name {
                        font-weight: 500;
                        margin-bottom: 2px;
                    }
                    .user-role {
                        font-size: 0.85rem;
                        color: #6c757d;
                    }
                </style>
                <div class="table-responsive">
                    <table class="table custom-table">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="ps-4">ID</th>
                                <th scope="col">Nome</th>
                                <th scope="col">Email</th>
                                <th scope="col">Telefone</th>
                                <th scope="col">Nº de BI</th>
                                <th scope="col" class="badge-cell">Perfil</th>
                                <th scope="col" class="action-cell text-center pe-4">Ações</th>
                            </tr>
                        </thead>
                        <tbody>';

    while ($row_admin = $result_admins->fetch(PDO::FETCH_ASSOC)) {
        // Sanitizar todos os dados
        $id = htmlspecialchars($row_admin['id'], ENT_QUOTES, 'UTF-8');
        $nome = htmlspecialchars($row_admin['nome'], ENT_QUOTES, 'UTF-8');
        $email = htmlspecialchars($row_admin['email'], ENT_QUOTES, 'UTF-8');
        $tel = htmlspecialchars($row_admin['tel'], ENT_QUOTES, 'UTF-8');
        $BI = htmlspecialchars($row_admin['BI'], ENT_QUOTES, 'UTF-8');
        
        // Formatar role com badge (sempre será Admin)
        $roleBadge = '<span class="badge bg-danger px-3 py-1">Admin</span>';

        // Formatar telefone (se existir)
        $telFormatado = !empty($tel) ? $tel : '<span class="text-muted">Não informado</span>';

        // Formatar BI (se existir)
        $BIFormatado = !empty($BI) ? $BI : '<span class="text-muted">Não informado</span>';

        // Linha da tabela com espaçamento adequado
        $dados .= '<tr>
                    <td class="fw-bold ps-4">'.$id.'</td>
                    <td class="data-cell">
                        <div class="user-info">
                            <div class="user-name">'.$nome.'</div>
                            <div class="user-role">Administrador do sistema</div>
                        </div>
                    </td>
                    <td class="data-cell">'.$email.'</td>
                    <td class="data-cell">'.$telFormatado.'</td>
                    <td class="data-cell">'.$BIFormatado.'</td>
                    <td class="badge-cell">'.$roleBadge.'</td>
                    <td class="action-cell text-center pe-4">
                        <div class="btn-group btn-group-sm" role="group" aria-label="Ações">
                            <button id="'.$id.'" class="btn btn-outline-warning px-3" onclick="editAdminDados('.$id.')" title="Editar" data-bs-toggle="tooltip">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="'.$id.'" class="btn btn-outline-danger px-3" onclick="apagarAdminDados('.$id.')" title="Excluir" data-bs-toggle="tooltip">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>';
    }

    // Se não houver resultados
    if ($result_admins->rowCount() === 0) {
        $dados .= '<tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="empty-table-message">
                            <i class="fas fa-user-shield fa-3x mb-3 text-muted opacity-50"></i>
                            <h4 class="text-muted mb-2">Nenhum administrador encontrado</h4>
                            <p class="text-muted">Todos os administradores serão listados aqui</p>
                        </div>
                    </td>
                </tr>';
    }

    $dados .= '</tbody></table></div></div>';

    // Paginação - contar o total de registros
    $query_pg = "SELECT COUNT(id) AS num_result FROM administrador";
    if (!empty($searchTerm)) {
        $query_pg .= " WHERE (nome LIKE :search 
                       OR email LIKE :search 
                       OR tel LIKE :search
                       OR BI LIKE :search)";
    }
    
    $result_pg = $conn->prepare($query_pg);
    
    if (!empty($searchTerm)) {
        $result_pg->bindValue(':search', '%' . $searchTerm . '%');
    }
    
    if (!$result_pg->execute()) {
        throw new PDOException("Erro ao contar registros de administradores");
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
                        $row_pg['num_result'] . '</span> administradores
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item'.($pagina <= 1 ? ' disabled' : '').'">
                                <a class="page-link px-3 py-2" href="#" onclick="listarAdmin(1)" aria-label="Primeira">
                                    <span aria-hidden="true">&laquo;&laquo;</span>
                                </a>
                            </li>
                            <li class="page-item'.($pagina <= 1 ? ' disabled' : '').'">
                                <a class="page-link px-3 py-2" href="#" onclick="listarAdmin('.($pagina - 1).')" aria-label="Anterior">
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
                        <a class="page-link px-3 py-2" href="#" onclick="listarAdmin('.$i.')">'.$i.'</a>
                       </li>';
        }

        if ($end < $qnt_pg) {
            $dados .= '<li class="page-item disabled"><a class="page-link px-3 py-2">...</a></li>';
        }

        $dados .= '<li class="page-item'.($pagina >= $qnt_pg ? ' disabled' : '').'">
                    <a class="page-link px-3 py-2" href="#" onclick="listarAdmin('.($pagina + 1).')" aria-label="Próxima">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                   </li>
                   <li class="page-item'.($pagina >= $qnt_pg ? ' disabled' : '').'">
                    <a class="page-link px-3 py-2" href="#" onclick="listarAdmin('.$qnt_pg.')" aria-label="Última">
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