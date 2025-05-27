<?php
session_start();
include_once '../Config/conection.php';

// Verificação de login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: ../Views/index.php");
    exit();
}

$stmt = $conn->query("SELECT COUNT(*) as count FROM admin_notifications WHERE read_status = 0");
$unreadCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Processar filtros
$where = "1=1";
$params = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['tipo_imovel'])) {
        $where .= " AND r.typeResi = :tipo_imovel";
        $params[':tipo_imovel'] = $_POST['tipo_imovel'];
    }
    if (!empty($_POST['status'])) {
        $where .= " AND r.status = :status";
        $params[':status'] = $_POST['status'];
    }
    if (!empty($_POST['preco_min'])) {
        $where .= " AND r.price >= :preco_min";
        $params[':preco_min'] = $_POST['preco_min'];
    }
    if (!empty($_POST['preco_max'])) {
        $where .= " AND r.price <= :preco_max";
        $params[':preco_max'] = $_POST['preco_max'];
    }
    if (!empty($_POST['usuario'])) {
        $where .= " AND u.nome LIKE :usuario";
        $params[':usuario'] = '%' . $_POST['usuario'] . '%';
    }

    // Construir a consulta SQL base dependendo do tipo de usuário
    if (!empty($_POST['tipo_usuario'])) {
        $tipo = strtolower($_POST['tipo_usuario']);
        if ($tipo === 'admin' || $tipo === 'administrador') {
            // Consulta específica para administradores
            $sql = "SELECT r.*, a.nome as nome_usuario, 'Administrador' as tipo_usuario
                   FROM residencia r 
                   INNER JOIN administrador a ON r.user_id = a.id
                   WHERE " . $where . " 
                   ORDER BY r.id DESC";
        } else {
            // Consulta para usuários regulares
            $sql = "SELECT r.*, u.nome as nome_usuario, 'Usuário' as tipo_usuario
                   FROM residencia r 
                   INNER JOIN usuario u ON r.user_id = u.id
                   WHERE " . $where . " AND u.role = 'user'
                   ORDER BY r.id DESC";
        }
    } else {
        // Consulta para todos os tipos
        $sql = "SELECT r.*, 
                CASE 
                    WHEN a.id IS NOT NULL THEN a.nome
                    ELSE u.nome
                END as nome_usuario,
                CASE 
                    WHEN a.id IS NOT NULL THEN 'Administrador'
                    ELSE 'Usuário'
                END as tipo_usuario
                FROM residencia r 
                LEFT JOIN usuario u ON r.user_id = u.id AND u.role = 'user'
                LEFT JOIN administrador a ON r.user_id = a.id
                WHERE " . $where . " 
                ORDER BY r.id DESC";
    }
}

// Buscar lista de usuários para o select
$queryUsuarios = "SELECT DISTINCT nome FROM usuario 
                 UNION 
                 SELECT DISTINCT nome FROM administrador 
                 ORDER BY nome";
$stmtUsuarios = $conn->query($queryUsuarios);
$usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_COLUMN);
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Relatórios - Painel Administrativo</title>
    <style>
        .filter-section {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .filter-item {
            margin-bottom: 15px;
        }
        .filter-item label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }
        .filter-item select,
        .filter-item input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: #4e73df;
            color: white;
        }
        .btn-secondary {
            background: #858796;
            color: white;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .report-actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .report-table th,
        .report-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .report-table th {
            background: #f8f9fc;
            font-weight: 600;
        }
        .report-table tr:hover {
            background: #f8f9fc;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-venda {
            background: #e8f5e9;
            color: #2e7d32;
        }
        .status-arrendamento {
            background: #e3f2fd;
            color: #1565c0;
        }
        .status-admin {
            background: #fff3e0;
            color: #e65100;
        }
        .status-user {
            background: #e8eaf6;
            color: #283593;
        }
        /* Estilo para o datalist */
        input[list] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <!-- SIDEBAR -->
    <section id="sidebar">
        <img src="../Views/Dashboard-main/img/logo_resi.png" alt="Logo">
        <ul class="side-menu">
            <li><a href="dash.php"><i class='bx bxs-dashboard icon'></i> Dashboard</a></li>
            <li class="divider" data-text="main">Main</li>
            <li>
                <a href="#"><i class='bx bxs-inbox icon'></i> Elementos <i class='bx bx-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="#">Alerta</a></li>
                    <li><a href="#">Mensagens</a></li>
                </ul>
            </li>
            <li><a href="dash.php"><i class='bx bxs-chart icon'></i> Graficos</a></li>
            <li><a href="map/map.php"><i class='bx bxs-widget icon'></i> Mapa</a></li>
            <li class="divider" data-text="table">Tabelas</li>
            <li>
                <a href="#"><i class='bx bxs-notepad icon'></i> Listagens <i class='bx bx-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="listarUsuarios.php">Listar Usuários</a></li>
                    <li><a href="listarAdmin.php">Listar Administradores</a></li>
                    <li><a href="listarResidencias.php">Listar Residências</a></li>
                    <!--<li><a href="listagemGeral.php">Dados - Residência & Proprietário</a></li>-->
                    <li><a href="../Views/listarPendingProperties.php">Listar Imóveis Pendentes</a></li>
                </ul>
            </li>
            <li class="divider" data-text="reports">Relatórios</li>
            <li><a href="relatorios.php" class="active"><i class='bx bxs-report icon'></i> Relatórios</a></li>
            <li class="divider" data-text="profile">Perfil</li>
            <li>
                <a href="#"><i class="bi bi-person-fill icon"></i> Meu Perfil <i class='bx bx-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="Perfil-Admin/perfil-admin.php">Perfil</a></li>
                    <li><a href="../Models/logout.php">Terminar Sessão</a></li>
                </ul>
            </li>
        </ul>
    </section>
    <!-- SIDEBAR -->

    <!-- NAVBAR -->
    <section id="content">
        <!-- NAVBAR -->
        <nav>
            <i class='bx bx-menu toggle-sidebar'></i>
            <form action="#">
                <div class="form-group">
                    <input type="text" placeholder="Search...">
                    <i class='bx bx-search icon'></i>
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
                $partesNome = array_filter(explode(' ', $nomeAdmin));
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
            </div>
        </nav>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <h1 class="title">Relatórios</h1>
            <ul class="breadcrumbs">
                <li><a href="dash.php">Início</a></li>
                <li class="divider">/</li>
                <li><a href="#" class="active">Relatórios</a></li>
            </ul>

            <!-- Seção de Filtros -->
            <div class="filter-section">
                <h2><i class="fas fa-filter"></i> Filtros</h2>
                <form method="POST" action="">
                    <div class="filter-grid">
                        <div class="filter-item">
                            <label for="tipo_imovel">Tipo de Imóvel</label>
                            <select name="tipo_imovel" id="tipo_imovel">
                                <option value="">Todos</option>
                                <option value="Apartamento">Apartamento</option>
                                <option value="Vivenda">Vivenda</option>
                                <option value="Moradia">Moradia</option>
                            </select>
                        </div>
                        <div class="filter-item">
                            <label for="status">Status</label>
                            <select name="status" id="status">
                                <option value="">Todos</option>
                                <option value="venda">Venda</option>
                                <option value="arrendamento">Arrendamento</option>
                            </select>
                        </div>
                        <div class="filter-item">
                            <label for="usuario">Proprietário</label>
                            <input type="text" name="usuario" id="usuario" list="lista-usuarios" placeholder="Nome do proprietário">
                            <datalist id="lista-usuarios">
                                <?php foreach ($usuarios as $nome): ?>
                                    <option value="<?= htmlspecialchars($nome) ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        <div class="filter-item">
                            <label for="tipo_usuario">Tipo de Usuário</label>
                            <select name="tipo_usuario" id="tipo_usuario">
                                <option value="">Todos</option>
                                <option value="user">Usuário</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>

                        <div class="filter-item">
                            <label for="preco_min">Preço Mínimo (Kz)</label>
                            <input type="number" name="preco_min" id="preco_min" min="0" step="0.01" placeholder="0" class="form-control">
                        </div>
                        <div class="filter-item">
                            <label for="preco_max">Preço Máximo (Kz)</label>
                            <input type="number" name="preco_max" id="preco_max" min="0" step="0.01" placeholder="999999999" class="form-control">
                        </div>
                    </div>
                    <div class="btn-group">
                        <button type="reset" class="btn btn-secondary">Limpar</button>
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                    </div>
                </form>
            </div>

            <!-- Resultados e Ações -->
            <div class="report-actions">
                <button class="btn btn-primary" onclick="exportarPDF()">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </button>
                <button class="btn btn-primary" onclick="exportarExcel()">
                    <i class="fas fa-file-excel"></i> Exportar Excel
                </button>
            </div>

            <!-- Tabela de Resultados -->
            <div class="table-responsive">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tipo</th>
                            <th>Tipologia</th>
                            <th>Localização</th>
                            <th>Preço</th>
                            <th>Status</th>
                            <th>Proprietário</th>
                            <th>Tipo Usuário</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($sql)) {
                            try {
                                $stmt = $conn->prepare($sql);
                                foreach ($params as $key => $value) {
                                    $stmt->bindValue($key, $value);
                                }
                                $stmt->execute();
                                
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $statusClass = $row['status'] == 'venda' ? 'status-venda' : 'status-arrendamento';
                                    $tipoUsuarioClass = $row['tipo_usuario'] == 'Administrador' ? 'status-admin' : 'status-user';
                                    echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>{$row['typeResi']}</td>
                                        <td>{$row['typology']}</td>
                                        <td>{$row['location']}</td>
                                        <td>" . number_format($row['price'], 2, ',', '.') . " Kz</td>
                                        <td><span class='status-badge {$statusClass}'>{$row['status']}</span></td>
                                        <td>{$row['nome_usuario']}</td>
                                        <td><span class='status-badge {$tipoUsuarioClass}'>{$row['tipo_usuario']}</span></td>
                                    </tr>";
                                }
                            } catch (PDOException $e) {
                                echo "<tr><td colspan='8'>Erro ao carregar dados: " . $e->getMessage() . "</td></tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
        <!-- MAIN -->
    </section>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <script>
        // Função para exportar para PDF
        function exportarPDF() {
            // Pegar todos os valores dos filtros
            const form = document.querySelector('form');
            const formData = new FormData(form);
            const params = new URLSearchParams();

            // Adicionar apenas os filtros que têm valores
            for (const [key, value] of formData.entries()) {
                if (value) {
                    params.append(key, value);
                }
            }

            // Redirecionar para a página de geração do PDF com os parâmetros
            window.location.href = '../Models/gerar_relatorio_filtrado.php?' + params.toString();
        }

        // Função para exportar para Excel
        function exportarExcel() {
            const table = document.querySelector('.report-table');
            const wb = XLSX.utils.table_to_book(table, {sheet: "Relatório"});
            XLSX.writeFile(wb, 'relatorio_residencias.xlsx');
        }

        // Script para o menu lateral
        const allDropdown = document.querySelectorAll('#sidebar .side-dropdown');
        const sidebar = document.getElementById('sidebar');

        allDropdown.forEach(item => {
            const a = item.parentElement.querySelector('a:first-child');
            a.addEventListener('click', function (e) {
                e.preventDefault();
                if (!this.classList.contains('active')) {
                    allDropdown.forEach(i => {
                        const aLink = i.parentElement.querySelector('a:first-child');
                        aLink.classList.remove('active');
                        i.classList.remove('show');
                    });
                }
                this.classList.toggle('active');
                item.classList.toggle('show');
            });
        });

        // SIDEBAR COLLAPSE
        const toggleSidebar = document.querySelector('nav .toggle-sidebar');
        const allSideDivider = document.querySelectorAll('#sidebar .divider');

        if (sidebar.classList.contains('hide')) {
            allSideDivider.forEach(item => {
                item.textContent = '-'
            });
            allDropdown.forEach(item => {
                const a = item.parentElement.querySelector('a:first-child');
                a.classList.remove('active');
                item.classList.remove('show');
            });
        } else {
            allSideDivider.forEach(item => {
                item.textContent = item.dataset.text;
            });
        }

        toggleSidebar.addEventListener('click', function () {
            sidebar.classList.toggle('hide');

            if (sidebar.classList.contains('hide')) {
                allSideDivider.forEach(item => {
                    item.textContent = '-'
                });

                allDropdown.forEach(item => {
                    const a = item.parentElement.querySelector('a:first-child');
                    a.classList.remove('active');
                    item.classList.remove('show');
                });
            } else {
                allSideDivider.forEach(item => {
                    item.textContent = item.dataset.text;
                });
            }
        });

        sidebar.addEventListener('mouseleave', function () {
            if (this.classList.contains('hide')) {
                allDropdown.forEach(item => {
                    const a = item.parentElement.querySelector('a:first-child');
                    a.classList.remove('active');
                    item.classList.remove('show');
                });
                allSideDivider.forEach(item => {
                    item.textContent = '-'
                });
            }
        });

        sidebar.addEventListener('mouseenter', function () {
            if (this.classList.contains('hide')) {
                allDropdown.forEach(item => {
                    const a = item.parentElement.querySelector('a:first-child');
                    a.classList.remove('active');
                    item.classList.remove('show');
                });
                allSideDivider.forEach(item => {
                    item.textContent = item.dataset.text;
                });
            }
        });
    </script>
</body>
</html> 