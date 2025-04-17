<?php
    
    include_once "../Config/conection.php";

    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if(empty($dados['id'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Tente novamente ou tente mais tarde!</div>"];
    } elseif(empty($dados['typeResi'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário selecionar o Tipo de Imóvel!</div>"];
    } elseif(empty($dados['typology'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário selecionar a Tipologia do Imóvel!</div>"];
    } elseif(empty($dados['location'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário pôr a sua Localização!</div>"];
    } elseif(empty($dados['price'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário definir um Preço (Valor Avaliado)!</div>"];
    } elseif(empty($dados['status'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário definir o Estado da Imóvel!</div>"];
    } else {

            // Editar residênca
            $query_residencia = "UPDATE residencia SET typeResi = :typeResi, typology = :typology, location = :location, price = :price, status = :status WHERE id = :id";
            $edit_residencia = $conn->prepare($query_residencia);
            $edit_residencia->bindParam(':typeResi', $dados['typeResi']);
            $edit_residencia->bindParam(':typology', $dados['typology']);
            $edit_residencia->bindParam(':location', $dados['location']);
            $edit_residencia->bindParam(':price', $dados['price']);
            $edit_residencia->bindParam(':status', $dados['status']);
            $edit_residencia->bindParam(':id', $dados['id']);

            if ($edit_residencia->execute()) {
                $retornar = ['erro' => false, 'msg' => "<div class='alert alert-success' role='alert'>Residência editado com sucesso!</div>"];
            } else {
                $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Residência não editado com sucesso!</div>"];
            }
    }

    echo json_encode($retornar);
?>