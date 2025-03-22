<?php
    require_once '../Config/conection.php'; 

    $id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);

    if (!empty($id)) {

        $query_proprietario = "DELETE FROM proprietario WHERE id = :id";
        $result_proprietario = $conn->prepare($query_proprietario);
        $result_proprietario->bindParam('id', $id);
        $result_proprietario->execute();

        if ($result_proprietario->execute()){
            $retornar = ['erro' => false, 'msg' => "<div class='alert alert-success' role='alert'>Proprietário apagado com sucesso!</div>"];
        } else {
            $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Proprietário não apagado com sucesso!</div>"];
        }

    } else {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Nenhum Proprietário encontardo!</div>"];
    }

    echo json_encode($retornar);
?>