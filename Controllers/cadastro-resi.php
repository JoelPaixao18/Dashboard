<?php
    
    include_once "../Config/conection.php";

    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if(empty($dados['typeResi'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário selecionar o Tipo de Imóvel!</div>"];
    } if(empty($dados['typology'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário selecionar a Tipologia do Imóvel!</div>"];
    } elseif(empty($dados['location'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário pôr a sua Localização!</div>"];
    } elseif(empty($dados['price'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário definir um Preço (Valor Avaliado)!</div>"];
    } elseif(empty($dados['status'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário definir o Estado da Residênncia!</div>"];
    } else {
            // Inserir novo usuário
            $query_residencia = "INSERT INTO residencia (typeResi, typology, location, price, status) VALUES (:typeResi, :typology, :location, :price, :status)";
            $cad_residencia = $conn->prepare($query_residencia);
            $cad_residencia->bindParam(':typeResi', $dados['typeResi']);
            $cad_residencia->bindParam(':typology', $dados['typology']);
            $cad_residencia->bindParam(':location', $dados['location']);
            $cad_residencia->bindParam(':price', $dados['price']);
            $cad_residencia->bindParam(':status', $dados['status']);

            if ($cad_residencia->execute()) {
                $retornar = ['erro' => false, 'msg' => "<div class='alert alert-success' role='alert'>Residência cadastrada com sucesso!</div>"];
            } else {
                $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Residência não cadastrado com sucesso!</div>"];
            }
    }

    echo json_encode($retornar);
?>