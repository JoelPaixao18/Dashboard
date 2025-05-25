<?php
include_once '../Config/conection.php';

session_start();

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../Views/CSS/style.css">
    <link rel="stylesheet" href="../Views/CSS/styles-dash.css">
    <link rel="shortcut icon" href="../Views/Dashboard-main/img/logo_resin.ico">
    <title>Listagem Geral - Usuários e Imóveis</title>
</head>
<body>
    <!-- SIDEBAR -->
    <section id="sidebar">
        <img src="../Views/Dashboard-main/img/logo_resi.png" alt="Logo">
        <ul class="side-menu">
            <li><a href="#"><i class='bx bxs-dashboard icon'></i> Dashboard</a></li>
            <li class="divider" data-text="main">Main</li>
            <li>
                <a href="#"><i class='bx bxs-inbox icon'></i> Elementos <i class='bx bx-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="#">Alerta</a></li>
                    <li><a href="#">Mensagens</a></li>
                    <li><a href="../Models/gerar_relatorio_geral.php" target="_blank">Relatório Geral</a></li>
                </ul>
            </li>
            <li><a href="../Views/dash.php"><i class='bx bxs-chart icon'></i> Graficos</a></li>
            <li><a href="../Views/map/map.php"><i class='bx bxs-widget icon'></i> Mapa</a></li>
            <li class="divider" data-text="table">Tabelas</li>
            <li>
                <a href="#"><i class='bx bxs-notepad icon'></i> Listagens <i class='bx bx-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="../Views/listarUsuarios.php">Listar Usuários</a></li>
                    <li><a href="../Views/listarAdmin.php">Listar Administradores</a></li>
                    <li><a href="../Views/listarResidencias.php">Listar Residências</a></li>
                    <li><a href="../Views/listagemGeral.php" class="active">Dados - Residência & Proprietário</a></li>
                </ul>
            </li>
            <li class="divider" data-text="profile">Perfil</li>
            <li>
                <a href="#"><i class="bi bi-person-fill icon"></i> Meu Perfil <i class='bx bx-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="../Views/Perfil-Admin/perfil-admin.php">Perfil</a></li>
                    <li><a href="../Models/logout.php">Terminar Sessão</a></li>
                </ul>
            </li>
        </ul>
        <div class="ads">
            <div class="wrapper">
                <a href="../Views/listagemGeral.php" class="btn-upgrade">Atualizar Página</a>
            </div>
        </div>
    </section>
    <!-- SIDEBAR -->

    <!-- NAVBAR -->
    <section id="content">
        <nav>
            <i class='bx bx-menu toggle-sidebar'></i>
            <form id="searchForm" onsubmit="event.preventDefault(); filterGeral();" class="d-flex align-items-center">
                <div class="input-group" style="width: 300px; margin-top: 1.5rem; height: 38px;">
                    <input type="search" id="searchInput" class="form-control border-end-0 h-100" style="margin-top: -0.5rem;" placeholder="Pesquisar usuários ou imóveis...">
                    <button type="submit" class="btn btn-primary border-start-0 h-100 px-3 d-flex align-items-center justify-content-center" style="margin-top: -0.5rem;">
                        <i class='bx bx-search icon'></i>
                    </button>
                </div>
            </form>
            <a href="../Models/gerar_relatorio_geral.php" target="_blank" class="btn btn-outline-light">
                <i class='bx bxs-file-pdf icon'></i>
            </a>
            <a href="#" class="nav-link">
                <i class='bx bxs-bell icon'></i>
                <span class="badge">5</span>
            </a>
            <a href="#" class="nav-link">
                <i class='bx bxs-message-square-dots icon'></i>
                <span class="badge">8</span>
            </a>
            <div class="profile">
                <?php
                $nomeAdmin = htmlspecialchars($_SESSION['nome'] ?? 'Admin');
                $partesNome = array_filter(explode(' ', $nomeAdmin));
                
                $iniciais = '';
                if (!empty($partesNome)) {
                    $iniciais .= strtoupper(substr($partesNome[0], 0, 1));
                    if (count($partesNome) > 1) {
                        $iniciais .= strtoupper(substr(end($partesNome), 0, 1));
                    }
                }
                ?>
                
                <div class="profile-initials"><?= $iniciais ?: 'AD' ?></div>
                
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
            <div class="container-fluid my-4">
                <div class="topbar">
                    <h2 style="margin-top: 5rem;">Listagem Geral - Usuários e Imóveis</h2>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="input-group" style="max-width: 300px;">
                                <input type="search" id="searchInput" class="form-control" placeholder="Pesquisar...">
                                <button class="btn btn-primary" type="button" onclick="filterGeral()">
                                    <i class='bx bx-search'></i>
                                </button>
                            </div>
                            <div>
                                <a href="../Models/gerar_relatorio_geral.php" target="_blank" class="btn btn-danger">
                                    <i class='bx bxs-file-pdf'></i> Exportar PDF
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <div id="msgAlerta"></div>
                            <div class="listar-geral"></div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <!-- MAIN -->
    </section>
    <!-- NAVBAR -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/custom-geral.js"></script>
	<script src="../js/filtragem.js"></script>
	<script src="../js/script.js"></script>
</body>
</html>