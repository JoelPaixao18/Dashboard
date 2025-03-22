<?php
    require_once '../Config/conection.php'; 

    $id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);

    if (!empty($id)) {

        $query_usuario = "DELETE FROM usuario WHERE id = :id";
        $result_usuario = $conn->prepare($query_usuario);
        $result_usuario->bindParam('id', $id);
        $result_usuario->execute();

        if ($result_usuario->execute()){
            $retornar = ['erro' => false, 'msg' => "<div class='alert alert-success' role='alert'>Usuário apagado com sucesso!</div>"];
        } else {
            $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Usuário não apagado com sucesso!</div>"];
        }

    } else {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Nenhum Usuário encontardo!</div>"];
    }

    echo json_encode($retornar);
?>