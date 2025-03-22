<?php
session_start();
require_once '../Config/conection.php';

if (isset($_POST['signup'])) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $role = 'user'; // Definição padrão

    // Verificando se o email já existe
    $verificarEmail = $conn->prepare("SELECT email FROM usuario WHERE email = :email");
    $verificarEmail->execute(['email' => $email]);
    if ($verificarEmail->rowCount() > 0) {
        $_SESSION['signup_error'] = 'Este email já existe!';
        $_SESSION['active_form'] = 'signup';
        header("Location: ../Views/index.php");
        exit();
    }

    // Verificando o número de administradores
    if (isset($_POST['role']) && $_POST['role'] === 'admin') {
        $verificarAdmins = $conn->query("SELECT id FROM usuario WHERE role = 'admin'");
        if ($verificarAdmins->rowCount() >= 2) {
            $_SESSION['signup_error'] = 'Já existem dois administradores!';
            $_SESSION['active_form'] = 'signup';
            header("Location: ../Views/index.php");
            exit();
        }
        $role = 'admin';
    }

    // Inserir novo usuário
    $stmt = $conn->prepare("INSERT INTO usuario (nome, email, senha, role) VALUES (:nome, :email, :senha, :role)");
    $stmt->execute(['nome' => $nome, 'email' => $email, 'senha' => $senha, 'role' => $role]);

    // Redirecionando para a tela de login
    $_SESSION['signup_success'] = 'Cadastro realizado com sucesso! Faça login para continuar.';
    $_SESSION['active_form'] = 'login';
    header("Location: ../Views/index.php");
    exit();
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Buscando o usuário no banco de dados com o email fornecido
    $stmt = $conn->prepare("SELECT * FROM usuario WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verificando a senha
        if (password_verify($senha, $user['senha'])) {
            $_SESSION['nome'] = $user['nome'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: ../Views/dash.php");
                exit();
            } else {
                $_SESSION['login_error'] = 'Acesso negado! Apenas administradores podem acessar esta área.';
                $_SESSION['active_form'] = 'login';
                header("Location: ../Views/index.php");
                exit();
            }
        }
    }

    // Caso a senha esteja incorreta
    $_SESSION['login_error'] = 'Email ou Senha incorreto!';
    $_SESSION['active_form'] = 'login';
    header("Location: ../Views/index.php");
    exit();
}
?>