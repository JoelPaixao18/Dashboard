<?php
session_start();
include_once '../../Config/conection.php';

// Verificação de autenticação
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: ../Views/index.php");
    exit();
}

    $stmt = $conn->query("SELECT COUNT(*) as count FROM admin_notifications WHERE read_status = 0");
	$unreadCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Inicializa variáveis
$processedProperties = [];
$dbError = null;

try {
    // Consulta básica para debug
    $stmt = $conn->query("
        SELECT 
            id, 
            typeResi, 
            location, 
            latitude, 
            longitude, 
            houseSize, 
            price, 
            status
        FROM residencia 
        WHERE latitude IS NOT NULL 
        AND longitude IS NOT NULL
    ");
    
    if ($stmt) {
        $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Número de propriedades encontradas na consulta: " . count($properties));
        
        if ($properties) {
            foreach ($properties as $property) {
                // Debug dos dados brutos
                error_log("Processando propriedade ID: " . $property['id']);
                error_log("Latitude: " . $property['latitude'] . ", Longitude: " . $property['longitude']);
                
                // Verificação adicional dos dados
                if (!is_numeric($property['latitude']) || !is_numeric($property['longitude'])) {
                    error_log("Coordenadas inválidas para ID: " . $property['id']);
                    continue;
                }
                
                $processedProperties[] = [
                    'id' => $property['id'],
                    'typeResi' => $property['typeResi'] ?? 'Imóvel',
                    'location' => isset($property['location']) ? 
                        str_replace(['Província ', 'Município ', 'Distrito ', 'Bairro '], '', $property['location']) : 
                        'Endereço não disponível',
                    'coordinates' => [
                        (float)$property['latitude'],
                        (float)$property['longitude']
                    ],
                    'houseSize' => $property['houseSize'] ?? null,
                    'price' => $property['price'] ?? null,
                    'status' => $property['status'] ?? 'N/A'
                ];
            }
        }
    }
    
    error_log("Número de propriedades processadas: " . count($processedProperties));
    
} catch (PDOException $e) {
    $dbError = "Erro ao buscar imóveis: " . $e->getMessage();
    error_log($dbError);
}

// Converter para JSON
$propertiesJson = json_encode(array_values($processedProperties));
error_log("JSON gerado: " . $propertiesJson);
?>

<!DOCTYPE html>
<html lang="pt-en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../../Views/CSS/style.css">
    <link rel="shortcut icon" href="../../Views/Dashboard-main/img/logo_resi.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <title>Mapa - Painel Administrativo</title>
    <style>
        #map-container {
            width: 100%;
            height: calc(100vh - 60px);
            min-height: 500px; /* Altura mínima garantida */
            position: relative;
            background-color: #f0f0f0; /* Cor de fundo temporária para debug */
        }
        #map {
            width: 100%;
            height: 100%;
        }
        .address-container {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.9);
            padding: 10px;
            text-align: center;
            z-index: 1000;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
        }
        .loading-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }
            /* Estilos do modal */
    .modal {
        display: none;
        position: fixed;
        z-index: 1001;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.4);
    }
    
    .modal-content {
        background-color: #fefefe;
        margin: 10% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 700px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .close-modal {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }
    
    .close-modal:hover {
        color: black;
    }
    
    .property-details {
        margin-top: 20px;
    }
    
    .property-details h3 {
        color: #2c3e50;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }
    
    .detail-row {
        display: flex;
        margin-bottom: 10px;
    }
    
    .detail-label {
        font-weight: bold;
        width: 150px;
        color: #7f8c8d;
    }
    
    .detail-value {
        flex: 1;
    }
    
    .property-images {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        overflow-x: auto;
        padding-bottom: 10px;
    }
    
    .property-images img {
        max-height: 150px;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    </style>
</head>
<body>
    
    <!-- SIDEBAR -->
    <section id="sidebar">
        <img src="../../Views/Dashboard-main/img/logo_resi.png" alt="Logo">
        <ul class="side-menu">
            <li><a href="#"><i class='bx bxs-dashboard icon' ></i> Dashboard</a></li>
            <li class="divider" data-text="main">Main</li>
            <li>
                <a href="#"><i class='bx bxs-inbox icon' ></i> Elementos <i class='bx bx-chevron-right icon-right' ></i></a>
                <ul class="side-dropdown">
                    <li><a href="#">Alerta</a></li>
                    <li><a href="#">Mensagens</a></li>
                </ul>
            </li>
            <li><a href="../../Views/dash.php"><i class='bx bxs-chart icon' ></i> Graficos</a></li>
            <li><a href="../../Views/map/map.php" class="active"><i class='bx bxs-widget icon' ></i> Mapa </a></li>
            <li class="divider" data-text="table">Tabelas</li>
            <li>
                <a href="#"><i class='bx bxs-notepad icon' ></i> Listagens <i class='bx bx-chevron-right icon-right' ></i></a>
                <ul class="side-dropdown">
                    <li><a href="../../Views/listarUsuarios.php">Listar Usuários</a></li>
                    <li><a href="../../Views/listarAdmin.php">Listar Administradores</a></li>
                    <li><a href="../../Views/listarResidencias.php">Listar Residências</a></li>
                    <li><a href="../../Views/listagemGeral.php">Dados - Residência & Proprietário</a></li>
                </ul>
            </li>
            <li class="divider" data-text="profile">Perfil</li>
            <li>
                <a href="#"><i class="bi bi-person-fill icon"></i> Meu Perfil <i class='bx bx-chevron-right icon-right' ></i></a>
                <ul class="side-dropdown">
                    <li><a href="/./../AGVRR/Views/perfil-admin.php"> Perfil </a></li>
                    <li><a href="/./../AGVRR/Models/logout.php"> Terminar Sessão </a></li>
                </ul>
            </li>
        </ul>
        <div class="ads">
            <div class="wrapper">
                <a href="../../Views/map/map.php" class="btn-upgrade">Atualizar</a>
            </div>
        </div>
    </section>
    <!-- SIDEBAR -->

    <!-- NAVBAR -->
    <section id="content">
        <nav>
            <i class='bx bx-menu toggle-sidebar' ></i>
            <form action="#">
                
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

        <!-- MAIN -->
        <main>
            <div id="map-container">
                <div id="map"></div>
                <div class="address-container" id="address-container">
                    <p id="address-text">Obtendo localização...</p>
                </div>
            </div>
        </main>
    </section>

    <!-- Modal para detalhes do imóvel -->
    <div id="propertyModal" class="modal" style="display: none; position: fixed; z-index: 1001; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
        <div class="modal-content" style="background-color: #fefefe; margin: 10% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 700px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
            <span class="close-modal" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
            <div id="modalContent">
                <!-- O conteúdo será carregado aqui via JavaScript -->
            </div>
        </div>
    </div>
    
    <script>
    // Debug: Verifica se os dados estão chegando
    const properties = <?php echo $propertiesJson; ?>;
    console.log("Dados carregados:", properties);

    function initMap() {
        ymaps.ready(function() {
            console.log("Yandex Maps API carregada");
            
            const map = new ymaps.Map("map", {
                center: [-8.8300, 13.2450], // Luanda
                zoom: 12
            });
            
            console.log("Mapa criado, adicionando", properties.length, "marcadores");

            // Adiciona marcadores para os imóveis
            properties.forEach(property => {
                console.log("Processando propriedade:", property);
                
                if (property.coordinates && 
                    Array.isArray(property.coordinates) && 
                    property.coordinates.length === 2) {
                    
                    console.log("Criando marcador em:", property.coordinates);
                    
                    const propertyPlacemark = new ymaps.Placemark(
                        property.coordinates,
                        {
                            hintContent: property.typeResi,
                            balloonContent: `
                                <div style="padding: 10px;">
                                    <strong>${property.typeResi}</strong><br>
                                    ${property.location}<br>
                                    ${property.status}<br>
                                    ${property.price ? property.price + ' Kz' : 'Preço sob consulta'}<br>
                                    ${property.houseSize ? property.houseSize + ' m²' : ''}
                                </div>
                            `
                        },
                        {
                            preset: 'islands#blueDotIcon'
                        }
                    );
                    
                    map.geoObjects.add(propertyPlacemark);
                    console.log("Marcador adicionado com sucesso");
                } else {
                    console.log("Coordenadas inválidas para propriedade:", property);
                }
            });

            // Ajusta o zoom para mostrar todos os marcadores
            if (map.geoObjects.getLength() > 0) {
                map.setBounds(map.geoObjects.getBounds(), {
                    checkZoomRange: true,
                    zoomMargin: 50
                });
            }
        });
    }

    // Inicia o mapa quando a página carregar
    document.addEventListener('DOMContentLoaded', function() {
        console.log("Página carregada, iniciando mapa");
        initMap();
    });
    </script>
    <script src="../../js/script.js"></script>
    <script src="https://api-maps.yandex.ru/2.1/?apikey=45f8077e-cd8f-4919-be26-31ce1a691183&lang=pt_BR" type="text/javascript"></script>
</body>
</html>