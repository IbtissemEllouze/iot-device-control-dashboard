<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ibtissem";

try {
    $led = isset($_GET['led']) ? ($_GET['led'] === '1' ? '1' : '0') : null;
    $relay = isset($_GET['relay']) ? ($_GET['relay'] === '1' ? '1' : '0') : null;

    if ($led === null || $relay === null) {
        throw new Exception("Paramètres manquants");
    }

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Connexion échouée: " . $conn->connect_error);
    }

    // On met à jour la dernière ligne insérée
    $stmt = $conn->prepare("UPDATE ibtissem SET led=?, relay=? ORDER BY id DESC LIMIT 1");

    if (!$stmt) {
        throw new Exception("Échec préparation: " . $conn->error);
    }

    $stmt->bind_param("ss", $led, $relay);
    
    if (!$stmt->execute()) {
        throw new Exception("Échec exécution: " . $stmt->error);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Mise à jour réussie',
        'updated' => $stmt->affected_rows
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    file_put_contents('php_error_log.txt', date('Y-m-d H:i:s') . " - " . $e->getMessage() . "\n", FILE_APPEND);
} finally {
    if (isset($conn)) $conn->close();
}
?>
