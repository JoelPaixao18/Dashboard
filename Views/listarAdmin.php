<?php
	include_once '../Config/conection.php';

	session_start();

	// Verificação de acesso
	if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
		header("Location: ../Views/index.php");
		exit();
	}

	$stmt = $conn->query("SELECT COUNT(*) as count FROM admin_notifications WHERE read_status = 0");
	$unreadCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
?>

<!DOCTYPE html>
<html lang="pt-en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link rel="stylesheet" href="https://icons.getbootstrap.com/icons/trash3-fill/">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.14.1/themes/smoothness/jquery-ui.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://icons.getbootstrap.com/">
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link rel="stylesheet" href="../Views/CSS/style.css">
    <link rel="stylesheet" href="../Views/CSS/styles-dash.css">
	<link rel="shortcut icon" href="../Views/Dashboard-main/img/logo_resin.ico">
	<title>Listagem dos Administradores</title>
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
					<!--<li><a href="../Views/listagemGeral.php">Dados - Residência & Proprietário</a></li>-->
					<li><a href="../Views/listarPendingProperties.php">Listar Imóveis Pendentes</a></li>
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
				<a href="../Views/listarAdmin.php" class="btn-upgrade">Atualizar Página</a>
			</div>
		</div>
	</section>
	<!-- SIDEBAR -->

	<!-- NAVBAR -->
	<section id="content">
		<!-- NAVBAR -->
		<nav>
			<i class='bx bx-menu toggle-sidebar' ></i>
			<!-- Formulário de pesquisa atualizado -->
			<form id="searchForm" onsubmit="event.preventDefault(); filterAdmins();" class="d-flex align-items-center">
				<div class="input-group" style="width: 300px; margin-top: 1.5rem; height: 38px;">
					<input type="search" id="searchInput" class="form-control border-end-0 h-100" style="margin-top: -0.5rem;" placeholder="Pesquisar administradores...">
					<button type="submit" class="btn btn-primary border-start-0 h-100 px-3 d-flex align-items-center justify-content-center" style="margin-top: -0.5rem;">
						<i class='bx bx-search icon'></i>
					</button>
				</div>
			</form>
			<a href="../Views/admin_notifications.php" class="nav-link">
				<i class='bx bxs-bell icon'></i>
				<span class="badge"><?= $unreadCount ?></span>
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

				<!--============Listar Administradores=========-->
				<div class="topbar">
					<h2 style="margin-top: 5rem;">Listagem dos Administradores</h2>
				</div>

				<div class="container">
					<!-- Button trigger modal -->
					<!--<div class="row mt-4">
						<div class="col-lg-12 d-flex justify-content-between align-items-center">
							<div class="">
								
								<button type="button" style="margin-left: 123vh; margin-top: -5rem;" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#cadAdminModal">
									Cadastrar Administrador
								</button>
							</div>
						</div>
					</div>-->
					<br>
					<hr>
					<span id="msgAlerta"></span>
					<div class="row">
						<div class="col-lg-12">
							<span class="listar-admin"></span>
						</div>
					</div>
				</div>

				<!-- Modal Cadastro -->
				<div class="modal fade" id="cadAdminModal" tabindex="-1" aria-labelledby="cadAdminModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title fs-5" id="cadAdminModalLabel">Cadastrar Administrador</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<form id="cad-admin-form">
									<span id="msgAlertaErroCad"></span>
									<div class="row mb-3">
										<label for="nome" class="col-form-label">Nome</label>
										<input type="text" name="nome" class="form-control" id="nome" placeholder="Nome Completo">
									</div>
									<div class="mb-3">
										<label for="email" class="col-form-label">E-mail</label>
										<input type="email" name="email" class="form-control" id="email" placeholder="email@gmail.com">
									</div>
									<div class="mb-3">
										<label for="tel" class="col-form-label">Telefone</label>
										<input type="text" name="tel" class="form-control" id="tel" placeholder="+244 999 999 999">
									</div>
									<div class="mb-3">
										<label for="bi" class="col-form-label">Nº de BI</label>
										<input type="text" name="bi" class="form-control" id="bi" placeholder="Número do Bilhete de Identidade">
									</div>
									<div class="mb-3">
										<label for="senha" class="col-form-label">Senha</label>
										<input type="password" name="senha" class="form-control" id="senha" placeholder="* * * * * * *">
									</div> 
									<div class="modal-footer">
										<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
										<input type="submit" class="btn btn-success" id="cad-admin-btn" value="Cadastrar"/>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				<!-- Modal Edição - Atualizado -->
				<div class="modal fade" id="editAdminModal" tabindex="-1" aria-labelledby="editAdminModalLabel" aria-hidden="true">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header bg-warning text-white">
								<h5 class="modal-title fs-5" id="editAdminModalLabel">
									<i class="fas fa-user-edit me-2"></i>Editar Administrador
								</h5>
								<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<form id="edit-admin-form" class="needs-validation" novalidate>
									<span id="msgAlertaErroEdit"></span>
									
									<input type="hidden" name="id" id="editid">
									
									<div class="row g-3">
										<div class="col-md-6">
											<label for="editnome" class="form-label">Nome Completo</label>
											<div class="input-group has-validation">
												<span class="input-group-text"><i class="fas fa-user"></i></span>
												<input type="text" name="nome" class="form-control" id="editnome" 
													placeholder="Nome Completo" required
													pattern="^[\p{L}\s]+$" title="Apenas letras e espaços">
												<div class="invalid-feedback">
													Por favor, insira um nome válido (apenas letras e espaços).
												</div>
											</div>
										</div>
										
										<div class="col-md-6">
											<label for="editemail" class="form-label">E-mail</label>
											<div class="input-group has-validation">
												<span class="input-group-text"><i class="fas fa-envelope"></i></span>
												<input type="email" name="email" class="form-control" id="editemail" 
													placeholder="email@exemplo.com" required>
												<div class="invalid-feedback">
													Por favor, insira um e-mail válido.
												</div>
											</div>
										</div>
										
										<div class="col-md-6">
											<label for="edittel" class="form-label">Telefone</label>
											<div class="input-group has-validation">
												<span class="input-group-text"><i class="fas fa-phone"></i></span>
												<input type="text" name="tel" class="form-control" id="edittel" 
													placeholder="+244 999 999 999" required
													pattern="^\+?[0-9]{1,3}[ ]?[0-9]{3}[ ]?[0-9]{3}[ ]?[0-9]{3}$"
													title="Formato: +244 999 999 999 ou 999 999 999">
												<div class="invalid-feedback">
													Por favor, insira um número de telefone válido.
												</div>
											</div>
										</div>
										
										<div class="col-md-6">
											<label for="editbi" class="form-label">Nº de BI</label>
											<div class="input-group has-validation">
												<span class="input-group-text"><i class="fas fa-id-card"></i></span>
												<input type="text" name="bi" class="form-control" id="editbi" 
													placeholder="Número do Bilhete de Identidade" required
													pattern="^\d{9}[A-Z]{2}\d{3}$" title="Formato: 123456789LA123">
												<div class="invalid-feedback">
													Por favor, insira um número de BI válido (ex: 123456789LA123).
												</div>
											</div>
										</div>
									</div>
									
									<div class="modal-footer border-top-0">
										<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
											<i class="fas fa-times me-1"></i> Cancelar
										</button>
										<button type="submit" class="btn btn-warning text-white" id="edit-admin-btn">
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

	<!-- Scripts -->
	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
	<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
	<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
	<script src="../js/script.js"></script>
	<script src="../js/custom-admin.js"></script>
</body>
</html>