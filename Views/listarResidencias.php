<?php
	include_once '../Config/conection.php';

	session_start();

	// Modifique a verificação para não interromper o carregamento dos dados
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
	<link rel="stylesheet" href="https://icons.getbootstrap.com/icons/trash3-fill/">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
    crossorigin="anonymous">
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link rel="stylesheet" href="../Views/CSS/style.css">
    <link rel="stylesheet" href="../Views/CSS/styles-dash.css">
	<link rel="shortcut icon" href="../Views/Dashboard-main/img/logo_resin.ico">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://icons.getbootstrap.com/">
	<title>Listagem das Residências</title>
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
					<li><a href="../Models/gerar_relatorio_residencia.php" target="_blank">Gerar Relatório - Proprietário</a></li>
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
				<a href="#"><i class="bi bi-person-fill icon"></i> Meu Perfil <i class='bx bx-chevron-right icon-right' ></i></a>
				<ul class="side-dropdown">
					<li><a href="../Views/perfil-admin.php"> Perfil </a></li>
					<li><a href="../Models/logout.php"> Terminar Sessão </a></li>
				</ul>
			</li>
		</ul>
		<div class="ads">
			<div class="wrapper">
				<a href="../Views/listarResidencias.php" class="btn-upgrade">Atualizar Página</a>
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
			<form id="searchForm" onsubmit="event.preventDefault(); filterUsers();" class="d-flex align-items-center">
				<div class="input-group" style="width: 300px; margin-top: 1.5rem; height: 38px;">
					<input type="search" id="searchInput" class="form-control border-end-0 h-100" style="margin-top: -0.5rem;" placeholder="Pesquisar Imoveis...">
					<button type="submit" class="btn btn-primary border-start-0 h-100 px-3 d-flex align-items-center justify-content-center" style="margin-top: -0.5rem;">
						<i class='bx bx-search icon'></i>
					</button>
				</div>
			</form>
			<a href="../Models/gerar_relatorio_residencia.php" target="_blank" class="btn btn btn-outline-light">
				<i class='bx bxs-file-pdf icon' ></i>
			</a>
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
			<div class="container my-5">

				<!--============Listar Residencia=========-->
				<div class="topbar">
					<h2 style="margin-top: 5rem;">Listagem das Residência</h2>
				</div>

				<div class="container">
					<div class="row mt-4">
						<div class="col-lg-12 d-flex justify-content-between align-items-center">
							<div class="">
								<!-- Button trigger modal -->
								<button type="button" style="margin-left: 119vh; margin-top: -5rem;" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#cadResidenciaModal">
									Cadastrar Residências
								</button>
							</div>
						</div>
					</div>
					<hr>
					<span id="msgAlerta"></span>
					<div class="row">
						<div class="col-lg-12">
							<span class="listar-residencias"></span>
						</div>
					</div>
				</div>

				<!-- Modal -->
				<div class="modal fade" id="cadResidenciaModal" tabindex="-1" aria-labelledby="cadResidenciaModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title fs-5" id="cadResidenciaModalLabel">Cadastrar Imóvel</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<form id="cad-residencia-form">
									<span id="msgAlertaErroCad"></span>
									<div class="row mb-3">
										<label for="typeResi" class="col-form-label">Tipo de Imóvel</label>
                                        <select name="typeResi" id="typeResi" class="form-select" aria-label="Default select example">
                                            <option value="">----- Tipo de Imóvel -----</option>
                                            <option value="Apartamento">Apartamento</option>
                                            <option value="Moradia">Moradia</option>
                                            <option value="Vivenda">Vivenda</option>
											<option value="Outro">Outro</option>
                                        </select>
									</div>
									<div class="mb-3">
										<label for="location" class="col-form-label">Localização</label>
										<input type="text" name="location" class="form-control" id="location" placeholder="Digite se Endereço" >
									</div>
                                    <div class="row mb-3">
										<label for="price" class="col-form-label">Valor Avaliado</label>
										<input type="number" name="price" class="form-control" id="price" step="0.01" min="15000" placeholder="Kz 0.00" >
									</div>
                                    <div class="row mb-3">
										<select name="status" id="status" class="form-select" aria-label="Default select example">
                                            <option value="">----- Estado da Residência ----</option>
                                            <option value="Venda">Venda</option>
                                            <option value="Arrendamento">Arrendamento</option>
                                        </select>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
										<input type="submit" class="btn btn-success" id="cad-residencia-btn" value="Cadastrar"/>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				<!--Modal Detalhes da Residencia-->
				<div class="modal fade" id="visResidenciaModal" tabindex="-1" aria-labelledby="visResidenciaModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title fs-5" id="visResidenciaModalLabel">Detalhes da Residência</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<span id="msgAlertaErroVis"></span>

								<dl class="row">
									<dt class="col-sm-3">ID</dt>
									<dd class="col-sm-9"><span id="idResidencia"></span></dd>

									<dt class="col-sm-3">Tipo de Imóvel</dt>
									<dd class="col-sm-9"><span id="typeResiResidencia"></span></dd>

									<dt class="col-sm-3">Tipologia do Imóvel</dt>
									<dd class="col-sm-9"><span id="typologyResidencia"></span></dd>

									<dt class="col-sm-3">Localização</dt>
									<dd class="col-sm-9"><span id="locationResidencia"></span></dd>

									<dt class="col-sm-3">Valor Avaliado</dt>
									<dd class="col-sm-9"><span id="priceResidencia"></span></dd>

									<dt class="col-sm-3">Status</dt>
									<dd class="col-sm-9"><span id="statusResidencia"></span></dd>

								</dl>
							</div>
						</div>
					</div>
				</div>
				<!--Modal Editar Residência-->
				<div class="modal fade" id="editResidenciaModal" tabindex="-1" aria-labelledby="editResidenciaModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title fs-5" id="editResidenciaModalLabel">Editar Imóvel</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<form id="edit-residencia-form">
									<span id="msgAlertaErroEdit"></span>

									<input type="hidden" name="id" id="editid" >
									
									<div class="row mb-3">
										<label for="typology" class="col-form-label">Tipo de Imóvel</label>
                                        <select name="typeResi" id="edittypeResi" class="form-select" aria-label="Default select example">
                                            <option value="">----- Tipo de Imóvel -----</option>
                                            <option value="Apartamento">Apartamento</option>
                                            <option value="Vivenda">Vivenda</option>
                                            <option value="Moradia">Maoradia</option>
                                            <option value="Outro">Outro</option>
                                        </select>
									</div>
									<div class="row mb-3">
										<label for="typology" class="col-form-label">Tipologia do Imóvel</label>
                                        <select name="typology" id="edittypology" class="form-select" aria-label="Default select example">
                                            <option value="">----- Tipologia do Imóvel -----</option>
                                            <option value="T2">T2</option>
                                            <option value="T3">T3</option>
                                            <option value="T4">T4</option>
                                            <option value="Outro">Outro</option>
                                        </select>
									</div>
									<div class="mb-3">
										<label for="location" class="col-form-label">Localização</label>
										<input type="text" name="location" class="form-control" id="editlocation" placeholder="Digite se Endereço" >
									</div>
                                    <div class="row mb-3">
										<label for="price" class="col-form-label">Valor Avaliado</label>
										<input type="number" name="price" class="form-control" id="editprice" step="0.01" min="1" placeholder="Kz 0.00" >
									</div>
                                    <div class="row mb-3">
										<select name="status" id="editstatus" class="form-select" aria-label="Default select example">
                                            <option value="">----- Estado do Imóvel ----</option>
                                            <option value="Venda">Venda</option>
                                            <option value="Arrendamento">Arrendamento</option>
                                        </select>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
										<input type="submit" class="btn btn-success" id="edit-residencia-btn" value="Editar"/>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

			</div>
			
		</main>
		<!-- MAIN -->
	</section>
	<!-- NAVBAR -->

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" 
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" 
    crossorigin="anonymous"></script>
	<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/js/my_chart.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
	<script src="../js/script.js"></script>
	<script src="../js/custom-resi.js"></script>
	<script src="../js/apartament-fields.js"></script>
	<script src="../js/vivenda-fields.js"></script>
</body>
</html>