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
                    // Remove qualquer barra inicial
                    $img = ltrim($img, '/');
                    
                    // Verifica se a imagem existe em Backend/uploads/
                    $backendPath = 'Backend/uploads/' . $img;
                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/RESINGOLA-main/' . $backendPath)) {
                        return $backendPath;
                    }
                    
                    // Se não encontrou, verifica com o prefixo 'uploads'
                    $backendPathWithPrefix = 'Backend/uploads/uploads' . $img;
                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/RESINGOLA-main/' . $backendPathWithPrefix)) {
                        return $backendPathWithPrefix;
                    }
                    
                    // Se não encontrou em nenhum lugar, retorna o caminho original
                    return $backendPath;
                }, $decodedImages);
            } else {
                // Se não for um JSON válido, trata como uma única imagem
                $img = ltrim($residencia['images'], '/');
                $backendPath = 'Backend/uploads/' . $img;
                $backendPathWithPrefix = 'Backend/uploads/uploads' . $img;
                
                if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/RESINGOLA-main/' . $backendPathWithPrefix)) {
                    $images = [$backendPathWithPrefix];
                } else {
                    $images = [$backendPath];
                }
            }
        } else if (is_array($residencia['images'])) {
            $images = array_map(function($img) {
                $img = ltrim($img, '/');
                $backendPath = 'Backend/uploads/' . $img;
                $backendPathWithPrefix = 'Backend/uploads/uploads' . $img;
                
                if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/RESINGOLA-main/' . $backendPathWithPrefix)) {
                    return $backendPathWithPrefix;
                }
                return $backendPath;
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