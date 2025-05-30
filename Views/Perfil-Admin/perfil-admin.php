<?php

	include_once '/Users/HP/PAP/htdocs/RESINGOLA-main/AGVRR/Config/conection.php';

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
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.14.1/themes/smoothness/jquery-ui.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://icons.getbootstrap.com/">
	<link rel="stylesheet" href="../CSS/style.css">
	<link rel="stylesheet" href="../CSS/styles-dash.css">
	<link rel="shortcut icon" href="../Dashboard-main/img/logo_resin.ico">
	<link rel="stylesheet" href="../CSS/style-perfil.css">
	<title>Painel Administrativo</title>
</head>
<body>
	
	<!-- SIDEBAR -->
	<section id="sidebar">
		<img src="../Dashboard-main/img/logo_resi.png" alt="Logo">
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
			<li><a href="/./../RESINGOLA-main/AGVRR/Views/dash.php"><i class='bx bxs-chart icon' ></i> Graficos</a></li>
			<li><a href="/./../RESINGOLA-main/AGVRR/Views/map/map.php"><i class='bx bxs-widget icon' ></i> Mapa </a></li>
			<li class="divider" data-text="table">Tabelas</li>
			<li>
				<a href="#"><i class='bx bxs-notepad icon' ></i> Listagens <i class='bx bx-chevron-right icon-right' ></i></a>
				<ul class="side-dropdown">
					<li><a href="/./../RESINGOLA-main/AGVRR/Views/listarUsuarios.php">Listar Usuários</a></li>
					<li><a href="/./../RESINGOLA-main/AGVRR/Views/listarAdmin.php">Listar Administradores</a></li>
					<li><a href="/./../RESINGOLA-main/AGVRR/Views/listarResidencias.php">Listar Residências</a></li>
					<!--<li><a href="/./../AGVRR/Views/listagemGeral.php">Dados - Residência & Proprietário</a></li>-->
					<li><a href="/./../RESINGOLA-main/AGVRR/Views/listarPendingProperties.php">Listar Imóveis Pendentes</a></li>
				</ul>
			</li>
			<li class="divider" data-text="profile">Perfil</li>
			<li>
				<a href="#"><i class="bi bi-person-fill icon"></i> Perfil <i class='bx bx-chevron-right icon-right' ></i></a>
				<ul class="side-dropdown">
					<li><a href="#" class="side-dropdown"><i class="bi bi-key icon"></i> Conta </a></li>
					<li><a href="#"><i class="bi bi-question-circle icon"></i> Ajuda </a></li>
					<li><a href="#"><i class="bi bi-gear icon"></i> Configurações </a></li>
					<li><a href="/./../AGVRR/Models/logout.php"> Terminar Sessão </a></li>
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
			<div class="container">
				<h1 class="header-title">Painel do Administrador</h1>
				
				<div class="admin-panel">
					<!-- Seção de Perfil do Admin -->
					<div class="profile-section">
						<div class="profile-header">
							<h2><i class="bi bi-person-badge"></i> Meu Perfil</h2>
						</div>
						
						<div class="profile-content">
							<?php
							try {
								// Verificar se o user_id está definido na sessão
								if (!isset($_SESSION['user_id'])) {
									throw new Exception("ID do administrador não encontrado na sessão.");
								}
								
								$adminId = $_SESSION['user_id'];
								
								// Consulta usando PDO
								$sql = "SELECT nome, email, tel, BI FROM administrador WHERE id = :user_id";
								$stmt = $conn->prepare($sql);
								$stmt->bindParam(':user_id', $adminId, PDO::PARAM_INT);
								$stmt->execute();
								
								if ($stmt->rowCount() > 0) {
									$admin = $stmt->fetch(PDO::FETCH_ASSOC);
									?>
									<div class="profile-info">
										<div class="profile-avatar">
											<div class="avatar-initials">
												<?php 
												$partes = explode(' ', $admin['nome']);
												$iniciais = strtoupper(substr($partes[0], 0, 1));
												if (count($partes) > 1) {
													$iniciais .= strtoupper(substr(end($partes), 0, 1));
												}
												echo $iniciais;
												?>
											</div>
										</div>
										
										<div class="profile-details">
											<div class="detail-row">
												<span class="detail-label">Nome:</span>
												<span class="detail-value"><?= htmlspecialchars($admin['nome']) ?></span>
											</div>
											<div class="detail-row">
												<span class="detail-label">Email:</span>
												<span class="detail-value"><?= htmlspecialchars($admin['email']) ?></span>
											</div>
											<div class="detail-row">
												<span class="detail-label">Telefone:</span>
												<span class="detail-value"><?= htmlspecialchars($admin['tel'] ?? 'Não informado') ?></span>
											</div>
											<div class="detail-row">
												<span class="detail-label">Nº do BI:</span>
												<span class="detail-value"><?= htmlspecialchars($admin['BI'] ?? 'Não informado') ?></span>
											</div>
										</div>
									</div>
									
									<div class="profile-actions">
										<button class="btn btn-edit"><i class="bi bi-pencil-square"></i> Editar Perfil</button>
										<button class="btn btn-change-password"><i class="bi bi-key"></i> Alterar Senha</button>
									</div>
									<?php
								} else {
									echo '<div class="alert alert-warning">Nenhum administrador encontrado com este ID.</div>';
								}
							} catch (PDOException $e) {
								echo '<div class="alert alert-danger">Erro no banco de dados: ' . htmlspecialchars($e->getMessage()) . '</div>';
							} catch (Exception $e) {
								echo '<div class="alert alert-danger">Erro: ' . htmlspecialchars($e->getMessage()) . '</div>';
							}
							?>
						</div>
					</div>
					
					<!-- Seção de Cadastro de Novo Admin -->
					<div class="register-admin-section">
						<div class="register-header">
							<h2><i class="bi bi-person-plus"></i> Cadastrar Novo Administrador</h2>
						</div>
						
						<div class="register-content">
							<form id="registerAdminForm" action="../Models/register-admin.php" method="POST">
								<div class="form-group">
									<label for="nome">Nome Completo</label>
									<div class="input-group">
										<span class="input-group-text"><i class="bi bi-person"></i></span>
										<input type="text" class="form-control" id="nome" name="nome" required>
									</div>
								</div>
								
								<div class="form-group">
									<label for="email">Email</label>
									<div class="input-group">
										<span class="input-group-text"><i class="bi bi-envelope"></i></span>
										<input type="email" class="form-control" id="email" name="email" required>
									</div>
								</div>
								
								<div class="form-group">
									<label for="telefone">Telefone</label>
									<div class="input-group">
										<span class="input-group-text"><i class="bi bi-telephone"></i></span>
										<input type="number" class="form-control" id="tel" name="tel">
									</div>
								</div>

								<div class="form-group">
									<label for="telefone">Nº do BI</label>
									<div class="input-group">
										<span class="input-group-text"><i class="bi bi-telephone"></i></span>
										<input type="text" class="form-control" id="BI" name="BI">
									</div>
								</div>
								
								<div class="form-group">
									<label for="senha">Senha</label>
									<div class="input-group">
										<span class="input-group-text"><i class="bi bi-lock"></i></span>
										<input type="password" class="form-control" id="senha" name="senha" required>
									</div>
								</div>
								
								<div class="form-group">
									<label for="confirmar_senha">Confirmar Senha</label>
									<div class="input-group">
										<span class="input-group-text"><i class="bi bi-lock"></i></span>
										<input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" required>
									</div>
								</div>
								
								<div class="form-actions">
									<button type="submit" class="btn btn-register"><i class="bi bi-save"></i> Cadastrar</button>
									<button type="reset" class="btn btn-cancel"><i class="bi bi-x-circle"></i> Limpar</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</main>
	</section>
	<!-- NAVBAR -->

	<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
	<script src="/./../RESINGOLA-main/AGVRR/js/script.js"></script>
</body>
</html>