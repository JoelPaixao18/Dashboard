<?php

    require_once '../Config/conection.php';

    $pagina = filter_input(INPUT_GET, "pagina", FILTER_SANITIZE_NUMBER_INT);

if (!empty($pagina)) {

    $qnt_result_pg = 20; // Quantidade de registros por página
    $inicio = ($pagina - 1) * $qnt_result_pg;

    // Consulta para listar usuários com limite e offset
    $query_usuarios = "SELECT id, nome, email, role FROM usuario ORDER BY id DESC LIMIT :inicio, :qnt_result_pg";
    $result_usuarios = $conn->prepare($query_usuarios);
    $result_usuarios->bindParam(':inicio', $inicio, PDO::PARAM_INT);
    $result_usuarios->bindParam(':qnt_result_pg', $qnt_result_pg, PDO::PARAM_INT);
    $result_usuarios->execute();

    $dados = "<div class='table-responsive'>
                <table class='table table-striped table-bordered' id='userTable'>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id='pesquisar-usuarios'>";

    while ($row_usuario = $result_usuarios->fetch(PDO::FETCH_ASSOC)) {
        extract($row_usuario);
        $dados .= "<tr>
                    <td>$id</td>
                    <td>$nome</td>
                    <td>$email</td>
                    <td>$role</td>
                    <td>
                        <button id='$id' class='btn btn-primary btn-sm' onclick='visUsuario($id)' >Vizualizar</button>
                        <button id='$id' class='btn btn-warning btn-sm' onclick='editUsuarioDados($id)' >Editar</button>
                        <button id='$id' class='btn btn-danger btn-sm' onclick='apagarUsuarioDados($id)' >Eliminar</button>
                    </td>
                </tr>";
    }

    $dados .= "</tbody></table></div>";

    // Paginação - contar o total de usuários
    $query_pg = "SELECT COUNT(id) AS num_result FROM usuario";
    $result_pg = $conn->prepare($query_pg);
    $result_pg->execute();
    $row_pg = $result_pg->fetch(PDO::FETCH_ASSOC);

    $qnt_pg = ceil($row_pg['num_result'] / $qnt_result_pg);
    $max_links = 2;

    $dados .= '<nav aria-label="Page navigation "><ul class="pagination justify-content-end">';

    $dados .= "<li class='page-item'><a href='#' class='page-link' onclick='listarUsuarios(1)'>Primeira</a></li>";

    for ($pag_ant = $pagina - $max_links; $pag_ant <= $pagina - 1; $pag_ant++) {
        if ($pag_ant >= 1) {
            $dados .= "<li class='page-item'><a class='page-link' href='#' onclick='listarUsuarios($pag_ant)'>$pag_ant</a></li>";
        }
    }

    $dados .= "<li class='page-item active'><a class='page-link' href='#'>$pagina</a></li>";

    for ($pag_dep = $pagina + 1; $pag_dep <= $pagina + $max_links; $pag_dep++) {
        if ($pag_dep <= $qnt_pg) {
            $dados .= "<li class='page-item'><a class='page-link' href='#' onclick='listarUsuarios($pag_dep)'>$pag_dep</a></li>";
        }
    }

    $dados .= "<li class='page-item'><a class='page-link' href='#' onclick='listarUsuarios($qnt_pg)'>Última</a></li>";
    $dados .= '</ul></nav>';

   echo $dados;
   
} else {
    echo "<div class='alert alert-danger' role='alert'>Erro: Nenhum Usuário encontardo!</div>";
}
?>