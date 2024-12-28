<?php
session_start();

// Überprüfen, ob der Benutzer angemeldet ist
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// CSRF-Token überprüfen
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF-Token ungültig.');
}

// Datenbankverbindung
$conn = new mysqli('sql208.epizy.com', 'epiz_34327624', 'P5lOsuIC072rdn', 'epiz_34327624_velocity');
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// ID der Karte abrufen und validieren
$id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
if ($id === false) {
    die('Ungültige ID.');
}

$titel = htmlspecialchars($_POST['titel'], ENT_QUOTES, 'UTF-8');
$description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');
$target_dir = "uploads/";

if (!empty($_FILES['image']['name'])) {
    $imageFileType = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowed_types)) {
        die('Ungültiger Dateityp.');
    }

    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        die('Die Datei ist kein Bild.');
    }

    // Altes Bild entfernen
    $sql = "SELECT image_name FROM cards WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $old_image = $result->fetch_assoc()['image_name'];
        if (file_exists($old_image)) {
            unlink($old_image);
        }
    }

    // Neues Bild hochladen
    $unique_name = uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $unique_name;
    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        die('Fehler beim Upload.');
    }
    $image_name = $target_file;
} else {
    // Behalte das alte Bild
    $sql = "SELECT image_name FROM cards WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $image_name = $result->fetch_assoc()['image_name'];
    }
}

// Beschreibung und Titel aktualisieren
$stmt = $conn->prepare("UPDATE cards SET image_name = ?, description = ?, titel = ? WHERE id = ?");
$stmt->bind_param("sssi", $image_name, $description, $titel, $id);
if ($stmt->execute()) {
    echo "<div class='alert alert-success text-center'>Die Karte wurde erfolgreich aktualisiert.</div>";
} else {
    echo "<div class='alert alert-danger text-center'>Fehler beim Aktualisieren: " . $stmt->error . "</div>";
}
$stmt->close();
$conn->close();

header('Location: admin.php');
exit();
?>