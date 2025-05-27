<?php
session_start();
require_once __DIR__ . '/./../Config/conection.php';

// Verificar se é admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: ../Views/index.php");
    exit();
}

$stmt = $conn->query("SELECT COUNT(*) as count FROM admin_notifications WHERE read_status = 0");
$unreadCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Debug: Verificar a consulta SQL
$sql = "SELECT * FROM residencia WHERE approval_status = 'pendente'";
error_log("Executando consulta SQL: " . $sql);

// Buscar imóveis pendentes
$stmt = $conn->query($sql);
$pendingProperties = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debug: Verificar número de imóveis encontrados
error_log("Número de imóveis pendentes encontrados: " . count($pendingProperties));

// Debug: Verificar dados de cada imóvel
foreach ($pendingProperties as &$property) {
    error_log("ID do imóvel: " . $property['id']);
    error_log("Status de aprovação: " . $property['approval_status']);
    error_log("Tipo: " . $property['typeResi']);
    error_log("Localização: " . $property['location']);
    
    $property['images'] = json_decode($property['images'], true);
}

// Debug: Verificar se há highlight
if (isset($_GET['highlight'])) {
    error_log("Highlight solicitado para o imóvel ID: " . $_GET['highlight']);
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../Views/CSS/style.css">
    <title>Imóveis Pendentes</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4e73df;
            color: white;
            font-weight: 600;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        button {
            padding: 8px 16px;
            margin: 0 5px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            color: white;
            font-weight: 500;
            transition: opacity 0.2s;
        }
        button:hover {
            opacity: 0.9;
        }
        .approve-btn {
            background-color: #1cc88a;
        }
        .reject-btn {
            background-color: #e74a3b;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-pending {
            background-color: #f6c23e;
            color: #fff;
        }
        .empty-message {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        .property-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>
    <script>
        // Função para carregar imóveis pendentes
        async function loadPendingProperties() {
            try {
                const baseUrl = window.location.origin;
                const response = await fetch(`${baseUrl}/RESINGOLA-main/Backend/get_pending_properties.php`, {
                    credentials: 'include'
                });
                
                const data = await response.json();
                if (data.status === 'success') {
                    const container = document.querySelector('.data');
                    if (data.properties.length === 0) {
                        container.innerHTML = `
                            <div class="empty-message">
                                <i class='bx bx-home' style="font-size: 48px; color: #ddd;"></i>
                                <p>Não há imóveis pendentes para aprovação</p>
                            </div>
                        `;
                        return;
                    }

                    let tableHtml = `
                        <table>
                            <thead>
                                <tr>
                                    <th>Imagem</th>
                                    <th>ID</th>
                                    <th>Tipo</th>
                                    <th>Status</th>
                                    <th>Localização</th>
                                    <th>Preço</th>
                                    <th>Detalhes</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    data.properties.forEach(property => {
                        const images = property.images || [];
                        const firstImage = images[0] ? `../../Backend/uploads/${images[0]}` : '../../Views/Dashboard-main/img/logo_resi.png';
                        
                        tableHtml += `
                            <tr>
                                <td>
                                    <img src="${firstImage}" 
                                         alt="Imagem do imóvel" 
                                         class="property-image"
                                         onerror="this.onerror=null; this.src='../../Views/Dashboard-main/img/logo_resi.png';">
                                </td>
                                <td>${property.id}</td>
                                <td>${property.typeResi}</td>
                                <td>
                                    <span class="status-badge status-pending">
                                        ${property.status}
                                    </span>
                                </td>
                                <td>${property.location}</td>
                                <td>${property.price} kz</td>
                                <td>
                                    <small>
                                        Tipologia: ${property.typology}<br>
                                        Área: ${property.houseSize} m²<br>
                                        Quartos: ${property.bathroomCount}
                                    </small>
                                </td>
                                <td>
                                    <button class="approve-btn" onclick="handleAction(${property.id}, 'approve')">
                                        <i class='bx bx-check'></i> Aprovar
                                    </button>
                                    <button class="reject-btn" onclick="handleAction(${property.id}, 'reject')">
                                        <i class='bx bx-x'></i> Rejeitar
                                    </button>
                                </td>
                            </tr>
                        `;
                    });

                    tableHtml += `
                            </tbody>
                        </table>
                    `;

                    container.innerHTML = tableHtml;

                    // Destacar imóvel se houver um ID no parâmetro highlight
                    const urlParams = new URLSearchParams(window.location.search);
                    const highlightId = urlParams.get('highlight');
                    if (highlightId) {
                        const row = document.querySelector(`tr td:nth-child(2):contains('${highlightId}')`).parentElement;
                        if (row) {
                            row.style.backgroundColor = '#fff3cd';
                            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            setTimeout(() => {
                                row.style.transition = 'background-color 1s ease';
                                row.style.backgroundColor = '';
                            }, 3000);
                        }
                    }
                }
            } catch (error) {
                console.error('Erro ao carregar imóveis pendentes:', error);
            }
        }

        // Função para atualizar contadores
        async function updateCounters() {
            try {
                const response = await fetch('../../Backend/get_notification_counts.php', {
                    credentials: 'include'
                });
                const data = await response.json();
                
                if (data.status === 'success') {
                    // Atualizar badge de notificações
                    const badge = document.querySelector('.badge');
                    if (badge) {
                        badge.textContent = data.unread_count;
                    }
                }
            } catch (error) {
                console.error('Erro ao atualizar contadores:', error);
            }
        }

        // Modificar a função handleAction para atualizar a lista após aprovar/rejeitar
        async function handleAction(propertyId, action) {
            try {
                const baseUrl = window.location.origin;
                const response = await fetch(`${baseUrl}/RESINGOLA-main/Backend/aprovado_property.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    credentials: 'include',
                    body: JSON.stringify({
                        property_id: propertyId,
                        action: action
                    })
                });

                const result = await response.json();
                if (result.status === 'success') {
                    // Atualizar a lista de imóveis e contadores
                    await loadPendingProperties();
                    await updateCounters();
                    alert(result.message);
                } else {
                    if (result.session_debug) {
                        console.error('Debug da sessão:', result.session_debug);
                    }
                    alert('Erro: ' + result.message);
                }
            } catch (error) {
                console.error('Erro ao processar a ação:', error);
                alert('Erro ao processar a ação: ' + error.message);
            }
        }

        // Carregar imóveis inicialmente
        document.addEventListener('DOMContentLoaded', () => {
            loadPendingProperties();
            
            // Atualizar a cada 10 segundos
            setInterval(() => {
                loadPendingProperties();
                updateCounters();
            }, 10000);
        });
    </script>
</head>
<body>
    <section id="sidebar">
        <img src="../Views/Dashboard-main/img/logo_resi.png" alt="Logo">
        <ul class="side-menu">
            <li><a href="../Views/dash.php"><i class='bx bxs-dashboard icon'></i> Dashboard</a></li>
            <li class="divider" data-text="main">Main</li>
            <li>
                <a href="#"><i class='bx bxs-inbox icon'></i> Elementos <i class='bx bx-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="#">Alerta</a></li>
                    <li><a href="#">Mensagens</a></li>
                </ul>
            </li>
            <li><a href="../Views/dash.php"><i class='bx bxs-chart icon'></i> Gráficos</a></li>
            <li><a href="../Views/map/map.php"><i class='bx bxs-widget icon'></i> Mapa</a></li>
            <li class="divider" data-text="table">Tabelas</li>
            <li>
                <a href="#"><i class='bx bxs-notepad icon'></i> Listagens <i class='bx bx-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="../Views/listarUsuarios.php">Listar Usuários</a></li>
                    <li><a href="../Views/listarAdmin.php">Listar Administradores</a></li>
                    <li><a href="../Views/listarResidencias.php">Listar Residências</a></li>
                    <li><a href="../Views/listarPendingProperties.php" class="active">Listar Imóveis Pendentes</a></li>
                </ul>
            </li>
            <li class="divider" data-text="reports">Relatórios</li>
            <li><a href="../Views/relatorios.php"><i class='bx bxs-report icon'></i> Relatórios</a></li>
            <li class="divider" data-text="profile">Perfil</li>
            <li>
                <a href="#"><i class="bi bi-person-fill icon"></i> Meu Perfil <i class='bx bx-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="../Views/Perfil-Admin/perfil-admin.php">Perfil</a></li>
                    <li><a href="../Models/logout.php">Terminar Sessão</a></li>
                </ul>
            </li>
        </ul>
    </section>

    <section id="content">
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
                <ul class="profile-link">
                    <li><a href="#"><i class='bx bxs-user-circle icon'></i> Perfil</a></li>
                    <li><a href="#"><i class='bx bxs-cog'></i> Configurações</a></li>
                    <li><a href="../Models/logout.php"><i class='bx bxs-log-out-circle'></i> Sair</a></li>
                </ul>
            </div>
        </nav>
        <main>
            <h1 class="title">Imóveis Pendentes</h1>
            <ul class="breadcrumbs">
                <li><a href="../Views/dash.php">Início</a></li>
                <li class="divider">/</li>
                <li><a href="#" class="active">Imóveis Pendentes</a></li>
            </ul>
            <div class="data">
                <?php if (empty($pendingProperties)): ?>
                    <div class="empty-message">
                        <i class='bx bx-home' style="font-size: 48px; color: #ddd;"></i>
                        <p>Não há imóveis pendentes para aprovação</p>
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Imagem</th>
                                <th>ID</th>
                                <th>Tipo</th>
                                <th>Status</th>
                                <th>Localização</th>
                                <th>Preço</th>
                                <th>Detalhes</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingProperties as $property): ?>
                                <tr>
                                    <td>
                                        <?php 
                                        $images = $property['images'] ?? [];
                                        $firstImage = !empty($images) ? $images[0] : null;
                                        if ($firstImage): 
                                        ?>
                                            <img src="../../Backend/uploads/<?= htmlspecialchars($firstImage) ?>" 
                                                 alt="Imagem do imóvel" 
                                                 class="property-image"
                                                 onerror="this.onerror=null; this.src='../../Views/Dashboard-main/img/logo_resi.png';">
                                        <?php else: ?>
                                            <div class="property-image" style="background: #eee; display: flex; align-items: center; justify-content: center;">
                                                <i class='bx bx-home' style="font-size: 24px; color: #999;"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($property['id']) ?></td>
                                    <td><?= htmlspecialchars($property['typeResi']) ?></td>
                                    <td>
                                        <span class="status-badge status-pending">
                                            <?= htmlspecialchars($property['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($property['location']) ?></td>
                                    <td><?= htmlspecialchars($property['price']) ?> kz</td>
                                    <td>
                                        <small>
                                            Tipologia: <?= htmlspecialchars($property['typology']) ?><br>
                                            Área: <?= htmlspecialchars($property['houseSize']) ?> m²<br>
                                            Quartos: <?= htmlspecialchars($property['bathroomCount']) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <button class="approve-btn" onclick="handleAction(<?= $property['id'] ?>, 'approve')">
                                            <i class='bx bx-check'></i> Aprovar
                                        </button>
                                        <button class="reject-btn" onclick="handleAction(<?= $property['id'] ?>, 'reject')">
                                            <i class='bx bx-x'></i> Rejeitar
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </main>
    </section>
    <script src="../js/script.js"></script>
</body>
</html>