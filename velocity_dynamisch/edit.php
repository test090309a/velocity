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
$id = $_GET['id'];
$sql = "SELECT * FROM cards WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
} else {
    header('Location: upload.php');
    exit();
}
$stmt->close();
$conn->close();
?>

<?php include 'includes/header.php'; ?>

<main class="container my-5">
    <h2 class="text-center mb-4">Karte bearbeiten</h2>
    <form method="post" action="update.php" enctype="multipart/form-data" class="row g-3">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
        <div class="col-md-6">
            <label for="image" class="form-label">Bild:</label>
            <img src="<?php echo $row['image_name']; ?>" alt="Bild" class="img-fluid mb-2">
            <input type="file" accept="image/*" class="form-control" id="image" name="image">
        </div>

        <div class="col-md-12">
            <label for="titel" class="form-label">Titel:</label>
            <textarea class="form-control" id="titel" name="titel" rows="1" required><?php echo $row['titel']; ?></textarea>
        </div>

        <div class="col-md-12">
            <label for="description" class="form-label">Beschreibung:</label>
            <textarea class="form-control" id="description" name="description" rows="5" required><?php echo $row['description']; ?></textarea>
        </div>
        <div class="col-md-12 text-center">
            <button type="submit" name="submit" class="btn btn-primary">Speichern</button>
        </div>
    </form>
</main>

<?php include 'includes/footer.php'; ?>