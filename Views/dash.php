<?php
session_start();
include_once '../Config/conection.php';

// Total de usuários
$stmt = $conn->query("SELECT COUNT(*) AS total FROM usuario");
$usuarios = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Residências à venda
$stmt = $conn->query("SELECT COUNT(*) AS total FROM residencia WHERE status = 'venda'");
$venda = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Residências à renda
$stmt = $conn->query("SELECT COUNT(*) AS total FROM residencia WHERE status = 'arrendamento'");
$renda = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Calcular porcentagens (adicione estas linhas)
$totalResidencias = $venda + $renda;
$percentUsuarios = $usuarios > 0 ? ($usuarios / ($usuarios + $totalResidencias)) * 100 : 0;
$percentVenda = $totalResidencias > 0 ? ($venda / $totalResidencias) * 100 : 0;
$percentRenda = $totalResidencias > 0 ? ($renda / $totalResidencias) * 100 : 0;

// Verificação de login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: ../Views/index.php");
    exit();
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://icons.getbootstrap.com/">
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
			<li><a href="../Views/map/map.php"><i class='bx bxs-widget icon' ></i> Mapa </a></li>
			<li class="divider" data-text="table">Tabelas</li>
			<li>
				<a href="#"><i class='bx bxs-notepad icon' ></i> Listagens <i class='bx bx-chevron-right icon-right' ></i></a>
				<ul class="side-dropdown">
					<li><a href="../Views/listarUsuarios.php">Listar Usuários</a></li>
					<li><a href="../Views/listarAdmin.php">Listar Administradores</a></li>
					<li><a href="../Views/listarResidencias.php">Listar Residências</a></li>
					<li><a href="../Views/listagemGeral.php">Dados - Residência & Proprietário</a></li>
				</ul>
			</li>
			<li class="divider" data-text="profile">Perfil</li>
			<li>
				<a href="#"><i class="bi bi-person-fill icon"></i> Meu Perfil <i class='bx bx-chevron-right icon-right' ></i></a>
				<ul class="side-dropdown">
					<li><a href="../Views/Perfil-Admin/perfil-admin.php"> Perfil </a></li>
					<li><a href="../Models/logout.php"> Terminar Sessão </a></li>
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
			<div class="profile">
				<?php
				$nomeAdmin = htmlspecialchars($_SESSION['nome'] ?? 'Admin');
				$partesNome = array_filter(explode(' ', $nomeAdmin)); // Remove valores vazios
				
				$iniciais = '';
				if (!empty($partesNome)) {
					$iniciais .= strtoupper(substr($partesNome[0], 0, 1));
					if (count($partesNome) > 1) {
						$iniciais .= strtoupper(substr(end($partesNome), 0, 1));
					}
				}
				?>
				
				<div class="profile-initials" style="
					width: 40px;
					height: 40px;
					border-radius: 50%;
					background: #4e73df;
					color: white;
					display: flex;
					align-items: center;
					justify-content: center;
					font-weight: bold;
					font-size: 16px;
				"><?= $iniciais ?: 'AD' ?></div>
				
				<ul class="profile-link">
					<li><a href="#"><i class='bx bxs-user-circle icon'></i> Perfil</a></li>
					<li><a href="#"><i class='bx bxs-cog'></i> Configurações</a></li>
					<li><a href="../Models/logout.php"><i class='bx bxs-log-out-circle'></i> Sair</a></li>
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
							<h2><?= $usuarios ?></h2>
							<p>Usuários</p>
						</div>
						<i class='bx bx-trending-down icon down'></i>
					</div>
					<span class="progress" data-value="<?= round($percentUsuarios) ?>%"></span>
					<span class="label"><?= round($percentUsuarios) ?>%</span>
				</div>

				<div class="card">
					<div class="head">
						<div>
							<h2><?= $venda ?></h2>
							<p>Imóveis à Venda</p>
						</div>
						<i class='bx bx-trending-up icon'></i>
					</div>
					<span class="progress" data-value="<?= round($percentVenda) ?>%"></span>
					<span class="label"><?= round($percentVenda) ?>%</span>
				</div>

				<div class="card">
					<div class="head">
						<div>
							<h2><?= $renda ?></h2>
							<p>Imóveis em Arrendamento</p>
						</div>
						<i class='bx bx-trending-up icon'></i>
					</div>
					<span class="progress" data-value="<?= round($percentRenda) ?>%"></span>
					<span class="label"><?= round($percentRenda) ?>%</span>
				</div>
			</div>

			<!--==========================Adicionandos os Gráficos================================-->
			<div class="data">
				<div class="content-data">
					<div class="head">
						<h3>Balanço Geral dos Gráficos</h3>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
	<script src="/js/my_chart.js"></script>

		<!-- Carregue APENAS UMA VERSÃO de cada biblioteca -->
	<script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0/dist/apexcharts.min.js"></script>

	<!-- Seu script.js deve vir DEPOIS -->
	<script src="../js/script.js"></script>

	<script src="../js/script.js"></script>
	
	<script>
	// Inicialização dos gráficos
	document.addEventListener('DOMContentLoaded', function() {
		// Gráfico ApexCharts
		var options = {
			series: [
				{
					name: 'Usuários',
					data: [<?= $usuarios ?>],
					color: '#6c5ce7'
				},
				{
					name: 'Vendas',
					data: [<?= $venda ?>],
					color: '#00b894'
				},
				{
					name: 'Arrendamentos',
					data: [<?= $renda ?>],
					color: '#fd79a8'
				}
			],
			chart: {
				type: 'bar',
				height: 350,
				toolbar: {
					show: true
				}
			},
			plotOptions: {
				bar: {
					horizontal: false,
					columnWidth: '55%',
					endingShape: 'rounded'
				},
			},
			dataLabels: {
				enabled: false
			},
			stroke: {
				show: true,
				width: 2,
				colors: ['transparent']
			},
			xaxis: {
				categories: ['Controle Geral'],
			},
			yaxis: {
				title: {
					text: 'Quantidade'
				}
			},
			fill: {
				opacity: 1
			},
			tooltip: {
				y: {
					formatter: function (val) {
						return val + " registros"
					}
				}
			}
		};

		var chart = new ApexCharts(document.querySelector("#chart"), options);
		chart.render();

		// Gráficos Chart.js
		const ctx1 = document.getElementById('myChart').getContext('2d');
		new Chart(ctx1, {
			type: 'bar',
			data: {
				labels: ['Venda', 'Arrendamento'],
				datasets: [{
					label: 'Imóveis',
					data: [<?= $venda ?>, <?= $renda ?>],
					backgroundColor: ['#4e73df', '#1cc88a']
				}]
			},
			options: {
				responsive: true,
				plugins: {
					legend: {
						position: 'top',
					},
					title: {
						display: true,
						text: 'Distribuição de Imóveis'
					}
				}
			}
		});

		const ctx2 = document.getElementById('earning').getContext('2d');
		new Chart(ctx2, {
			type: 'doughnut',
			data: {
				labels: ['Usuários', 'Imóveis à Venda', 'Imóveis para Arrendamento'],
				datasets: [{
					data: [<?= $usuarios ?>, <?= $venda ?>, <?= $renda ?>],
					backgroundColor: ['#36b9cc', '#4e73df', '#1cc88a'],
					hoverOffset: 10
				}]
			},
			options: {
				responsive: true,
				plugins: {
					legend: {
						position: 'bottom',
					},
					title: {
						display: true,
						text: 'Distribuição Geral'
					}
				}
			}
		});
	});
	</script>

</body>
</html>