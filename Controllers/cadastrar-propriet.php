<?php
    
    include_once "../Config/conection.php";

    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if(empty($dados['nome'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário preencher o campo Nome!</div>"];
    } elseif(!preg_match('/^[a-zA-Z\s]+$/', $dados['nome'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Nome inválido! Informe apenas letras e espaços.</div>"];
    } elseif(empty($dados['email'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário preencher o campo E-mail!</div>"];
    } elseif (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: E-mail inválido!</div>"];
    } elseif(empty($dados['tel'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário preencher o campo de Contato!</div>"];
    } elseif(!preg_match('/^[0-9]{9,15}$/', $dados['tel'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Contato inválido! Informe apenas números com 9 a 15 dígitos.</div>"];
    } elseif(empty($dados['BI'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário preencher o campo de Bilhete de Identidade!</div>"];
    } elseif(!preg_match('/^[0-9]{9}[A-Z]{2}[0-9]{3}$/', $dados['BI'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Bilhete de Identidade inválido!</div>"];
    } elseif(empty($dados['endereco'])) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário preencher o campo do seu Endereço!</div>"];
    } else {
        // Verificar se o e-mail já está cadastrado
        $query_verifica_email = "SELECT id FROM usuario WHERE email = :email LIMIT 1";
        $verifica_email = $conn->prepare($query_verifica_email);
        $verifica_email->bindParam(':email', $dados['email']);
        $verifica_email->execute();

        if ($verifica_email->rowCount() > 0) {
            $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Este e-mail já está cadastrado!</div>"];
        } else {
            // Inserir novo usuário
            $query_proprietario = "INSERT INTO proprietario (nome, email, tel, BI, endereco) VALUES (:nome, :email, :tel, :BI, :endereco)";
            $cad_proprietario = $conn->prepare($query_proprietario);
            $cad_proprietario->bindParam(':nome', $dados['nome']);
            $cad_proprietario->bindParam(':email', $dados['email']);
            $cad_proprietario->bindParam(':tel', $dados['tel']);
            $cad_proprietario->bindParam(':BI', $dados['BI']);
            $cad_proprietario->bindParam(':endereco', $dados['endereco']);

            if ($cad_proprietario->execute()) {
                $retornar = ['erro' => false, 'msg' => "<div class='alert alert-success' role='alert'>Proprietário cadastrado com sucesso!</div>"];
            } else {
                $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Proprietário não cadastrado com sucesso!</div>"];
            }
        }
    }

    echo json_encode($retornar);
?>