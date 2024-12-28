<?php
session_start();

// Überprüfen, ob der Benutzer angemeldet ist
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Datenbankverbindung
$conn = new mysqli('sql208.epizy.com', 'epiz_34327624', 'P5lOsuIC072rdn', 'epiz_34327624_velocity');
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Daten aus dem POST-Request lesen
$data = json_decode(file_get_contents('php://input'), true);
$order = $data['order'];

// Reihenfolge in der Datenbank aktualisieren
foreach ($order as $index => $id) {
    $stmt = $conn->prepare("UPDATE cards SET sort_order = ? WHERE id = ?");
    $stmt->bind_param("ii", $index, $id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();

// Erfolgsmeldung zurückgeben
echo json_encode(['success' => true]);
?>