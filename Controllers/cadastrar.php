<?php
    
    include_once "../Config/conection.php";

    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if(empty($dados['nome'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário preencher o campo Nome!</div>"];
    } elseif(!preg_match('/^[\p{L}\s]+$/u', $dados['nome'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Nome inválido! Informe apenas letras e espaços.</div>"];
    } elseif(empty($dados['email'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário preencher o campo E-mail!</div>"];
    } elseif (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: E-mail inválido!</div>"];
    } elseif (empty($dados['senha'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário preencher o campo Senha!</div>"];
    } elseif (strlen($dados['senha']) < 6) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: A senha deve ter no mínimo 6 caracteres!</div>"];
    }  else {
        // Verificar se o e-mail já está cadastrado
        $query_verifica_email = "SELECT id FROM usuario WHERE email = :email LIMIT 1";
        $verifica_email = $conn->prepare($query_verifica_email);
        $verifica_email->bindParam(':email', $dados['email']);
        $verifica_email->execute();

        if ($verifica_email->rowCount() > 0) {
            $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Este e-mail já está cadastrado!</div>"];
        } else {
            // Hash da senha
            $senha_hash = password_hash($dados['senha'], PASSWORD_DEFAULT);

            // Inserir novo usuário
            $query_usuario = "INSERT INTO usuario (nome, email, senha) VALUES (:nome, :email, :senha)";
            $cad_usuario = $conn->prepare($query_usuario);
            $cad_usuario->bindParam(':nome', $dados['nome']);
            $cad_usuario->bindParam(':email', $dados['email']);
            $cad_usuario->bindParam(':senha', $senha_hash);

            if ($cad_usuario->execute()) {
                $retornar = ['erro' => false, 'msg' => "<div class='alert alert-success' role='alert'>Usuário cadastrado com sucesso!</div>"];
            } else {
                $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Usuário não cadastrado com sucesso!</div>"];
            }
        }
    }

    echo json_encode($retornar);
?>