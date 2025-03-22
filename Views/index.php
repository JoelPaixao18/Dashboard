<?php

session_start();

$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'signup' => $_SESSION['signup_error'] ?? ''
];
$activeForm = $_SESSION['active_form'] ?? 'login';

session_unset();

function showError($error) {
    return !empty($error) ? "<p class='error_message'>$error</p>" : '';
}

function isActiveForm($formName, $activeForm) {
    return $formName === $activeForm ? 'active' : '';
}
?>

<!DOCTYPE html>
<html lang="pt-en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RESINGOLA</title>
    <script src="https://kit.fontawesome.com/ca14b9e588.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../Views/CSS/styles-login.css">
    <link rel="shortcut icon" href="../Views/Dashboard-main/img/logo_resin.ico">
</head>
<body>
   <div class="container">
    <div class="form-box <?= isActiveForm('login', $activeForm); ?>" id="login-form">
        <form action="../Controllers/login_signup.php" method="post">
            <h2>Login</h2>
            <?= showError($errors['login']); ?>
            <label for="email">E-mail</label>
            <input type="email" name="email" placeholder="email@gmail.com" required>
            <label for="senha">Senha</label>
            <input type="password" name="senha" placeholder="* * * * * * *" required>
            <button type="submit" name="login"> Entrar </button>
            <p>Não tens uma Conta? <a href="#" onclick="showForm('signup-form')"> Registrar </a></p>
        </form>
    </div>

    <div class="form-box <?= isActiveForm('signup', $activeForm); ?>" id="signup-form">
        <form action="../Controllers/login_signup.php" method="post">
            <h2>Cadastrar</h2>
            <?= showError($errors['signup']); ?>
            <label for="nome">Nome</label>
            <input type="text" name="nome" placeholder="Nome Completo" required>
            <label for="email">E-mail</label>
            <input type="email" name="email" placeholder="email@gmail.com" required>
            <label for="senha">Senha</label>
            <input type="password" name="senha" placeholder="* * * * * * *" required>
            <select name="role" id="role">
                <option value="">----- Select Role -----</option>
                <option value="admin">Admin</option>
                <!--<option value="user">User</option>-->
            </select>
            <button type="submit" name="signup"> Cadastrar </button>
            <p>Já tens uma Conta? <a href="#" onclick="showForm('login-form')"> Entrar </a></p>
        </form>
    </div>

   </div>
   <script src="../js/login_signup.js"></script>
</body>
</html>