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
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link rel="stylesheet" href="../Views/CSS/style.css">
	<link rel="shortcut icon" href="../Views/Dashboard-main/img/logo_resin.ico">
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
		</ul>
		<div class="ads">
			<div class="wrapper">
				<a href="../Views/dash.php" class="btn-upgrade">Atualizar</a>
				<!--<p>torne se <span>PRO</span> um membro <span>Aproveite os recursos</span></p>-->
			</div>
		</div>
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
			<h1 class="title">Dashboard</h1>
			<ul class="breadcrumbs">
				<li><a href="../Views/dash.php">inicio</a></li>
				<li class="divider">/</li>
				<li><a href="#" class="active">Dashboard</a></li>
			</ul>
			<!--==========================Adicionandos os Cards================================-->
			<div class="info-data">
				<div class="card">
					<div class="head">
						<div>
							<h2>1500</h2>
							<p>Proprietário</p>
						</div>
						<i class='bx bx-trending-up icon' ></i>
					</div>
					<span class="progress" data-value="40%"></span>
					<span class="label">40%</span>
				</div>
				<div class="card">
					<div class="head">
						<div>
							<h2>234</h2>
							<p>Usuários</p>
						</div>
						<i class='bx bx-trending-down icon down' ></i>
					</div>
					<span class="progress" data-value="60%"></span>
					<span class="label">60%</span>
				</div>
				<div class="card">
					<div class="head">
						<div>
							<h2>245</h2>
							<p>Residências à Venda</p>
						</div>
						<i class='bx bx-trending-up icon' ></i>
					</div>
					<span class="progress" data-value="35%"></span>
					<span class="label">35%</span>
				</div>
				<div class="card">
					<div class="head">
						<div>
							<h2>465</h2>
							<p>Residências à Renda</p>
						</div>
						<i class='bx bx-trending-up icon' ></i>
					</div>
					<span class="progress" data-value="75%"></span>
					<span class="label">75%</span>
				</div>
			</div>
			<!--==========================Adicionandos os Gráficos================================-->
			<div class="data">
				<div class="content-data">
					<div class="head">
						<h3>relatório de vendas e alugueres</h3>
						<div class="menu">
							<i class='bx bx-dots-horizontal-rounded icon'></i>
							<ul class="menu-link">
								<!--<li><a href="#">Edit</a></li>-->
								<li><a href="#">Save</a></li>
								<!--<li><a href="#">Remove</a></li>-->
							</ul>
						</div>
					</div>
					<div class="chart">
						<div id="chart"></div>
					</div>
				</div>
				<div class="graphBox">
					<div class="box">
						<canvas id="myChart"></canvas>
					</div>
					<div class="box">
						<canvas id="earning"></canvas>
					</div>
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