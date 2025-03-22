<?php
    
    include_once "../Config/conection.php";

    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if(empty($dados['zonamento'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário selecionar o Tipo de Residência!</div>"];
    } elseif(empty($dados['localizacao'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário pôr a sua Localização!</div>"];
    } elseif(empty($dados['preco'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário definir um Preço (Valor Avaliado)!</div>"];
    } elseif(empty($dados['status'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário definir o Estado da Residênncia!</div>"];
    } elseif(empty($dados['descricao'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário descrever a Residência!</div>"];
    } else {
            // Inserir novo usuário
            $query_residencia = "INSERT INTO residencia (zonamento, localizacao, preco, status, descricao) VALUES (:zonamento, :localizacao, :preco, :status, :descricao)";
            $cad_residencia = $conn->prepare($query_residencia);
            $cad_residencia->bindParam(':zonamento', $dados['zonamento']);
            $cad_residencia->bindParam(':localizacao', $dados['localizacao']);
            $cad_residencia->bindParam(':preco', $dados['preco']);
            $cad_residencia->bindParam(':status', $dados['status']);
            $cad_residencia->bindParam(':descricao', $dados['descricao']);

            if ($cad_residencia->execute()) {
                $retornar = ['erro' => false, 'msg' => "<div class='alert alert-success' role='alert'>Residência cadastrada com sucesso!</div>"];
            } else {
                $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Residência não cadastrado com sucesso!</div>"];
            }
    }

    echo json_encode($retornar);
?>