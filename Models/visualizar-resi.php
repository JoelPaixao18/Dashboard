<?php
    require_once '../Config/conection.php'; 

    $id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);

    if (!empty($id)) {

        $query_residencia = "SELECT id, typeResi, typology, location, price, status FROM residencia WHERE id = :id LIMIT 1";
        $result_residencia = $conn->prepare($query_residencia);
        $result_residencia->bindParam('id', $id);
        $result_residencia->execute();

        $row_residencia = $result_residencia->fetch(PDO::FETCH_ASSOC);


        $retornar = ['erro' => false, 'dados' => $row_residencia];
    
    } else {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Nenhum Imóvel encontardo!</div>"];
    }

    echo json_encode($retornar);
?>