<?php

    require_once '../Config/conection.php';

    $pagina = filter_input(INPUT_GET, "pagina", FILTER_SANITIZE_NUMBER_INT);

if (!empty($pagina)) {

    $qnt_result_pg = 20; // Quantidade de registros por página
    $inicio = ($pagina - 1) * $qnt_result_pg;

    // Consulta para listar proprietários com limite e offset
    $query_residencias = "SELECT id, zonamento, localizacao, preco, status, descricao FROM residencia ORDER BY id DESC LIMIT :inicio, :qnt_result_pg";
    $result_residencias = $conn->prepare($query_residencias);
    $result_residencias->bindParam(':inicio', $inicio, PDO::PARAM_INT);
    $result_residencias->bindParam(':qnt_result_pg', $qnt_result_pg, PDO::PARAM_INT);
    $result_residencias->execute();

    $dados = "<div class='table-responsive'>
                <table class='table table-striped table-bordered'>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tipo de Residêcia</th>
                            <th>Localização</th>
                            <th>Valor Avaliado</th>
                            <th>Status</th>
                            <th>Descrição</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>";

    while ($row_residencia = $result_residencias->fetch(PDO::FETCH_ASSOC)) {
        extract($row_residencia);
        $dados .= "<tr>
                    <td>$id</td>
                    <td>$zonamento</td>
                    <td>$localizacao</td>
                    <td>$preco</td>
                    <td>$status</td>
                    <td>$descricao</td>
                    <td>
                        <button id='$id' class='btn btn-primary btn-sm' onclick='visResidencia($id)' >
                            <i class='fas fa-eye'></i>
                        </button>
                        <button id='$id' class='btn btn-warning btn-sm' onclick='editResidenciaDados($id)' >
                            <i class='fas fa-edit'></i>
                        </button>
                        <button id='$id' class='btn btn-danger btn-sm' onclick='apagarResidenciaDados($id)' >
                            <i class='fas fa-trash'></i>
                        </button>
                    </td>
                </tr>";
    }

    $dados .= "</tbody></table></div>";

    // Paginação - contar o total de Residências
    $query_pg = "SELECT COUNT(id) AS num_result FROM residencia";
    $result_pg = $conn->prepare($query_pg);
    $result_pg->execute();
    $row_pg = $result_pg->fetch(PDO::FETCH_ASSOC);

    $qnt_pg = ceil($row_pg['num_result'] / $qnt_result_pg);
    $max_links = 2;

    $dados .= '<nav aria-label="Page navigation example"><ul class="pagination justify-content-end">';

    $dados .= "<li class='page-item'><a href='#' class='page-link' onclick='listarResidencias(1)'>Primeira</a></li>";

    for ($pag_ant = $pagina - $max_links; $pag_ant <= $pagina - 1; $pag_ant++) {
        if ($pag_ant >= 1) {
            $dados .= "<li class='page-item'><a class='page-link' href='#' onclick='listarResidencias($pag_ant)'>$pag_ant</a></li>";
        }
    }

    $dados .= "<li class='page-item active'><a class='page-link' href='#'>$pagina</a></li>";

    for ($pag_dep = $pagina + 1; $pag_dep <= $pagina + $max_links; $pag_dep++) {
        if ($pag_dep <= $qnt_pg) {
            $dados .= "<li class='page-item'><a class='page-link' href='#' onclick='listarResidencias($pag_dep)'>$pag_dep</a></li>";
        }
    }

    $dados .= "<li class='page-item'><a class='page-link' href='#' onclick='listarResidencias($qnt_pg)'>Última</a></li>";
    $dados .= '</ul></nav>';

    echo $dados;

} else {
    echo "<div class='alert alert-danger' role='alert'>Erro: Nenhum Usuário encontardo!</div>";
}

?>