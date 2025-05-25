<?php
    require_once '../Config/conection.php'; 

    $id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);

    if (!empty($id)) {

        $query_admin = "DELETE FROM administrador WHERE id = :id";
        $result_admin = $conn->prepare($query_admin);
        $result_admin->bindParam('id', $id);
        $result_admin->execute();

        if ($result_admin->execute()){
            $retornar = ['erro' => false, 'msg' => "<div class='alert alert-success' role='alert'>Administrador apagado com sucesso!</div>"];
        } else {
            $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Administrador não apagado com sucesso!</div>"];
        }

    } else {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Nenhum Administrador encontardo!</div>"];
    }

    echo json_encode($retornar);
?>