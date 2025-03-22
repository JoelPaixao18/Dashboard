<?php
include_once '../Config/conection.php';

try {
    $pesquisa = isset($_POST['pesquisa']) ? "%" . $_POST['pesquisa'] . "%" : "%%";

    $query = "SELECT id, nome, email FROM usuario WHERE id LIKE :pesquisa OR nome LIKE :pesquisa OR email LIKE :pesquisa";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':pesquisa', $pesquisa, PDO::PARAM_STR);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) > 0) {
        echo "<table class='table table-bordered'>";
        echo "<tr><th>ID</th><th>Nome</th><th>Email</th></tr>";
        foreach ($result as $row) {
            echo "<tr><td>{$row['id']}</td><td>{$row['nome']}</td><td>{$row['email']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Nenhum usuário encontrado!</p>";
    }
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?>
