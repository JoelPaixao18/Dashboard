<?php
    require_once '../Config/conection.php'; 

    $id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);

    if (!empty($id)) {

        $query_residencia = "DELETE FROM residencia WHERE id = :id";
        $result_residencia = $conn->prepare($query_residencia);
        $result_residencia->bindParam('id', $id);
        $result_residencia->execute();

        if ($result_residencia->execute()){
            $retornar = ['erro' => false, 'msg' => "<div class='alert alert-success' role='alert'>Imóvel apagado com sucesso!</div>"];
        } else {
            $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro ao apagar Imóvel!</div>"];
        }

    } else {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Nenhum Imóvel encontardo!</div>"];
    }

    echo json_encode($retornar);
?>