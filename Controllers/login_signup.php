
<?php
session_start();
require_once '../Config/conection.php';

if (isset($_POST['signup'])) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $tel = $_POST['tel'];
    $bi = $_POST['bi'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $role = 'admin'; // Definido como padrão

    // Verificando se o email já existe
    $verificarEmail = $conn->prepare("SELECT email FROM administrador WHERE email = :email OR bi = :bi");
    $verificarEmail->execute(['email' => $email, 'bi' => $bi]);
    
    if ($verificarEmail->rowCount() > 0) {
        $existingUser = $verificarEmail->fetch(PDO::FETCH_ASSOC);
        if ($existingUser['email'] === $email) {
            $_SESSION['signup_error'] = 'Este email já existe!';
        } else {
            $_SESSION['signup_error'] = 'Este número de BI já está registado!';
        }
        $_SESSION['active_form'] = 'signup';
        header("Location: ../Views/index.php");
        exit();
    }

    // Verificando o número de administradores
    $verificarAdmins = $conn->query("SELECT id FROM administrador WHERE role = 'admin'");
    if ($verificarAdmins->rowCount() >= 10) {
        $_SESSION['signup_error'] = 'Já existem +9 administradores!';
        $_SESSION['active_form'] = 'signup';
        header("Location: ../Views/index.php");
        exit();
    }

    // Inserir novo usuário
    $stmt = $conn->prepare("INSERT INTO administrador (nome, email, tel, bi, senha, role) VALUES (:nome, :email, :tel, :bi, :senha, :role)");
    $stmt->execute([
        'nome' => $nome, 
        'email' => $email, 
        'tel' => $tel,
        'bi' => $bi,
        'senha' => $senha, 
        'role' => $role
    ]);

    // Redirecionando para a tela de login
    $_SESSION['signup_success'] = 'Cadastro realizado com sucesso! Faça login para continuar.';
    $_SESSION['active_form'] = 'login';
    header("Location: ../Views/index.php");
    exit();
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $stmt = $conn->prepare("SELECT * FROM administrador WHERE email = :email");
    $stmt->execute(['email' => $email]);
    
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (password_verify($senha, $user['senha'])) {
            // Armazena TODOS os dados necessários na sessão
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nome'] = $user['nome'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['loggedin'] = true; // Flag importante

            // Debug: Verifique os dados da sessão
            error_log("Dados da sessão: " . print_r($_SESSION, true));

            if ($user['role'] === 'admin') {
                header("Location: ../Views/dash.php");
                exit();
            } else {
                $_SESSION['login_error'] = 'Apenas administradores podem acessar';
                header("Location: ../Views/index.php");
                exit();
            }
        }
    }

    $_SESSION['login_error'] = 'Credenciais inválidas';
    header("Location: ../Views/index.php");
    exit();
}
?>
