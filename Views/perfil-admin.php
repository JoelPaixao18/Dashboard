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
	<link rel="stylesheet" href="https://icons.getbootstrap.com/icons/trash3-fill/">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.14.1/themes/smoothness/jquery-ui.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://icons.getbootstrap.com/">
	<link rel="stylesheet" href="../Views/CSS/style.css">
	<link rel="stylesheet" href="../Views/CSS/styles-dash.css">
	<link rel="shortcut icon" href="../Views/Dashboard-main/img/logo_resin.ico">
	<link rel="stylesheet" href="../Views/CSS/style-perfil.css">
	<title>Painel Administrativo</title>
</head>
<body>
	
	<!-- SIDEBAR -->
	<section id="sidebar">
		<img src="../Views/Dashboard-main/img/logo_resi.png" alt="Logo">
		<ul class="side-menu">
			<li><a href="#" class="active"><i class='bx bxs-dashboard icon' ></i> Dashboard</a></li>
			<li class="divider" data-text="main">Main</li>
			<li>
				<a href="#"><i class='bx bxs-inbox icon' ></i> Elementos <i class='bx bx-chevron-right icon-right' ></i></a>
				<ul class="side-dropdown">
					<li><a href="#">Alerta</a></li>
					<li><a href="#">Mensagens</a></li>
				</ul>
			</li>
			<li><a href="../Views/dash.php"><i class='bx bxs-chart icon' ></i> Graficos</a></li>
			<li><a href="#"><i class='bx bxs-widget icon' ></i> Mapa </a></li>
			<li class="divider" data-text="table">Tabelas</li>
			<li>
				<a href="#"><i class='bx bxs-notepad icon' ></i> Listagens <i class='bx bx-chevron-right icon-right' ></i></a>
				<ul class="side-dropdown">
					<li><a href="../Views/listarUsuarios.php">Listar Usuários</a></li>
					<li><a href="../Views/listarProprietarios.php">Listar Proprietários</a></li>
					<li><a href="../Views/listarResidencias.php">Listar Residências</a></li>
					<li><a href="../Views/listagemGeral.php">Dados - Residência & Proprietário</a></li>
				</ul>
			</li>
			<li class="divider" data-text="profile">Perfil</li>
			<li>
				<a href="#"><i class="bi bi-person-fill icon"></i> Perfil <i class='bx bx-chevron-right icon-right' ></i></a>
				<ul class="side-dropdown">
					<li><a href="#"> Vizualizar Perfil </a></li>
					<li><a href="#"> Editar Perfil </a></li>
					<li><a href="../Models/logout.php"> Sair </a></li>
				</ul>
			</li>
            <li class="divider" data-text="settings"> Configurações </li>
			<li>
				<a href="#"><i class="bi bi-gear icon"></i> Settings <i class='bx bx-chevron-right icon-right' ></i></a>
				<ul class="side-dropdown">
					<li><a href="#" class="side-dropdown"><i class="bi bi-key icon"></i> Conta </a>
                    </li>
					<li><a href="#"><i class="bi bi-question-circle icon"></i> Ajuda </a></li>
				</ul>
			</li>
		</ul>
		<!--<div class="ads">
			<div class="wrapper">
				<a href="../Views/dash.php" class="btn-upgrade">Atualizar</a>
				<p>torne se <span>PRO</span> um membro <span>Aproveite os recursos</span></p>
			</div>
		</div>-->
	</section>
	<!-- SIDEBAR -->

	<!-- NAVBAR -->
	<section id="content">
		<!-- NAVBAR -->
		<nav>
			<i class='bx bx-menu toggle-sidebar' ></i>
			<form action="#">
				<div class="form-group">
					<input type="text" placeholder="Search...">
					<i class='bx bx-search icon' ></i>
				</div>
			</form>
			<a href="#" class="nav-link">
				<i class='bx bxs-bell icon' ></i>
				<span class="badge">5</span>
			</a>
			<a href="#" class="nav-link">
				<i class='bx bxs-message-square-dots icon' ></i>
				<span class="badge">8</span>
			</a>
			<span class="divider"></span>
			<div class="profile">
				<img src="../Views/Dashboard-main/img/IMG-20241121-WA0048.jpg" alt="">
				
				<ul class="profile-link">
					<li><a href="#"><i class='bx bxs-user-circle icon' ></i> Profil</a></li>
					<li><a href="#"><i class='bx bxs-cog' ></i> Settings</a></li>
					<li><a href="../Models/logout.php"><i class='bx bxs-log-out-circle' ></i> sair</a></li>
				</ul>
			</div>
		</nav>
		<!-- NAVBAR -->

		<!-- MAIN -->
		<main>
            <div class="container">
				
                <div class="content">
                    <form action="" class="form">
                        <h2 class="title">Meu Perfil</h2>
                        <label for="nome" class="label-input">
							<i class="bi bi-person icon-modify"></i>
                        	<input type="text" name="nome" placeholder="Nome Completo" required>
						</label>
                        <label for="email" class="label-input">
							<i class="bi bi-envelope-at-fill icon-modify"></i>
                        	<input type="email" name="email" placeholder="email@gmail.com" required>
						</label>
                        <label for="senha" class="label-input">
							<i class="bi bi-lock icon-modify"></i>
                        	<input type="password" name="senha" placeholder="* * * * * * *" required>
						</label>
                    </form>
                </div>

            </div>
		</main>
		<!-- MAIN -->
	</section>
	<!-- NAVBAR -->

	<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/js/my_chart.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
	<script src="../js/script.js"></script>
</body>
</html>