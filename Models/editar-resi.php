<?php
include_once "../Config/conection.php";

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

if (empty($dados['id'])) {
    $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Tente novamente ou tente mais tarde!</div>"];
} elseif (empty($dados['typeResi'])) {
    $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário selecionar o Tipo de Imóvel!</div>"];
} elseif (empty($dados['typology'])) {
    $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário selecionar a Tipologia do Imóvel!</div>"];
} elseif (empty($dados['location'])) {
    $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário pôr a sua Localização!</div>"];
} elseif (empty($dados['price'])) {
    $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário definir um Preço (Valor Avaliado)!</div>"];
} elseif (empty($dados['status'])) {
    $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário definir o Estado da Imóvel!</div>"];
} else {
    // Editar residência
    $query_residencia = "UPDATE residencia SET 
        typeResi = :typeResi, 
        typology = :typology, 
        location = :location, 
        price = :price, 
        status = :status, 
        houseSize = :houseSize, 
        livingRoomCount = :livingRoomCount, 
        bathroomCount = :bathroomCount, 
        kitchenCount = :kitchenCount, 
        andares = :andares, 
        quintal = :quintal, 
        garagem = :garagem, 
        hasWater = :hasWater, 
        hasElectricity = :hasElectricity 
        WHERE id = :id";

    $edit_residencia = $conn->prepare($query_residencia);
    $edit_residencia->bindParam(':typeResi', $dados['typeResi']);
    $edit_residencia->bindParam(':typology', $dados['typology']);
    $edit_residencia->bindParam(':location', $dados['location']);
    $edit_residencia->bindParam(':price', $dados['price']);
    $edit_residencia->bindParam(':status', $dados['status']);
    $edit_residencia->bindParam(':houseSize', $dados['houseSize']);
    $edit_residencia->bindParam(':livingRoomCount', $dados['livingRoomCount']);
    $edit_residencia->bindParam(':bathroomCount', $dados['bathroomCount']);
    $edit_residencia->bindParam(':kitchenCount', $dados['kitchenCount']);
    $edit_residencia->bindParam(':andares', $dados['andares']);
    $edit_residencia->bindParam(':quintal', $dados['quintal'], PDO::PARAM_INT);
    $edit_residencia->bindParam(':garagem', $dados['garagem'], PDO::PARAM_INT);
    $edit_residencia->bindParam(':hasWater', $dados['hasWater'], PDO::PARAM_INT);
    $edit_residencia->bindParam(':hasElectricity', $dados['hasElectricity'], PDO::PARAM_INT);
    $edit_residencia->bindParam(':id', $dados['id'], PDO::PARAM_INT);

    if ($edit_residencia->execute()) {
        $retornar = ['erro' => false, 'msg' => "<div class='alert alert-success' role='alert'>Residência editada com sucesso!</div>"];
    } else {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Residência não editada com sucesso!</div>"];
    }
}

echo json_encode($retornar);
?>