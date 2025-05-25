<?php
require_once '../Config/conection.php'; 

$id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);

if (!empty($id)) {
    $query_admin = "SELECT id, nome, email, role, tel, BI FROM administrador WHERE id = :id LIMIT 1";
    $result_admin = $conn->prepare($query_admin);
    $result_admin->bindParam('id', $id);
    $result_admin->execute();

    $row_admin = $result_admin->fetch(PDO::FETCH_ASSOC);

    $retornar = ['erro' => false, 'dados' => $row_admin];
} else {
    $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Nenhum Administrador encontrado!</div>"];
}

echo json_encode($retornar);
?>