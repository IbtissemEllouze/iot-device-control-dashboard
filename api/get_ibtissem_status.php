<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ibtissem";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connexion échouée: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT led, relay FROM ibtissem ORDER BY id DESC LIMIT 1");
    
    if (!$stmt->execute()) {
        throw new Exception("Échec exécution requête: " . $stmt->error);
    }

    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            'led' => $row['led'],
            'relay' => $row['relay']
        ]);
    } else {
        echo json_encode([
            'led' => '0',
            'relay' => '0'
        ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
} finally {
    if (isset($conn)) $conn->close();
}
?>
