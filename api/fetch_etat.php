<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "base"; // Assure-toi que le nom de la base est correct

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connexion échouée: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT gaz, pression, deplacement FROM etat ORDER BY id DESC LIMIT 50");
    
    if (!$stmt->execute()) {
        throw new Exception("Échec exécution requête: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'gaz' => (int)$row['gaz'],
            'pression' => (int)$row['pression'],
            'deplacement' => (int)$row['deplacement']
        ];
    }

    echo json_encode($data);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
} finally {
    if (isset($conn)) $conn->close();
}
?>
