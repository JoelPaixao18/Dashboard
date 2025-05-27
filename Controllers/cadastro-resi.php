<?php
header('Content-Type: application/json');
include_once "../Config/conection.php";

try {
    // Validar método da requisição
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método não permitido", 405);
    }

    // Configurações para upload de imagens
    $uploadDir = '../../uploads/';
    $backendUploadDir = '../../Backend/uploads/';
    
    // Garantir que ambos os diretórios existam
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    if (!file_exists($backendUploadDir)) {
        mkdir($backendUploadDir, 0755, true);
    }

    // Processar imagens
    $imagePaths = [];
    if (isset($_FILES['images'])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                $fileName = $_FILES['images']['name'][$key];
                $fileType = $_FILES['images']['type'][$key];
                
                // Verificar tipo do arquivo
                if (!in_array($fileType, ['image/jpeg', 'image/png', 'image/gif'])) {
                    continue;
                }

                // Gerar nome único para a imagem
                $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                $newFileName = 'img_' . date('YmdHis') . '_' . $key . '.' . $extension;
                
                // Salvar em ambos os diretórios
                $targetPath = $uploadDir . $newFileName;
                $backendTargetPath = $backendUploadDir . $newFileName;
                
                // Copiar para ambos os diretórios
                if (move_uploaded_file($tmp_name, $targetPath)) {
                    // Copiar do uploads para Backend/uploads
                    copy($targetPath, $backendTargetPath);
                    // Armazenar apenas o caminho relativo
                    $imagePaths[] = $newFileName;
                }
            }
        }
    }

    // Validar e sanitizar dados de entrada
    $dados = filter_input_array(INPUT_POST, [
        'typeResi' => FILTER_SANITIZE_STRING,
        'typology' => FILTER_SANITIZE_STRING,
        'location' => FILTER_SANITIZE_STRING,
        'price' => FILTER_SANITIZE_NUMBER_FLOAT,
        'status' => FILTER_SANITIZE_STRING,
        'houseSize' => FILTER_SANITIZE_NUMBER_FLOAT,
        'livingRoomCount' => FILTER_SANITIZE_STRING,
        'bathroomCount' => FILTER_SANITIZE_STRING,
        'kitchenCount' => FILTER_SANITIZE_STRING,
        'quintal' => FILTER_SANITIZE_STRING,
        'varanda' => FILTER_SANITIZE_STRING,
        'andares' => FILTER_SANITIZE_STRING,
        'garagem' => FILTER_SANITIZE_STRING,
        'hasWater' => FILTER_SANITIZE_STRING,
        'hasElectricity' => FILTER_SANITIZE_STRING,
        'add' => FILTER_VALIDATE_INT
    ]);

    // Verificar se todos os campos obrigatórios estão presentes
    $camposObrigatorios = ['typeResi', 'typology', 'location', 'price', 'status'];
    $errors = [];

    foreach ($camposObrigatorios as $campo) {
        if (empty($dados[$campo])) {
            $errors[] = "O campo {$campo} é obrigatório";
        }
    }

    // Validações específicas
    if (!is_numeric($dados['price']) || $dados['price'] <= 0) {
        $errors[] = "Preço inválido (deve ser maior que 0)";
    }

    // Validações específicas para Vivenda
    if ($dados['typeResi'] === 'Vivenda') {
        $camposVivenda = ['houseSize', 'livingRoomCount', 'bathroomCount', 'kitchenCount', 'quintal', 'andares', 'garagem', 'hasWater', 'hasElectricity'];
        
        foreach ($camposVivenda as $campo) {
            if (!isset($dados[$campo])) {
                $errors[] = "Para Vivenda, o campo {$campo} é obrigatório";
            }
        }

        if (!is_numeric($dados['houseSize']) || $dados['houseSize'] < 10) {
            $errors[] = "Área inválida (mínimo 10m²)";
        }

        // Validar valores permitidos
        $tipologiasValidas = ['T2', 'T3', 'T4', 'T5+'];
        if (!in_array($dados['typology'], $tipologiasValidas)) {
            $errors[] = "Tipologia inválida";
        }

        $andaresValidos = ['Nenhum', '1 - Andar', '2 - Andar(es)', '3 - Andar(es)'];
        if (!in_array($dados['andares'], $andaresValidos)) {
            $errors[] = "Número de andares inválido";
        }

        // Validação dos campos booleanos
        $camposBinarios = ['quintal', 'garagem', 'hasWater', 'hasElectricity'];
        $opcoesBinarias = ['Sim', 'Não'];
        
        foreach ($camposBinarios as $campo) {
            // Garantir que o valor seja uma string e remover espaços extras
            $valor = trim((string)$dados[$campo]);
            
            // Debug para verificar o valor recebido
            error_log("Valor recebido para {$campo}: " . var_export($valor, true));
            
            if (!in_array($valor, $opcoesBinarias)) {
                $errors[] = "Valor inválido para {$campo}. Deve ser 'Sim' ou 'Não'. Valor recebido: '{$valor}'";
            }
        }
    }

        // Validações específicas para Apartamento
        if ($dados['typeResi'] === 'Apartamento') {
            $camposApartamento = ['houseSize', 'livingRoomCount', 'bathroomCount', 'kitchenCount', 'varanda'];
            
            foreach ($camposApartamento as $campo) {
                if (empty($dados[$campo])) {
                    $errors[] = "Para Apartamento, o campo {$campo} é obrigatório";
                }
            }
    
            if (!is_numeric($dados['houseSize']) || $dados['houseSize'] < 10) {
                $errors[] = "Área inválida (mínimo 10m²)";
            }
    
            // Validar valores permitidos
            $tipologiasValidas = ['T2', 'T3', 'T4', 'T5+'];
            if (!in_array($dados['typology'], $tipologiasValidas)) {
                $errors[] = "Tipologia inválida";
            }

            $opcoesBinarias = ['Sim', 'Não'];
            $camposBinarios = ['varanda'];
            
            foreach ($camposBinarios as $campo) {
                if (!in_array($dados[$campo], $opcoesBinarias)) {
                    $errors[] = "Valor inválido para {$campo}";
                }
            }
        }

    if (!empty($errors)) {
        throw new Exception(implode("<br>", $errors));
    }

    // Preparar a query SQL com o campo images
    $query = "INSERT INTO residencia (
                typeResi, typology, location, price, status, 
                houseSize, livingRoomCount, bathroomCount, kitchenCount, 
                quintal, varanda, andares, garagem, hasWater, hasElectricity,
                images
              ) VALUES (
                :typeResi, :typology, :location, :price, :status, 
                :houseSize, :livingRoomCount, :bathroomCount, :kitchenCount, 
                :quintal, :varanda, :andares, :garagem, :hasWater, :hasElectricity,
                :images
              )";

    $stmt = $conn->prepare($query);

    // Converter array de imagens para JSON
    $imagesJson = json_encode($imagePaths);

    // Bind dos parâmetros
    $stmt->bindParam(':typeResi', $dados['typeResi']);
    $stmt->bindParam(':typology', $dados['typology']);
    $stmt->bindParam(':location', $dados['location']);
    $stmt->bindParam(':price', $dados['price']);
    $stmt->bindParam(':status', $dados['status']);
    $stmt->bindParam(':houseSize', $dados['houseSize']);
    $stmt->bindParam(':livingRoomCount', $dados['livingRoomCount']);
    $stmt->bindParam(':bathroomCount', $dados['bathroomCount']);
    $stmt->bindParam(':kitchenCount', $dados['kitchenCount']);
    $stmt->bindParam(':quintal', $dados['quintal']);
    $stmt->bindParam(':varanda', $dados['varanda']);
    $stmt->bindParam(':andares', $dados['andares']);
    $stmt->bindParam(':garagem', $dados['garagem']);
    $stmt->bindParam(':hasWater', $dados['hasWater']);
    $stmt->bindParam(':hasElectricity', $dados['hasElectricity']);
    $stmt->bindParam(':images', $imagesJson);

    // Executar a query
    if ($stmt->execute()) {
        $response = [
            'erro' => false,
            'msg' => "<div class='alert alert-success'>Imóvel cadastrado com sucesso!</div>"
        ];
    } else {
        throw new Exception("Erro ao cadastrar Imóvel no banco de dados");
    }
} catch (PDOException $e) {
    $response = [
        'erro' => true,
        'msg' => "<div class='alert alert-danger'>Erro no banco de dados: " . $e->getMessage() . "</div>"
    ];
} catch (Exception $e) {
    $response = [
        'erro' => true,
        'msg' => "<div class='alert alert-danger'>" . $e->getMessage() . "</div>"
    ];
} finally {
    echo json_encode($response);
    exit;
}
?>