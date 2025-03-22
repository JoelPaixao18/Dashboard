<?php

    //------------------- Listar Proprietários --------------------------------//
    require_once '../Config/conection.php';

    $pagina = filter_input(INPUT_GET, "pagina", FILTER_SANITIZE_NUMBER_INT);
    if (!empty($pagina)) {

    $qnt_result_pg = 20; // Quantidade de registros por página
    $inicio = ($pagina - 1) * $qnt_result_pg;

    // Consulta para listar proprietários com limite e offset
    $query_proprietarios = "SELECT id, nome, email, tel, BI, endereco FROM proprietario ORDER BY id DESC LIMIT :inicio, :qnt_result_pg";
    $result_proprietarios = $conn->prepare($query_proprietarios);
    $result_proprietarios->bindParam(':inicio', $inicio, PDO::PARAM_INT);
    $result_proprietarios->bindParam(':qnt_result_pg', $qnt_result_pg, PDO::PARAM_INT);
    $result_proprietarios->execute();

    $dados = "<div class='table-responsive'>
                <table class='table table-striped table-bordered'>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Contacto</th>
                            <th>Bilhete de Identidade</th>
                            <th>Endereço</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>";

    while ($row_proprietario = $result_proprietarios->fetch(PDO::FETCH_ASSOC)) {
        extract($row_proprietario);
        $dados .= "<tr>
                    <td>$id</td>
                    <td>$nome</td>
                    <td>$email</td>
                    <td>$tel</td>
                    <td>$BI</td>
                    <td>$endereco</td>
                    <td>
                        <button id='$id' class='btn btn-primary btn-sm' onclick='visProprietario($id)' >Vizualizar</button>
                        <button id='$id' class='btn btn-warning btn-sm' onclick='editProprietarioDados($id)' >Editar</button>
                        <button id='$id' class='btn btn-danger btn-sm' onclick='apagarProprietarioDados($id)' >Eliminar</button>
                    </td>
                </tr>";
    }

    $dados .= "</tbody></table></div>";

    // Paginação - contar o total de proprietários
    $query_pg = "SELECT COUNT(id) AS num_result FROM proprietario";
    $result_pg = $conn->prepare($query_pg);
    $result_pg->execute();
    $row_pg = $result_pg->fetch(PDO::FETCH_ASSOC);

    $qnt_pg = ceil($row_pg['num_result'] / $qnt_result_pg);
    $max_links = 2;

    $dados .= '<nav aria-label="Page navigation example"><ul class="pagination justify-content-end">';

    $dados .= "<li class='page-item'><a href='#' class='page-link' onclick='listarProprietarios(1)'>Primeira</a></li>";

    for ($pag_ant = $pagina - $max_links; $pag_ant <= $pagina - 1; $pag_ant++) {
        if ($pag_ant >= 1) {
            $dados .= "<li class='page-item'><a class='page-link' href='#' onclick='listarProprietarios($pag_ant)'>$pag_ant</a></li>";
        }
    }

    $dados .= "<li class='page-item active'><a class='page-link' href='#'>$pagina</a></li>";

    for ($pag_dep = $pagina + 1; $pag_dep <= $pagina + $max_links; $pag_dep++) {
        if ($pag_dep <= $qnt_pg) {
            $dados .= "<li class='page-item'><a class='page-link' href='#' onclick='listarProprietarios($pag_dep)'>$pag_dep</a></li>";
        }
    }

    $dados .= "<li class='page-item'><a class='page-link' href='#' onclick='listarProprietarios($qnt_pg)'>Última</a></li>";
    $dados .= '</ul></nav>';

    echo $dados;

} else {
    echo "<div class='alert alert-danger' role='alert'>Erro: Nenhum Proprietário encontardo!</div>";
}

?>