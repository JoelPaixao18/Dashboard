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
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
	<link rel="stylesheet" href="https://getbootstrap.com/docs/5.3/forms/checks-radios/#radios">
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

				<!-- Modal de Cadastro -->
				<div class="modal fade" id="cadResidenciaModal" tabindex="-1" aria-labelledby="cadResidenciaModalLabel" aria-hidden="true">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header bg-primary text-white">
								<h5 class="modal-title fs-5">
									<i class="fas fa-home me-2"></i>Cadastrar Novo Imóvel
								</h5>
								<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<form id="cad-residencia-form" enctype="multipart/form-data">
									<span id="msgAlertaErroCad"></span>
									
									<!-- Seção de Imagens -->
									<div class="card mb-4">
										<div class="card-header bg-light">
											<h6 class="mb-0">
												<i class="fas fa-images me-2"></i>Imagens do Imóvel
											</h6>
										</div>
										<div class="card-body">
											<div class="row" id="cad-images-container">
												<div class="col-12 text-muted">Nenhuma imagem selecionada</div>
											</div>
											<div class="mt-3">
												<button type="button" class="btn btn-outline-primary" id="add-images-btn">
													<i class="fas fa-plus me-1"></i> Adicionar Imagens
												</button>
												<input type="file" id="image-upload" multiple accept="image/*" style="display: none;">
												<small class="text-muted d-block mt-1">Máximo 5 imagens (JPEG, PNG, até 2MB cada)</small>
											</div>
										</div>
									</div>
									
									<div class="row">
										<!-- Coluna de Informações Básicas -->
										<div class="col-md-6">
											<div class="card mb-4">
												<div class="card-header bg-light">
													<h6 class="mb-0">
														<i class="fas fa-info-circle me-2"></i>Informações Básicas
													</h6>
												</div>
												<div class="card-body">
													<div class="mb-3">
														<label for="typeResi" class="form-label required">Tipo de Imóvel</label>
														<select name="typeResi" id="typeResi" class="form-select" required>
															<option value="">Selecione o tipo</option>
															<option value="Apartamento">Apartamento</option>
															<option value="Vivenda">Vivenda</option>
															<option value="Moradia">Moradia</option>
														</select>
													</div>
													
													<div class="mb-3">
														<label for="typology" class="form-label required">Tipologia</label>
														<div id="typology-options" class="btn-group" role="group">
															<input type="radio" class="btn-check" name="typology" id="typologyT1" value="T1" required>
															<label class="btn btn-outline-primary" for="typologyT1">T1</label>
															
															<input type="radio" class="btn-check" name="typology" id="typologyT2" value="T2">
															<label class="btn btn-outline-primary" for="typologyT2">T2</label>
															
															<input type="radio" class="btn-check" name="typology" id="typologyT3" value="T3">
															<label class="btn btn-outline-primary" for="typologyT3">T3</label>
															
															<input type="radio" class="btn-check" name="typology" id="typologyT4" value="T4">
															<label class="btn btn-outline-primary" for="typologyT4">T4</label>

															<input type="radio" class="btn-check" name="typology" id="typologyT5" value="T5">
															<label class="btn btn-outline-primary" for="typologyT5">T5</label>

															<input type="radio" class="btn-check" name="typology" id="typologyT6" value="T6">
															<label class="btn btn-outline-primary" for="typologyT6">T6</label>
															
															<input type="radio" class="btn-check" name="typology" id="typologyOutro" value="Outro">
															<label class="btn btn-outline-primary" for="typologyOutro">Outro</label>
														</div>
													</div>
													
													<div class="mb-3">
														<label for="location" class="form-label required">Localização</label>
														<input type="text" name="location" id="location" class="form-control" required>
														<div id="location-suggestions" class="list-group mt-2 d-none"></div>
													</div>
													
													<div class="mb-3">
														<label for="houseSize" class="form-label required">Área Construída (m²)</label>
														<input type="number" name="houseSize" id="houseSize" class="form-control" step="0.1" min="0" required>
													</div>
												</div>
											</div>
										</div>

										<!-- Coluna de Valores e Status -->
										<div class="col-md-6">
											<div class="card mb-4">
												<div class="card-header bg-light">
													<h6 class="mb-0">
														<i class="fas fa-tag me-2"></i>Valor e Status
													</h6>
												</div>
												<div class="card-body">
													<div class="mb-3">
														<label for="status" class="form-label required">Status</label>
														<select name="status" id="status" class="form-select" required>
															<option value="">Selecione o status</option>
															<option value="Venda">Venda</option>
															<option value="Arrendamento">Arrendamento</option>
														</select>
													</div>
													
													<div class="mb-3">
														<label for="price" class="form-label required">Valor (Kz)</label>
														<div class="input-group">
															<span class="input-group-text">Kz</span>
															<input type="number" name="price" id="price" class="form-control" step="0.01" min="0" required>
														</div>
													</div>
													
													<!-- Campos dinâmicos baseados no tipo de imóvel -->
													<div id="dynamic-fields">
														<!-- Será preenchido via JavaScript -->
													</div>
												</div>
											</div>
										</div>
									</div>

									<!-- Seção de Características -->
									<div class="card mb-4">
										<div class="card-header bg-light">
											<h6 class="mb-0">
												<i class="fas fa-home me-2"></i>Características
											</h6>
										</div>
										<div class="card-body">
											<div class="row" id="features-container">
												<!-- Será preenchido via JavaScript -->
											</div>
										</div>
									</div>

									<!-- Seção de Infraestrutura -->
									<div class="card mb-4">
										<div class="card-header bg-light">
											<h6 class="mb-0">
												<i class="fas fa-bolt me-2"></i>Infraestrutura
											</h6>
										</div>
										<div class="card-body">
											<div class="row">
												<div class="col-md-3">
													<div class="form-check form-switch mb-3">
														<input class="form-check-input" type="checkbox" name="quintal" id="quintal" value="1">
														<label class="form-check-label" for="quintal">Quintal/Jardim</label>
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-check form-switch mb-3">
														<input class="form-check-input" type="checkbox" name="garagem" id="garagem" value="1">
														<label class="form-check-label" for="garagem">Garagem</label>
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-check form-switch mb-3">
														<input class="form-check-input" type="checkbox" name="hasWater" id="hasWater" value="1">
														<label class="form-check-label" for="hasWater">Água</label>
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-check form-switch mb-3">
														<input class="form-check-input" type="checkbox" name="hasElectricity" id="hasElectricity" value="1">
														<label class="form-check-label" for="hasElectricity">Energia</label>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="modal-footer">
										<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
											<i class="fas fa-times me-1"></i> Cancelar
										</button>
										<button type="submit" class="btn btn-primary" id="cad-residencia-btn">
											<i class="fas fa-save me-1"></i> Cadastrar Imóvel
										</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				<!-- Modal Detalhes da Residencia -->
				<div class="modal fade" id="visResidenciaModal" tabindex="-1" aria-labelledby="visResidenciaModalLabel" aria-hidden="true">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header bg-primary text-white">
								<h5 class="modal-title fs-5" id="visResidenciaModalLabel">
									<i class="fas fa-home me-2"></i>Detalhes Completo do Imóvel
								</h5>
								<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<span id="msgAlertaErroVis"></span>

								<!-- Adicionando a seção de imagens -->
								<div class="card mb-4" style="justify-content: center;">
									<div class="card-header bg-light">
										<h6 class="mb-0">
											<i class="fas fa-images me-2"></i>Imagens do Imóvel
										</h6>
									</div>
									<div class="card-body">
										<div class="row" id="vis-images-container">
											<!-- As imagens serão carregadas aqui via JavaScript -->
											<div class="col-12 text-muted">Carregando imagens...</div>
										</div>
									</div>
								</div>
								
								<div class="row">
									<!-- Coluna de Informações Básicas -->
									<div class="col-md-6">
										<div class="card mb-4">
											<div class="card-header bg-light">
												<h6 class="mb-0">
													<i class="fas fa-info-circle me-2"></i>Informações Básicas
												</h6>
											</div>
											<div class="card-body">
												<dl class="row">
													<dt class="col-sm-5">ID do Imóvel</dt>
													<dd class="col-sm-7"><span id="idResidencia" class="fw-bold text-primary"></span></dd>

													<dt class="col-sm-5">Tipo de Imóvel</dt>
													<dd class="col-sm-7"><span id="typeResiResidencia"></span></dd>

													<dt class="col-sm-5">Tipologia</dt>
													<dd class="col-sm-7"><span id="typologyResidencia"></span></dd>

													<dt class="col-sm-5">Localização</dt>
													<dd class="col-sm-7"><span id="locationResidencia"></span></dd>

													<dt class="col-sm-5">Valor</dt>
													<dd class="col-sm-7"><span id="priceResidencia" class="fw-bold"></span></dd>

													<dt class="col-sm-5">Status</dt>
													<dd class="col-sm-7"><span id="statusResidencia" class="badge"></span></dd>
												</dl>
											</div>
										</div>
									</div>

									<!-- Coluna de Características -->
									<div class="col-md-6">
										<div class="card mb-4">
											<div class="card-header bg-light">
												<h6 class="mb-0">
													<i class="fas fa-ruler-combined me-2"></i>Características
												</h6>
											</div>
											<div class="card-body">
												<dl class="row">
													<dt class="col-sm-5">Área Construída</dt>
													<dd class="col-sm-7"><span id="houseSizeResidencia"></span></dd>

													<dt class="col-sm-5">Salas de Estar</dt>
													<dd class="col-sm-7"><span id="livingRoomCountResidencia"></span></dd>

													<dt class="col-sm-5">Banheiros</dt>
													<dd class="col-sm-7"><span id="bathroomCountResidencia"></span></dd>

													<dt class="col-sm-5">Cozinhas</dt>
													<dd class="col-sm-7"><span id="kitchenCountResidencia"></span></dd>

													<dt class="col-sm-5">Quintal/Jardim</dt>
													<dd class="col-sm-7"><span id="quintalResidencia"></span></dd>

													<dt class="col-sm-5">Número de Andares</dt>
													<dd class="col-sm-7"><span id="andaresResidencia"></span></dd>
												</dl>
											</div>
										</div>
									</div>
								</div>

								<!-- Seção de Infraestrutura -->
								<div class="card mb-4">
									<div class="card-header bg-light">
										<h6 class="mb-0">
											<i class="fas fa-bolt me-2"></i>Infraestrutura
										</h6>
									</div>
									<div class="card-body">
										<div class="row">
											<div class="col-md-4">
												<div class="d-flex align-items-center mb-3">
													<i class="fas fa-car me-2 text-muted"></i>
													<span>Garagem: </span>
													<span id="garagemResidencia" class="ms-2 fw-bold"></span>
												</div>
											</div>
											<div class="col-md-4">
												<div class="d-flex align-items-center mb-3">
													<i class="fas fa-tint me-2 text-muted"></i>
													<span>Água: </span>
													<span id="hasWaterResidencia" class="ms-2 fw-bold"></span>
												</div>
											</div>
											<div class="col-md-4">
												<div class="d-flex align-items-center mb-3">
													<i class="fas fa-lightbulb me-2 text-muted"></i>
													<span>Energia: </span>
													<span id="hasElectricityResidencia" class="ms-2 fw-bold"></span>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="card mb-4">
									<div class="card-header bg-light">
										<h6 class="mb-0">
											<i class="fas fa-bolt me-2"></i>Descrição
										</h6>
									</div>
									<div class="row">
										<span id="descriptionResidencia" class="ms-2 fw-bold"></span>
									</div>
								</div>

								<!-- Seção de Mapa (opcional) -->
								<!--<div class="card">
									<div class="card-header bg-light">
										<h6 class="mb-0">
											<i class="fas fa-map-marked-alt me-2"></i>Localização
										</h6>
									</div>
									<div class="card-body p-0" style="height: 200px;">
										<div id="mapPreview" class="h-100 bg-light d-flex align-items-center justify-content-center">
											<p class="text-muted mb-0">Mapa da localização será exibido aqui</p>
										</div>
									</div>
								</div>-->
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
									<i class="fas fa-times me-1"></i> Fechar
								</button>
								<button type="button" class="btn btn-primary" id="printResidenciaBtn">
									<i class="fas fa-print me-1"></i> Imprimir
								</button>
							</div>
						</div>
					</div>
				</div>


				
				<!-- Modal Editar Residência -->
			<div class="modal fade" id="editResidenciaModal" tabindex="-1" aria-labelledby="editResidenciaModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-xl">
					<div class="modal-content">
						<div class="modal-header bg-primary text-white">
							<h5 class="modal-title fs-5">
								<i class="fas fa-edit me-2"></i>Editar Imóvel
							</h5>
							<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<form id="edit-residencia-form">
								<span id="msgAlertaErroEdit"></span>
								<input type="hidden" name="id" id="editid">
								
								<!-- Seção de Imagens -->
								<div class="card mb-4">
									<div class="card-header bg-light">
										<h6 class="mb-0">
											<i class="fas fa-images me-2"></i>Imagens do Imóvel
										</h6>
									</div>
									<div class="card-body">
										<div class="row" id="edit-images-container">
											<!-- As imagens serão carregadas aqui via JavaScript -->
										</div>
										<div class="mt-3">
											<button type="button" class="btn btn-outline-primary" id="edit-add-images-btn">
												<i class="fas fa-plus me-1"></i> Adicionar Imagens
											</button>
											<input type="file" id="edit-image-upload" multiple accept="image/*" style="display: none;">
										</div>
									</div>
								</div>
								
								<div class="row">
									<!-- Coluna de Informações Básicas -->
									<div class="col-md-6">
										<div class="card mb-4">
											<div class="card-header bg-light">
												<h6 class="mb-0">
													<i class="fas fa-info-circle me-2"></i>Informações Básicas
												</h6>
											</div>
											<div class="card-body">
												<div class="mb-3">
													<label for="edittypeResi" class="form-label required">Tipo de Imóvel</label>
													<select name="typeResi" id="edittypeResi" class="form-select" required>
														<option value="">Selecione o tipo</option>
														<option value="Apartamento">Apartamento</option>
														<option value="Moradia">Moradia</option>
														<option value="Vivenda">Vivenda</option>
														<option value="Outro">Outro</option>
													</select>
												</div>
												
												<div class="mb-3">
													<label for="edittypology" class="form-label required">Tipologia</label>
													<div id="edit-typology-options" class="btn-group" role="group">
														<!-- Opções serão carregadas via JavaScript -->
													</div>
												</div>
												
												<div class="mb-3">
													<label for="editlocation" class="form-label required">Localização</label>
													<input type="text" name="location" id="editlocation" class="form-control" required>
													<div id="edit-location-suggestions" class="list-group mt-2 d-none"></div>
												</div>
												
												<div class="mb-3">
													<label for="edithouseSize" class="form-label required">Área Construída (m²)</label>
													<input type="number" name="houseSize" id="edithouseSize" class="form-control" step="0.1" min="0" required>
												</div>
											</div>
										</div>
									</div>

									<!-- Coluna de Valores e Status -->
									<div class="col-md-6">
										<div class="card mb-4">
											<div class="card-header bg-light">
												<h6 class="mb-0">
													<i class="fas fa-tag me-2"></i>Valor e Status
												</h6>
											</div>
											<div class="card-body">
												<div class="mb-3">
													<label for="editstatus" class="form-label required">Status</label>
													<select name="status" id="editstatus" class="form-select" required>
														<option value="">Selecione o status</option>
														<option value="Venda">Venda</option>
														<option value="Arrendamento">Arrendamento</option>
														<option value="Indisponível">Indisponível</option>
													</select>
												</div>
												
												<div class="mb-3">
													<label for="editprice" class="form-label required">Valor (Kz)</label>
													<div class="input-group">
														<span class="input-group-text">Kz</span>
														<input type="number" name="price" id="editprice" class="form-control" step="0.01" min="0" required>
													</div>
												</div>
												
												<!-- Campos dinâmicos baseados no tipo de imóvel -->
												<div id="edit-dynamic-fields">
													<!-- Será preenchido via JavaScript -->
												</div>
											</div>
										</div>
									</div>
								</div>

								<!-- Seção de Características -->
								<div class="card mb-4">
									<div class="card-header bg-light">
										<h6 class="mb-0">
											<i class="fas fa-home me-2"></i>Características
										</h6>
									</div>
									<div class="card-body">
										<div class="row" id="edit-features-container">
											<!-- Será preenchido via JavaScript -->
										</div>
									</div>
								</div>

								<!-- Seção de Infraestrutura -->
								<div class="card mb-4">
									<div class="card-header bg-light">
										<h6 class="mb-0">
											<i class="fas fa-bolt me-2"></i>Infraestrutura
										</h6>
									</div>
									<div class="card-body">
										<div class="row">
											<div class="col-md-3">
												<div class="form-check form-switch mb-3">
													<input class="form-check-input" type="checkbox" name="quintal" id="editquintal" value="1">
													<label class="form-check-label" for="editquintal">Quintal/Jardim</label>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-check form-switch mb-3">
													<input class="form-check-input" type="checkbox" name="garagem" id="editgaragem" value="1">
													<label class="form-check-label" for="editgaragem">Garagem</label>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-check form-switch mb-3">
													<input class="form-check-input" type="checkbox" name="hasWater" id="edithasWater" value="1">
													<label class="form-check-label" for="edithasWater">Água</label>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-check form-switch mb-3">
													<input class="form-check-input" type="checkbox" name="hasElectricity" id="edithasElectricity" value="1">
													<label class="form-check-label" for="edithasElectricity">Energia</label>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
										<i class="fas fa-times me-1"></i> Cancelar
									</button>
									<button type="submit" class="btn btn-primary" id="edit-residencia-btn">
										<i class="fas fa-save me-1"></i> Salvar Alterações
									</button>
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
	<!--<script src="../js/apartament-fields.js"></script>
	<script src="../js/vivenda-fields.js"></script>-->
</body>
</html>