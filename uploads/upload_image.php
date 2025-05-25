<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

require_once '../Config/conection.php';

// Configurações
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
$maxFileSize = 2 * 1024 * 1024; // 2MB
$relativeUploadDir = '../uploads/';
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

try {
    // Verifica se o arquivo foi enviado corretamente
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Erro no upload da imagem. Código: ' . ($_FILES['image']['error'] ?? 'Nenhum arquivo enviado'));
    }

    // Validações de segurança
    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($fileInfo, $_FILES['image']['tmp_name']);
    finfo_close($fileInfo);

    if (!in_array($mimeType, $allowedTypes)) {
        throw new Exception('Tipo de arquivo não permitido. Envie apenas imagens JPEG, PNG ou GIF');
    }

    if ($_FILES['image']['size'] > $maxFileSize) {
        throw new Exception('Tamanho do arquivo excede o limite de 2MB');
    }

    // Cria diretório se não existir
    if (!file_exists($relativeUploadDir)) {
        if (!mkdir($relativeUploadDir, 0755, true)) {
            throw new Exception('Falha ao criar diretório de uploads');
        }
    }

    // Gera nome único para o arquivo
    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $fileName = 'img_' . date('YmdHis') . '_' . uniqid() . '.' . $extension;
    $filePath = $relativeUploadDir . $fileName;

    // Move o arquivo
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
        throw new Exception('Falha ao salvar a imagem no servidor');
    }

    // Retorna resposta JSON
    echo json_encode([
        'status' => 'success',
        'imageUrl' => $baseUrl . '/RESINGOLA-main/Backend/uploads/' . $fileName,
        'fileName' => $fileName,
        'relativePath' => 'uploads/' . $fileName // Caminho relativo para armazenar no banco
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
exit;