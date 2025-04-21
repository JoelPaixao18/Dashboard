<?php
header('Content-Type: application/json');
include_once "../Config/conection.php";

try {
    // Validar método da requisição
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método não permitido", 405);
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
            if (empty($dados[$campo])) {
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

        $opcoesBinarias = ['Sim', 'Não'];
        $camposBinarios = ['quintal', 'garagem', 'hasWater', 'hasElectricity'];
        
        foreach ($camposBinarios as $campo) {
            if (!in_array($dados[$campo], $opcoesBinarias)) {
                $errors[] = "Valor inválido para {$campo}";
            }
        }
    }

    if (!empty($errors)) {
        throw new Exception(implode("<br>", $errors));
    }

    // Preparar a query SQL
    $query = "INSERT INTO residencia (
                typeResi, typology, location, price, status, 
                houseSize, livingRoomCount, bathroomCount, kitchenCount, 
                quintal, andares, garagem, hasWater, hasElectricity
              ) VALUES (
                :typeResi, :typology, :location, :price, :status, 
                :houseSize, :livingRoomCount, :bathroomCount, :kitchenCount, 
                :quintal, :andares, :garagem, :hasWater, :hasElectricity
              )";

    $stmt = $conn->prepare($query);

    // Bind dos parâmetros
    $stmt->bindParam(':typeResi', $dados['typeResi']);
    $stmt->bindParam(':typology', $dados['typology']);
    $stmt->bindParam(':location', $dados['location']);
    $stmt->bindParam(':price', $dados['price'], PDO::PARAM_STR);
    $stmt->bindParam(':status', $dados['status']);

    // Campos específicos da Vivenda (ou NULL se não for Vivenda)
    $houseSize = $dados['typeResi'] === 'Vivenda' ? $dados['houseSize'] : null;
    $livingRoomCount = $dados['typeResi'] === 'Vivenda' ? $dados['livingRoomCount'] : null;
    $bathroomCount = $dados['typeResi'] === 'Vivenda' ? $dados['bathroomCount'] : null;
    $kitchenCount = $dados['typeResi'] === 'Vivenda' ? $dados['kitchenCount'] : null;
    $quintal = $dados['typeResi'] === 'Vivenda' ? $dados['quintal'] : null;
    $andares = $dados['typeResi'] === 'Vivenda' ? $dados['andares'] : null;
    $garagem = $dados['typeResi'] === 'Vivenda' ? $dados['garagem'] : null;
    $hasWater = $dados['typeResi'] === 'Vivenda' ? $dados['hasWater'] : null;
    $hasElectricity = $dados['typeResi'] === 'Vivenda' ? $dados['hasElectricity'] : null;

    $stmt->bindParam(':houseSize', $houseSize);
    $stmt->bindParam(':livingRoomCount', $livingRoomCount);
    $stmt->bindParam(':bathroomCount', $bathroomCount);
    $stmt->bindParam(':kitchenCount', $kitchenCount);
    $stmt->bindParam(':quintal', $quintal);
    $stmt->bindParam(':andares', $andares);
    $stmt->bindParam(':garagem', $garagem);
    $stmt->bindParam(':hasWater', $hasWater);
    $stmt->bindParam(':hasElectricity', $hasElectricity);

    // Executar a query
    if ($stmt->execute()) {
        $response = [
            'erro' => false,
            'msg' => "<div class='alert alert-success'>Residência cadastrada com sucesso!</div>"
        ];
    } else {
        throw new Exception("Erro ao cadastrar residência no banco de dados");
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