<?php
    require_once '../Config/conection.php'; 

    $id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);

    if (!empty($id)) {

        $query_proprietario = "SELECT id, nome, email, tel, BI, endereco FROM proprietario WHERE id = :id LIMIT 1";
        $result_proprietario = $conn->prepare($query_proprietario);
        $result_proprietario->bindParam(':id', $id);
        $result_proprietario->execute();

        $row_proprietario = $result_proprietario->fetch(PDO::FETCH_ASSOC);

        $retornar = ['erro' => false, 'dados' => $row_proprietario];
    
    } else {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Nenhum Proprietário encontardo!</div>"];
    }

    echo json_encode($retornar);
?>