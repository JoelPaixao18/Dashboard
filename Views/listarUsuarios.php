<?php
	include_once '../Config/conection.php';

	session_start();

	// Modifique a verificação para não interromper o carregamento dos dados
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
	<title>Listagem dos Usuários</title>
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
				<a href="../Views/listarUsuarios.php" class="btn-upgrade">Atualizar Página</a>
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
			<!-- Formulário de pesquisa atualizado -->
			<form id="searchForm" onsubmit="event.preventDefault(); filterUsers();" class="d-flex align-items-center">
				<div class="input-group" style="width: 300px; margin-top: 1.5rem; height: 38px;">
					<input type="search" id="searchInput" class="form-control border-end-0 h-100" style="margin-top: -0.5rem;" placeholder="Pesquisar usuários...">
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

				<!--============Listar Usuarios=========-->
				<div class="topbar">
					<h2 style="margin-top: 5rem;">Listagem dos Usuários</h2>
				</div>

				<div class="container">
					<div class="row mt-4">
						<div class="col-lg-12 d-flex justify-content-between align-items-center">
							<div class="">
								<!-- Button trigger modal -->
								<button type="button" style="margin-left: 123vh; margin-top: -5rem;" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#cadUsuarioModal">
									Cadastrar Usuário
								</button>
							</div>
						</div>
					</div>
					<hr>
					<span id="msgAlerta"></span>
					<div class="row">
						<div class="col-lg-12">
							<span class="listar-usuarios"></span>
						</div>
					</div>
				</div>

				<!-- Modal Cadastro -->
				<div class="modal fade" id="cadUsuarioModal" tabindex="-1" aria-labelledby="cadUsuarioModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title fs-5" id="cadUsuarioModalLabel">Cadastrar Usuário</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<form id="cad-usuario-form">
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
										<input type="submit" class="btn btn-success" id="cad-usuario-btn" value="Cadastrar"/>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				<!-- Modal Visualização -->
				<!--<div class="modal fade" id="visUsuarioModal" tabindex="-1" aria-labelledby="visUsuarioModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title fs-5" id="visUsuarioModalLabel">Detalhes do Usuário</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<span id="msgAlertaErroVis"></span>

								<dl class="row">
									<dt class="col-sm-3">ID</dt>
									<dd class="col-sm-9"><span id="idUsuario"></span></dd>

									<dt class="col-sm-3">Nome</dt>
									<dd class="col-sm-9"><span id="nomeUsuario"></span></dd>

									<dt class="col-sm-3">E-mail</dt>
									<dd class="col-sm-9"><span id="emailUsuario"></span></dd>

									<dt class="col-sm-3">Telefone</dt>
									<dd class="col-sm-9"><span id="telUsuario"></span></dd>

									<dt class="col-sm-3">Nº de BI</dt>
									<dd class="col-sm-9"><span id="biUsuario"></span></dd>

									<dt class="col-sm-3">Perfil</dt>
									<dd class="col-sm-9"><span id="roleUsuario"></span></dd>
								</dl>
							</div>
						</div>
					</div>
				</div>-->

				<!-- Modal Edição -->
				<div class="modal fade" id="editUsuarioModal" tabindex="-1" aria-labelledby="editUsuarioModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title fs-5" id="editUsuarioModalLabel">Editar Usuário</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<form id="edit-usuario-form">
									<span id="msgAlertaErroEdit"></span>

									<input type="hidden" name="id" id="editid">
									
									<div class="row mb-3">
										<label for="nome" class="col-form-label">Nome</label>
										<input type="text" name="nome" class="form-control" id="editnome" placeholder="Nome Completo">
									</div>
									<div class="mb-3">
										<label for="email" class="col-form-label">E-mail</label>
										<input type="email" name="email" class="form-control" id="editemail" placeholder="email@gmail.com">
									</div>
									<div class="mb-3">
										<label for="edittel" class="col-form-label">Telefone</label>
										<input type="text" name="tel" class="form-control" id="edittel" placeholder="+244 999 999 999">
									</div>
									<div class="mb-3">
										<label for="editbi" class="col-form-label">Nº de BI</label>
										<input type="text" name="bi" class="form-control" id="editbi" placeholder="Número do Bilhete de Identidade">
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
										<input type="submit" class="btn btn-warning" id="edit-usuario-btn" value="Salvar"/>
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

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.14.1/jquery-ui.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" 
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" 
    crossorigin="anonymous"></script>
	<script src="https://icons.getbootstrap.com/"></script>
	<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
	<script src="../js/script.js"></script>
	<script src="../js/custom.js"></script>
	<script src="../js/filtragem.js"></script>
</body>
</html>