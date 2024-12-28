<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}
?>

<?php
// Datenbankverbindung
$conn = new mysqli('sql208.epizy.com', 'epiz_34327624', 'P5lOsuIC072rdn', 'epiz_34327624_velocity');
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// ID der Karte abrufen
$id = $_POST['id'];
$description = $_POST['description'];
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["image"]["name"]);

if (!empty($_FILES['image']['name'])) {
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
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
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $image_name = $target_file;
    } else {
        echo "<div class='alert alert-warning text-center'>Die Datei ist kein Bild.</div>";
        header('Location: edit.php?id=' . $id);
        exit();
    }
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

// Beschreibung aktualisieren
$stmt = $conn->prepare("UPDATE cards SET image_name = ?, description = ? WHERE id = ?");
$stmt->bind_param("ssi", $image_name, $description, $id);
if ($stmt->execute()) {
    echo "<div class='alert alert-success text-center'>Die Karte wurde erfolgreich aktualisiert.</div>";
} else {
    echo "<div class='alert alert-danger text-center'>Fehler beim Aktualisieren: " . $stmt->error . "</div>";
}
$stmt->close();
$conn->close();

header('Location: upload.php');
exit();
?>