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
} elseif(empty($dados['tel'])) {
    $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário preencher o campo Telefone!</div>"];
} elseif(empty($dados['bi'])) {
    $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Necessário preencher o campo BI!</div>"];
} else {
    // Verificar se o e-mail ou BI já está cadastrado para outro admin
    $query_verifica_email = "SELECT id FROM administrador WHERE (email = :email OR BI = :bi) AND id != :id LIMIT 1";
    $verifica_email = $conn->prepare($query_verifica_email);
    $verifica_email->bindParam(':email', $dados['email']);
    $verifica_email->bindParam(':bi', $dados['bi']);
    $verifica_email->bindParam(':id', $dados['id']);
    $verifica_email->execute();

    if ($verifica_email->rowCount() > 0) {
        $retornar = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: Este e-mail ou BI já está cadastrado para outro usuário!</div>"];
    } else {
        // Editar admin
        $query_usuario = "UPDATE administrador SET nome = :nome, email = :email, tel = :tel, BI = :bi WHERE id = :id";
        $edit_usuario = $conn->prepare($query_usuario);
        $edit_usuario->bindParam(':nome', $dados['nome']);
        $edit_usuario->bindParam(':email', $dados['email']);
        $edit_usuario->bindParam(':tel', $dados['tel']);
        $edit_usuario->bindParam(':bi', $dados['bi']);
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