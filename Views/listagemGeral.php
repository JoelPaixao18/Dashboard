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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.14.1/themes/smoothness/jquery-ui.css">
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link rel="stylesheet" href="../Views/CSS/style.css">
    <link rel="stylesheet" href="../Views/CSS/styles-dash.css">
	<link rel="shortcut icon" href="../Views/Dashboard-main/img/logo_resin.ico">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://icons.getbootstrap.com/">
	<title>Dados - Residências & Proprietários</title>
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
					<li><a href="#" target="_blank">Gerar Relatório - Usuário</a></li>
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
					<li><a href="../Views/listagemGeral.php ">Dados - Residência & Proprietário</a></li>
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
				<a href="../Views/listagemGeral.php" class="btn-upgrade">Atualizar Página</a>
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
			<form action="#" method="POST">
				<div class="form-group">
					<input type="search" name="pesquisa" id="pesquisa" placeholder="Pesquisar Dados..." style="margin-top: 1.5rem;">
					<button type="submit" onclick="searchData()" name="sendPesq" value="Pesquisar">
						<i class='bx bx-search icon'></i>
					</button>
				</div>
			</form>
			<a href="#" target="_blank" class="btn btn btn-outline-light">
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

				<!--============Listar Usuarios=========-->
				<div class="topbar">
					<h2 style="margin-top: 5rem;">Dados - Residências & Proprietários</h2>
				</div>

				<div class="container">
					<div class="row mt-4">
						<div class="col-lg-12 d-flex justify-content-between align-items-center">
							<div class="">
								<!-- Button trigger modal -->
								<button type="button" style="margin-left: 118vh; margin-top: -5rem;" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#cadPropriedadeModal">
									Cadastrar Propriedade
								</button>
							</div>
						</div>
					</div>
					<hr>
					<span id="msgAlerta"></span>
					<div class="row">
						<div class="col-lg-12">
							<span class="listar-propriedade"></span>
						</div>
					</div>
				</div>

				<!-- Modal -->
				<div class="modal fade" id="cadPropriedadeModal" tabindex="-1" aria-labelledby="cadPropriedadeModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title fs-5" id="cadPropriedadeModalLabel">Cadastrar Propriedade</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<form id="cad-propriedade-form">
                                <span id="msgAlertaErroCad"></span>
									<h3>Dados do Proprietário</h3>
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

                                    <hr>
                                    
                                    <h3>Registrar Propriedade</h3>
                                    <div class="row mb-3">
                                        <select name="zonamento" id="zonamento" class="form-select" aria-label="Default select example">
                                            <option value="">----- Tipo de Residencia -----</option>
                                            <option value="Residencia">Apartamento</option>
                                            <option value="Residencia">Kitnet</option>
                                            <option value="Residencia">Flat</option>
                                            <option value="Residencia">Condomínio Residencial</option>
                                            <option value="Residencia">Coberturas</option>
                                                <option value="Residencia">Casas de Condomínio fechado</option>
                                            <option value="Residencia">Apartamento padrão</option>
                                            <option value="Residencia">Eco-Condomínio</option>
                                            <option value="Residencia">Casa autoconstruída</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="localizaco" class="col-form-label">Localização</label>
                                        <input type="text" name="localizacao" class="form-control" id="localizacao" placeholder="Digite se Endereço" >
                                    </div>
                                    <div class="row mb-3">
                                        <label for="preco" class="col-form-label">Valor Avaliado</label>
                                        <input type="number" name="preco" class="form-control" id="preco" step="0.01" min="0" placeholder="Kz 0.00" >
                                    </div>
                                    <div class="row mb-3">
                                        <select name="status" id="status" class="form-select" aria-label="Default select example">
                                            <option value="">----- Estado da Residência ----</option>
                                            <option value="venda">Venda</option>
                                            <option value="renda">Renda</option>
                                        </select>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="descricao" class="col-form-label">Descrição</label>
                                        <input type="text" name="descricao" class="form-control" id="descricao" placeholder="Digite a descrição de Residência" >
                                    </div>
									<div class="modal-footer">
										<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
										<input type="submit" class="btn btn-success" id="cad-propriedade-btn" value="Cadastrar"/>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				<!--Modal Detalhes do Usuario-->
				<div class="modal fade" id="visUsuarioModal" tabindex="-1" aria-labelledby="visUsuarioModalLabel" aria-hidden="true">
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

									<dt class="col-sm-3">Role</dt>
									<dd class="col-sm-9"><span id="roleUsuario"></span></dd>
								</dl>
							</div>
						</div>
					</div>
				</div>

				<!--Modal Editar Usuario-->
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

									<input type="hidden" name="id" id="editid" >
									
									<div class="row mb-3">
										<label for="nome" class="col-form-label">Nome</label>
										<input type="text" name="nome" class="form-control" id="editnome" placeholder="Nome Completo" >
									</div>
									<div class="mb-3">
										<label for="email" class="col-form-label">E-mail</label>
										<input type="email" name="email" class="form-control" id="editemail" placeholder="email@gmail.com" >
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
	<script src="../js/custom-geral.js"></script>
</body>
</html>