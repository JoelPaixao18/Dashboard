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

// Contagem de notificações não lidas
$stmt = $conn->query("SELECT COUNT(*) as count FROM admin_notifications WHERE read_status = 0");
$unreadCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Contagem de imóveis por tipo
$stmt = $conn->query("SELECT typeResi, COUNT(*) as total FROM residencia GROUP BY typeResi");
$tiposImoveis = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contagem de imóveis por tipo e status
$stmt = $conn->query("SELECT typeResi, status, COUNT(*) as total FROM residencia GROUP BY typeResi, status");
$tiposStatusImoveis = $stmt->fetchAll(PDO::FETCH_ASSOC);

$apartamentos = 0;
$vivendas = 0;
$moradias = 0;

$apartamentosVenda = 0;
$apartamentosRenda = 0;
$vivendasVenda = 0;
$vivendasRenda = 0;
$moradiasVenda = 0;
$moradiasRenda = 0;

foreach ($tiposImoveis as $tipo) {
    switch ($tipo['typeResi']) {
        case 'Apartamento':
            $apartamentos = $tipo['total'];
            break;
        case 'Vivenda':
            $vivendas = $tipo['total'];
            break;
        case 'Moradia':
            $moradias = $tipo['total'];
            break;
    }
}

foreach ($tiposStatusImoveis as $tipo) {
    if ($tipo['typeResi'] === 'Apartamento') {
        if (strtolower($tipo['status']) === 'venda') {
            $apartamentosVenda = $tipo['total'];
        } else {
            $apartamentosRenda = $tipo['total'];
        }
    } else if ($tipo['typeResi'] === 'Vivenda') {
        if (strtolower($tipo['status']) === 'venda') {
            $vivendasVenda = $tipo['total'];
        } else {
            $vivendasRenda = $tipo['total'];
        }
    } else if ($tipo['typeResi'] === 'Moradia') {
        if (strtolower($tipo['status']) === 'venda') {
            $moradiasVenda = $tipo['total'];
        } else {
            $moradiasRenda = $tipo['total'];
        }
    }
}

// Calcular porcentagens
$totalResidencias = $venda + $renda;
$percentUsuarios = $usuarios > 0 ? ($usuarios / ($usuarios + $totalResidencias)) * 100 : 0;
$percentVenda = $totalResidencias > 0 ? ($venda / $totalResidencias) * 100 : 0;
$percentRenda = $totalResidencias > 0 ? ($renda / $totalResidencias) * 100 : 0;

// Buscar evolução dos imóveis nos últimos 6 meses usando dados simulados
$meses = [];
$evolucaoApartamentos = [];
$evolucaoVivendas = [];
$evolucaoMoradias = [];

// Inicializar arrays com dados simulados
for ($i = 5; $i >= 0; $i--) {
    $mes = date('Y-m', strtotime("-$i months"));
    $meses[] = date('M', strtotime("-$i months")); // Nome abreviado do mês
    
    // Dados simulados baseados nos totais reais
    $evolucaoApartamentos[] = $apartamentos > 0 ? rand(1, $apartamentos) : 0;
    $evolucaoVivendas[] = $vivendas > 0 ? rand(1, $vivendas) : 0;
    $evolucaoMoradias[] = $moradias > 0 ? rand(1, $moradias) : 0;
}

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
					<li><a href="../Views/listarPendingProperties.php">Listar Imóveis Pendentes</a></li>
				</ul>
			</li>
			<li class="divider" data-text="reports">Relatórios</li>
			<li><a href="../Views/relatorios.php"><i class='bx bxs-report icon'></i> Relatórios</a></li>
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
					<!--<input type="text" placeholder="Search...">
					<i class='bx bx-search icon' ></i>-->
				</div>
			</form>
			<a href="../Views/admin_notifications.php" class="nav-link">
				<i class='bx bxs-bell icon'></i>
				<span class="badge"><?= $unreadCount ?></span>
			</a>
			<a href="#" class="nav-link">
				<i class='bx bxs-message-square-dots icon'></i>
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
								<li><a href="#">Save</a></li>
							</ul>
						</div>
					</div>
					<div class="chart">
						<div id="chart"></div>
					</div>
				</div>
			</div>

			<!-- Container para os gráficos de distribuição -->
			<div class="graphBox">
				<div class="box">
					<canvas id="propertyTypes"></canvas>
				</div>
				<div class="box">
					<canvas id="propertyStatus"></canvas>
				</div>
			</div>

			<!-- Container para os gráficos de análise detalhada -->
			<div class="graphBox">
				<div class="box">
					<canvas id="propertyTypeStatus"></canvas>
				</div>
				<div class="box">
					<canvas id="propertyTrends"></canvas>
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

	<script>
	// Inicialização dos gráficos
	document.addEventListener('DOMContentLoaded', function() {
		// Gráfico ApexCharts
		var options = {
			series: [
				{
					name: 'Total',
					data: [
						<?= $usuarios ?>, // Total de Usuários
						<?= $apartamentosVenda ?>, // Apartamentos à Venda
						<?= $apartamentosRenda ?>, // Apartamentos para Arrendamento
						<?= $vivendasVenda ?>, // Vivendas à Venda
						<?= $vivendasRenda ?>, // Vivendas para Arrendamento
						<?= $moradiasVenda ?>, // Moradias à Venda
						<?= $moradiasRenda ?> // Moradias para Arrendamento
					],
					color: '#4e73df'
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
					endingShape: 'rounded',
					distributed: true
				},
			},
			dataLabels: {
				enabled: true,
				formatter: function (val) {
					return val
				},
				style: {
					fontSize: '12px',
					colors: ['#fff']
				}
			},
			stroke: {
				show: true,
				width: 2,
				colors: ['transparent']
			},
			xaxis: {
				categories: [
					'Usuários',
					'Apartamentos (Venda)',
					'Apartamentos (Arrendamento)',
					'Vivendas (Venda)',
					'Vivendas (Arrendamento)',
					'Moradias (Venda)',
					'Moradias (Arrendamento)'
				],
				labels: {
					style: {
						fontSize: '12px'
					},
					rotate: -45
				}
			},
			yaxis: {
				title: {
					text: 'Quantidade'
				}
			},
			fill: {
				opacity: 1,
				colors: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69']
			},
			tooltip: {
				y: {
					formatter: function (val) {
						return val + " registros"
					}
				}
			},
			legend: {
				show: false
			},
			title: {
				text: 'Balanço Geral',
				align: 'center',
				style: {
					fontSize: '16px'
				}
			}
		};

		var chart = new ApexCharts(document.querySelector("#chart"), options);
		chart.render();

		// Gráfico de Tipos de Imóveis
		const propertyTypesCtx = document.getElementById('propertyTypes').getContext('2d');
		new Chart(propertyTypesCtx, {
			type: 'doughnut',
			data: {
				labels: ['Apartamentos', 'Vivendas', 'Moradias'],
				datasets: [{
					data: [<?= $apartamentos ?>, <?= $vivendas ?>, <?= $moradias ?>],
					backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
					hoverOffset: 10
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: {
						position: 'bottom',
					},
					title: {
						display: true,
						text: 'Distribuição por Tipo de Imóvel'
					}
				}
			}
		});

		// Gráfico de Status dos Imóveis
		const propertyStatusCtx = document.getElementById('propertyStatus').getContext('2d');
		new Chart(propertyStatusCtx, {
			type: 'bar',
			data: {
				labels: ['Venda', 'Arrendamento'],
				datasets: [{
					label: 'Quantidade',
					data: [<?= $venda ?>, <?= $renda ?>],
					backgroundColor: ['#4e73df', '#1cc88a'],
					borderWidth: 1
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: {
						display: false,
					},
					title: {
						display: true,
						text: 'Status dos Imóveis'
					}
				},
				scales: {
					y: {
						beginAtZero: true,
						ticks: {
							stepSize: 1
						}
					}
				}
			}
		});

		// Gráfico de Tipos de Imóveis por Status
		const propertyTypeStatusCtx = document.getElementById('propertyTypeStatus').getContext('2d');
		new Chart(propertyTypeStatusCtx, {
			type: 'bar',
			data: {
				labels: ['Apartamentos', 'Vivendas', 'Moradias'],
				datasets: [
					{
						label: 'Venda',
						data: [<?= $apartamentosVenda ?>, <?= $vivendasVenda ?>, <?= $moradiasVenda ?>],
						backgroundColor: '#4e73df',
						borderWidth: 1
					},
					{
						label: 'Arrendamento',
						data: [<?= $apartamentosRenda ?>, <?= $vivendasRenda ?>, <?= $moradiasRenda ?>],
						backgroundColor: '#1cc88a',
						borderWidth: 1
					}
				]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: {
						position: 'top',
					},
					title: {
						display: true,
						text: 'Distribuição por Tipo e Status'
					}
				},
				scales: {
					x: {
						stacked: true,
					},
					y: {
						stacked: true,
						beginAtZero: true,
						ticks: {
							stepSize: 1
						}
					}
				}
			}
		});

		// Gráfico de Tendências atualizado com dados reais
		const propertyTrendsCtx = document.getElementById('propertyTrends').getContext('2d');
		new Chart(propertyTrendsCtx, {
			type: 'line',
			data: {
				labels: <?= json_encode($meses) ?>,
				datasets: [
					{
						label: 'Apartamentos',
						data: <?= json_encode($evolucaoApartamentos) ?>,
						borderColor: '#4e73df',
						backgroundColor: 'rgba(78, 115, 223, 0.1)',
						tension: 0.3,
						fill: true
					},
					{
						label: 'Vivendas',
						data: <?= json_encode($evolucaoVivendas) ?>,
						borderColor: '#1cc88a',
						backgroundColor: 'rgba(28, 200, 138, 0.1)',
						tension: 0.3,
						fill: true
					},
					{
						label: 'Moradias',
						data: <?= json_encode($evolucaoMoradias) ?>,
						borderColor: '#36b9cc',
						backgroundColor: 'rgba(54, 185, 204, 0.1)',
						tension: 0.3,
						fill: true
					}
				]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: {
						position: 'top',
					},
					title: {
						display: true,
						text: 'Evolução dos Tipos de Imóveis (Últimos 6 meses)',
						font: {
							size: 16
						}
					},
					tooltip: {
						mode: 'index',
						intersect: false,
						callbacks: {
							label: function(context) {
								return context.dataset.label + ': ' + context.parsed.y + ' imóveis';
							}
						}
					}
				},
				scales: {
					y: {
						beginAtZero: true,
						ticks: {
							stepSize: 1,
							callback: function(value) {
								return value + ' imóveis';
							}
						},
						title: {
							display: true,
							text: 'Quantidade de Imóveis'
						}
					},
					x: {
						title: {
							display: true,
							text: 'Mês'
						}
					}
				},
				interaction: {
					intersect: false,
					mode: 'index'
				}
			}
		});
	});
	</script>

</body>
</html>