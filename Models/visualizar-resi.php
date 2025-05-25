<?php
header('Content-Type: application/json');
require_once '../Config/conection.php';

try {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    
    if (!$id) {
        throw new Exception("ID inválido ou não fornecido");
    }

    // Consulta única para obter todos os dados da residência
    $query = "SELECT * FROM residencia WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $residencia = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$residencia) {
        throw new Exception("Residência não encontrada para o ID: " . $id);
    }

    // Tratamento das imagens
    $images = [];
    if (!empty($residencia['images'])) {
        // Se images é uma string JSON, decodifica
        if (is_string($residencia['images'])) {
            $decodedImages = json_decode($residencia['images'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedImages)) {
                $images = array_map(function($img) {
                    // Remove qualquer barra inicial e limpa o caminho
                    $fileName = ltrim($img, '/');
                    $fileName = preg_replace('/^(Backend\/uploads\/|uploads\/)/', '', $fileName);
                    
                    // Verifica primeiro no diretório Backend/uploads/
                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/RESINGOLA-main/Backend/uploads/' . $fileName)) {
                        return $fileName;
                    }
                    
                    // Se não existir, verifica no diretório uploads/
                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/RESINGOLA-main/uploads/' . $fileName)) {
                        return $fileName;
                    }
                    
                    // Se não encontrar em nenhum lugar, retorna apenas o nome do arquivo
                    return $fileName;
                }, $decodedImages);
            } else {
                // Se não for um JSON válido, trata como uma única imagem
                $fileName = ltrim($residencia['images'], '/');
                $fileName = preg_replace('/^(Backend\/uploads\/|uploads\/)/', '', $fileName);
                $images = [$fileName];
            }
        } else if (is_array($residencia['images'])) {
            $images = array_map(function($img) {
                // Remove qualquer barra inicial e limpa o caminho
                $fileName = ltrim($img, '/');
                return preg_replace('/^(Backend\/uploads\/|uploads\/)/', '', $fileName);
            }, $residencia['images']);
        }
    }

    // Atualiza o campo images com o array processado
    $residencia['images'] = $images;

    // Retorna os dados
    echo json_encode([
        'erro' => false,
        'dados' => $residencia
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'erro' => true,
        'msg' => 'Erro no banco de dados: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'erro' => true,
        'msg' => $e->getMessage()
    ]);
}