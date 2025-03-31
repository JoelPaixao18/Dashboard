<?php
    
    include_once "../Config/conection.php";

    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if(empty($dados['id'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Tente novamente ou tente mais tarde!</div>"];
    } elseif(empty($dados['nome'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário preencher o campo Nome!</div>"];
    } elseif(!preg_match('/^[\p{L}\s]+$/u', $dados['nome'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Nome inválido! Informe apenas letras e espaços.</div>"];
    } elseif(empty($dados['email'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário preencher o campo E-mail!</div>"];
    } elseif (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: E-mail inválido!</div>"];
    }  elseif(empty($dados['tel'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário preencher o campo de Contato!</div>"];
    } elseif(!preg_match('/^[0-9]{9,15}$/', $dados['tel'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Contato inválido! Informe apenas números com 9 a 15 dígitos.</div>"];
    }  elseif(empty($dados['BI'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário preencher o campo do B.I!</div>"];
    } elseif(!preg_match('/^[0-9]{9}[A-Z]{2}[0-9]{3}$/', $dados['BI'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Bilhete de Identidade inválido!</div>"];
    } elseif(empty($dados['endereco'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário preencher o campo com seu Endereço!</div>"];
    } else {
        // Verificar se o e-mail já está cadastrado
        $query_verifica_email = "SELECT id FROM proprietario WHERE email = :email LIMIT 1";
        $verifica_email = $conn->prepare($query_verifica_email);
        $verifica_email->bindParam(':email', $dados['email']);
        $verifica_email->execute();

        if ($verifica_email->rowCount() > 0) {
            $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Este e-mail já está cadastrado!</div>"];
        } else {

            // Editar usuário
            $query_proprietario = "UPDATE proprietario SET nome = :nome, email = :email, tel = :tel, BI = :BI, endereco = :endereco WHERE id = :id";
            $edit_proprietario = $conn->prepare($query_proprietario);
            $edit_proprietario->bindParam(':nome', $dados['nome']);
            $edit_proprietario->bindParam(':email', $dados['email']);
            $edit_proprietario->bindParam(':tel', $dados['tel']);
            $edit_proprietario->bindParam(':BI', $dados['BI']);
            $edit_proprietario->bindParam(':endereco', $dados['endereco']);
            $edit_proprietario->bindParam(':id', $dados['id']);

            if ($edit_proprietario->execute()) {
                $retornar = ['erro' => false, 'msg' => "<div class='alert alert-success' role='alert'>Proprietário editado com sucesso!</div>"];
            } else {
                $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Proprietário não editado com sucesso!</div>"];
            }
        }
    }

    echo json_encode($retornar);
?>