<?php
session_start();
require_once __DIR__ . '/./../Config/conection.php';

// Verificar se é admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: ../Views/index.php");
    exit();
}

// Buscar notificações
$stmt = $conn->query("SELECT * FROM admin_notifications ORDER BY read_status ASC, created_at DESC LIMIT 50");
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contar notificações não lidas
$stmt = $conn->query("SELECT COUNT(*) as count FROM admin_notifications WHERE read_status = 0");
$unreadCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../Views/CSS/style.css">
    <title>Notificações do Admin</title>
    <style>
        .notifications-container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        .notification-item {
            background: #fff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        .notification-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .notification-item.unread {
            border-left: 4px solid #4e73df;
            background-color: #f8f9fc;
        }
        .notification-icon {
            margin-right: 15px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #4e73df;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .notification-content {
            flex: 1;
        }
        .notification-message {
            margin: 0;
            color: #333;
            font-size: 16px;
        }
        .notification-time {
            color: #666;
            font-size: 12px;
            margin-top: 5px;
        }
        .notification-actions {
            display: flex;
            gap: 10px;
        }
        .action-button {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        .view-button {
            background-color: #4e73df;
            color: white;
        }
        .view-button:hover {
            background-color: #2e59d9;
        }
        .mark-read-button {
            background-color: #1cc88a;
            color: white;
        }
        .mark-read-button:hover {
            background-color: #169b6b;
        }
        .empty-notifications {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        .empty-notifications i {
            font-size: 48px;
            color: #ddd;
            margin-bottom: 15px;
        }
        .notifications-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .unread-count {
            background: #4e73df;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 14px;
        }
    </style>
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
                    <li><a href="../Views/listarPendingProperties.php">Listar Imóveis Pendentes</a></li>
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
            <a href="#" class="nav-link">
                <i class='bx bxs-bell icon'></i>
                <span class="badge"><?= $unreadCount ?></span>
            </a>
            <!--<a href="#" class="nav-link">
				<i class='bx bxs-message-square-dots icon'></i>
				<span class="badge">8</span>
			</a>-->
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
            <h1 class="title">Notificações</h1>
            <ul class="breadcrumbs">
                <li><a href="../Views/dash.php">Início</a></li>
                <li class="divider">/</li>
                <li><a href="#" class="active">Notificações</a></li>
            </ul>

            <div class="notifications-container">
                <div class="notifications-header">
                    <h2>Todas as Notificações</h2>
                    <?php if ($unreadCount > 0): ?>
                        <span class="unread-count"><?= $unreadCount ?> não lida<?= $unreadCount > 1 ? 's' : '' ?></span>
                    <?php endif; ?>
                </div>

                <?php if (empty($notifications)): ?>
                    <div class="empty-notifications">
                        <i class='bx bx-bell'></i>
                        <p>Nenhuma notificação disponível</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($notifications as $notification): ?>
                        <div class="notification-item <?= $notification['read_status'] ? '' : 'unread' ?>" 
                             data-id="<?= $notification['id'] ?>">
                            <div class="notification-icon">
                                <i class='bx bx-bell'></i>
                            </div>
                            <div class="notification-content">
                                <p class="notification-message"><?= htmlspecialchars($notification['message']) ?></p>
                                <p class="notification-time">
                                    <?= date('d/m/Y H:i', strtotime($notification['created_at'])) ?>
                                </p>
                            </div>
                            <div class="notification-actions">
                                <?php if ($notification['property_id']): ?>
                                    <button class="action-button view-button" 
                                            onclick="viewProperty(<?= $notification['property_id'] ?>)">
                                        Ver Imóvel
                                    </button>
                                <?php endif; ?>
                                <?php if (!$notification['read_status']): ?>
                                    <button class="action-button mark-read-button" 
                                            onclick="markAsRead(<?= $notification['id'] ?>)">
                                        Marcar como Lida
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </section>

    <script>
    function markAsRead(notificationId) {
        fetch('../../Backend/mark_admin_notification_read.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                notification_id: notificationId
            }),
            credentials: 'include'
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                // Atualizar a UI
                const notification = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
                if (notification) {
                    notification.classList.remove('unread');
                    
                    // Remover o botão "Marcar como Lida"
                    const markReadButton = notification.querySelector('.mark-read-button');
                    if (markReadButton) {
                        markReadButton.remove();
                    }

                    // Atualizar o contador de não lidas
                    const unreadCount = document.querySelector('.unread-count');
                    const badgeCount = document.querySelector('.badge');
                    
                    if (data.unread_count !== undefined) {
                        // Usar o valor retornado pelo servidor
                        if (data.unread_count > 0) {
                            if (unreadCount) {
                                unreadCount.textContent = `${data.unread_count} não lida${data.unread_count > 1 ? 's' : ''}`;
                            }
                            if (badgeCount) {
                                badgeCount.textContent = data.unread_count;
                            }
                        } else {
                            // Se não há mais notificações não lidas
                            if (unreadCount) unreadCount.remove();
                            if (badgeCount) badgeCount.textContent = '0';
                        }
                    }
                }
            } else {
                throw new Error(data.message || 'Erro ao marcar notificação como lida');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert(error.message || 'Erro ao marcar notificação como lida');
        });
    }

    function viewProperty(propertyId) {
        window.location.href = `listarPendingProperties.php?highlight=${propertyId}`;
    }

    // Atualização automática das notificações a cada 30 segundos
    setInterval(() => {
        fetch('../../Backend/get_admin_notifications.php', {
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Atualizar a contagem de não lidas
                const unreadCount = document.querySelector('.unread-count');
                const badgeCount = document.querySelector('.badge');
                
                if (data.unread_count > 0) {
                    if (unreadCount) {
                        unreadCount.textContent = `${data.unread_count} não lida${data.unread_count > 1 ? 's' : ''}`;
                    }
                    if (badgeCount) {
                        badgeCount.textContent = data.unread_count;
                    }
                }

                // Se houver novas notificações, recarregar a página
                if (data.notifications.length > document.querySelectorAll('.notification-item').length) {
                    location.reload();
                }
            }
        })
        .catch(error => console.error('Erro ao atualizar notificações:', error));
    }, 30000);
    </script>

    <script src="../js/script.js"></script>
</body>
</html> 