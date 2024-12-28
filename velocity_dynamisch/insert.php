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

// Eingaben validieren und bereinigen
$titel = htmlspecialchars($_POST['titel'], ENT_QUOTES, 'UTF-8');
$description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');
$target_dir = "uploads/";

// Bild validieren und hochladen
if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
    // Überprüfen, ob die Datei ein Bild ist
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        die('Die Datei ist kein Bild.');
    }

    // Dateityp überprüfen
    $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowed_types)) {
        die('Ungültiger Dateityp.');
    }

    // Eindeutigen Dateinamen generieren
    $unique_name = uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $unique_name;

    // Datei in das Upload-Verzeichnis verschieben
    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        die('Fehler beim Upload.');
    }

    // Bild in die Datenbank einfügen
    $stmt = $conn->prepare("INSERT INTO cards (image_name, titel, description) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $target_file, $titel, $description);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success text-center'>Die Karte wurde erfolgreich hinzugefügt.</div>";
        header('Refresh: 2; url=admin.php');
    } else {
        echo "<div class='alert alert-danger text-center'>Fehler beim Speichern: " . $stmt->error . "</div>";
    }
    $stmt->close();
} else {
    die('Fehler beim Upload: ' . $_FILES['image']['error']);
}

$conn->close();
header('Location: admin.php');
exit();
?>