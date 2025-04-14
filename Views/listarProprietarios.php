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
	<title>Listagem dos Proprietários</title>
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
					<li><a href="../Models/gerar_relatorio_proprietario.php" target="_blank">Gerar Relatório - Proprietário</a></li>
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
					<li><a href="../Models/logout.php"> Sair </a></li>
				</ul>
			</li>
		</ul>
		<div class="ads">
			<div class="wrapper">
				<a href="../Views/listarProprietarios.php" class="btn-upgrade">Atualizar Página</a>
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
					<input type="text" placeholder="Pesquisar Proprietários...">
					<i class='bx bx-search icon' ></i>
				</div>
			</form>
			<a href="../Models/gerar_relatorio_proprietario.php" target="_blank" class="btn btn btn-outline-light">
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
			<div class="container my-5">

				<!--============Listar Usuarios=========-->
				<div class="topbar">
					<h2 style="margin-top: 5rem;">Listagem dos Proprietários</h2>
				</div>

				<div class="container">
					<div class="row mt-4">
						<div class="col-lg-12 d-flex justify-content-between align-items-center">
							<div class="">
								<!-- Button trigger modal -->
								<button type="button" style="margin-left: 125vh; margin-top: -5rem;" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#cadProprietarioModal">
									Cadastrar Proprietários
								</button>
							</div>
						</div>
					</div>
					<hr>
					<span id="msgAlerta"></span>
					<div class="row">
						<div class="col-lg-12">
							<span class="listar-proprietarios"></span>
						</div>
					</div>
				</div>

				<!-- Modal -->
				<div class="modal fade" id="cadProprietarioModal" tabindex="-1" aria-labelledby="cadProprietarioModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title fs-5" id="cadProprietarioModalLabel">Cadastrar Proprietário</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<form id="cad-proprietario-form">
									<span id="msgAlertaErroCad"></span>
									<div class="row mb-3">
										<label for="nome" class="col-form-label">Nome</label>
										<input type="text" name="nome" class="form-control" id="nome" placeholder="Nome Completo" >
									</div>
									<div class="mb-3">
										<label for="email" class="col-form-label">E-mail</label>
										<input type="email" name="email" class="form-control" id="email" placeholder="email@gmail.com" >
									</div>
                                    <div class="row mb-3">
										<label for="tel" class="col-form-label">Contato</label>
										<input type="number" name="tel" class="form-control" id="tel" placeholder="(+244) 9xx xxx xxx" >
									</div>
                                    <div class="row mb-3">
										<label for="BI" class="col-form-label">Bilhete de Identidade</label>
										<input type="text" name="BI" class="form-control" id="BI" placeholder="Nº do BI" >
									</div>
                                    <div class="row mb-3">
										<label for="endereco" class="col-form-label">Endereço</label>
										<input type="text" name="endereco" class="form-control" id="endereco" placeholder="Digite seu Endereço" >
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
										<input type="submit" class="btn btn-success" id="cad-proprietario-btn" value="Cadastrar"/>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				<!--Modal Detalhes do Proprietario-->
				<div class="modal fade" id="visProprietarioModal" tabindex="-1" aria-labelledby="visProprietarioModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title fs-5" id="visProprietarioModalLabel">Detalhes do Proprietário</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<span id="msgAlertaErroVis"></span>

								<dl class="row">
									<dt class="col-sm-3">ID</dt>
									<dd class="col-sm-9"><span id="idProprietario"></span></dd>

									<dt class="col-sm-3">Nome</dt>
									<dd class="col-sm-9"><span id="nomeProprietario"></span></dd>

									<dt class="col-sm-3">E-mail</dt>
									<dd class="col-sm-9"><span id="emailProprietario"></span></dd>

									<dt class="col-sm-3">Contato</dt>
									<dd class="col-sm-9"><span id="contatoProprietario"></span></dd>

									<dt class="col-sm-3">Bilhete de Identidade</dt>
									<dd class="col-sm-9"><span id="biProprietario"></span></dd>

									<dt class="col-sm-3">Endereço</dt>
									<dd class="col-sm-9"><span id="enderecoProprietario"></span></dd>
								</dl>
							</div>
						</div>
					</div>
				</div>

				<!--Modal Editar Usuario-->
				<div class="modal fade" id="editProprietarioModal" tabindex="-1" aria-labelledby="editProprietarioModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title fs-5" id="editProprietarioModalLabel">Editar Proprietário</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<form id="edit-proprietario-form">
									<span id="msgAlertaErroEdit"></span>

									<input type="hidden" name="id" id="editid" >
									
									<div class="row mb-3">
										<label for="nome" class="col-form-label">Nome</label>
										<input type="text" name="nome" class="form-control" id="editnome" placeholder="Nome Completo" >
									</div>
									<div class="mb-3">
										<label for="email" class="col-form-label">E-mail</label>
										<input type="email" name="email" class="form-control" id="editemail" placeholder="email@gmail.com" >
									</div>
									<div class="row mb-3">
										<label for="tel" class="col-form-label">Contato</label>
										<input type="number" name="tel" class="form-control" id="edittel" placeholder="(+244) 9xx xxx xxx" >
									</div>
									<div class="row mb-3">
										<label for="BI" class="col-form-label">Bilhete de Identidade</label>
										<input type="text" name="BI" class="form-control" id="editbi" placeholder="Nº do Bilhete de Identidade" >
									</div>
									<div class="row mb-3">
										<label for="endereco" class="col-form-label">Endereço</label>
										<input type="text" name="endereco" class="form-control" id="editendereco" placeholder="Digite seu Endereço" >
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
										<input type="submit" class="btn btn-warning" id="edit-proprietario-btn" value="Salvar"/>
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
	<script src="../js/script.js"></script>
	<script src="../js/custon-proprie.js"></script>
</body>
</html>