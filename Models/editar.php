<?php
    
    include_once "../Config/conection.php";

    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if(empty($dados['id'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Tente novamente ou tente mais tarde!</div>"];
    } elseif(!preg_match('/^[\p{L}\s]+$/u', $dados['nome'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Nome inválido! Informe apenas letras e espaços.</div>"];
    } elseif(empty($dados['nome'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário preencher o campo Nome!</div>"];
    } elseif(empty($dados['email'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário preencher o campo E-mail!</div>"];
    } elseif (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: E-mail inválido!</div>"];
    }else {
        // Verificar se o e-mail já está cadastrado
        $query_verifica_email = "SELECT id FROM usuario WHERE email = :email LIMIT 1";
        $verifica_email = $conn->prepare($query_verifica_email);
        $verifica_email->bindParam(':email', $dados['email']);
        $verifica_email->execute();

        if ($verifica_email->rowCount() > 0) {
            $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Este e-mail já está cadastrado!</div>"];
        } else {

            // Editar usuário
            $query_usuario = "UPDATE usuario SET nome = :nome, email = :email WHERE id = :id";
            $edit_usuario = $conn->prepare($query_usuario);
            $edit_usuario->bindParam(':nome', $dados['nome']);
            $edit_usuario->bindParam(':email', $dados['email']);
            $edit_usuario->bindParam(':id', $dados['id']);

            if ($edit_usuario->execute()) {
                $retornar = ['erro' => false, 'msg' => "<div class='alert alert-success' role='alert'>Usuário editado com sucesso!</div>"];
            } else {
                $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Usuário não editado com sucesso!</div>"];
            }
        }
    }

    echo json_encode($retornar);
?>