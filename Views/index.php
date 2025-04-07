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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://icons.getbootstrap.com/">
    <link rel="stylesheet" href="../Views/CSS/style-index.css">
    <link rel="shortcut icon" href="../Views/Dashboard-main/img/logo_resin.ico">
</head>
<body>
<div class="container">
        <!-- First Content -->
        <div class="content first-content">
            <div class="first-column">
                <img class="img" src="../Views/imgs/logo_resi.png" alt="Logo">
                <h2 class="title title-primary"> Bem-Vindo ao RESINGOLA </h2>
                <p class="description description-primary">Para continuar a gerenciar a App</p>
                <p class="description description-primary">Por favor entre com as suas infomações pessoais!</p>

                <button id="signin" class="btn btn-primary"> Entrar </button>
            </div>
            <div class="second-column <?= isActiveForm('signup', $activeForm) ?>" id="signup-form">
                <h2 class="title title-second"> Criar Conta </h2>
                <div class="social-media">
                    <ul class="list-social-media">
                        <a href="#" class="link-social-media">
                            <li class="item-social-media">
                                <i class="fa fa-facebook" aria-hidden="true"></i>
                            </li>
                        </a>
                        <a href="#" class="link-social-media">
                            <li class="item-social-media">
                                <i class="fa fa-google" aria-hidden="true"></i>
                            </li>
                        </a>
                        <a href="#" class="link-social-media">
                            <li class="item-social-media">
                                <i class="fa fa-linkedin" aria-hidden="true"></i>
                            </li>
                        </a>
                    </ul>
                </div>
                <p class="description description-second">ou use o seu e-mail para o cadastro:</p>
                    <form action="../Controllers/login_signup.php" method="post" class="form">

                        <?= showError($errors['signup']); ?>

                        <label for="nome" class="label-input">
                            <i class="bi bi-person icon-modify"></i>
                            <input type="text" name="nome" placeholder="Nome Completo" required>
                        </label>

                        <label for="email" class="label-input">
                            <i class="bi bi-envelope-at-fill icon-modify"></i>
                            <input type="email" name="email" placeholder="Exemple: email123@gmail.com" required>
                        </label>

                        <label for="senha" class="label-input">
                            <i class="bi bi-lock icon-modify"></i>
                            <input type="password" name="senha" placeholder="* * * * * * *" required>
                        </label>

                        <label for="" class="label-input">
                            <select name="role" id="role" class="label-input">
                                <option value="">----- Select Role -----</option>
                                <option value="admin">Admin</option>
                                <!--<option value="user">User</option>-->
                            </select>
                        </label>

                        <button class="btn btn-second" type="submit" name="signup"> Cadastrar </i></button>
                    </form>
            </div>
        </div>

        <!-- Second Content -->
        <div class="content second-content">
            <div class="first-column">
                <img class="img" src="../Views/imgs/logo_resi.png" alt="Logo">
                <h2 class="title title-primary"> Welcome to RESINGOLA </h2>
                <p class="description description-primary">Digite seus dados pessoais</p>
                <p class="description description-primary">e começa a gerenciar a App</p>

                <button id="signup1" class="btn btn-primary"> Cadastrar </button>
            </div>
            <div class="second-column">
                <h2 class="title title-second"> Iniciar sessão </h2>
                <div class="social-media">
                    <ul class="list-social-media">
                        <a href="#" class="link-social-media">
                            <li class="item-social-media">
                                <i class="fa fa-facebook" aria-hidden="true"></i>
                            </li>
                        </a>
                        <a href="#" class="link-social-media">
                            <li class="item-social-media">
                                <i class="fa fa-google" aria-hidden="true"></i>
                            </li>
                        </a>
                        <a href="#" class="link-social-media">
                            <li class="item-social-media">
                                <i class="fa fa-linkedin" aria-hidden="true"></i>
                            </li>
                        </a>
                    </ul>
                </div>
                <p class="description description-second">ou use sua conta de e-mail:</p>
                <!--<div class="<?= isActiveForm('login', $activeForm) ?>" id="login-form">-->
                    <form action="../Controllers/login_signup.php" method="post" class="form">

                        <?= showError($errors['login']); ?>

                        <label for="email" class="label-input">
                            <i class="bi bi-envelope-at-fill icon-modify"></i>
                            <input type="email" name="email" placeholder="Exemple: email123@gmail.com" required>
                        </label>

                        <label for="senha" class="label-input">
                            <i class="bi bi-lock icon-modify"></i>
                            <input type="password" name="senha" placeholder="* * * * * * *" required>
                        </label>

                        <a href="#" class="password">forgot your password?</a>

                        <button class="btn btn-second" type="submit" name="login"> Entrar </button>

                    </form>
                <!--</div>-->
            </div>
        </div>
   </div>

   </div>
   <script src="../js/login_signup.js"></script>
</body>
</html>