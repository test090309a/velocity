<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Security check for ID
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    die('Ungültige ID.');
}
$id = $_POST['id'];

// Datenbankverbindung
$conn = new mysqli('sql208.epizy.com', 'epiz_34327624', 'P5lOsuIC072rdn', 'epiz_34327624_velocity');
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Retrieve image path
$sql = "SELECT image_name FROM cards WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $image_path = $row['image_name'];
    // Delete image from filesystem
    if (file_exists($image_path)) {
        unlink($image_path);
    }
    // Delete record from database
    $del_stmt = $conn->prepare("DELETE FROM cards WHERE id = ?");
    $del_stmt->bind_param("i", $id);
    if ($del_stmt->execute()) {
        echo "<div class='alert alert-success text-center'>Die Karte wurde erfolgreich gelöscht.</div>";
    } else {
        echo "<div class='alert alert-danger text-center'>Fehler beim Löschen: " . $del_stmt->error . "</div>";
    }
    $del_stmt->close();
} else {
    echo "<div class='alert alert-warning text-center'>Karte nicht gefunden.</div>";
}
$stmt->close();
$conn->close();

// Redirect back to admin.php
header('Location: admin.php');
exit();
?>