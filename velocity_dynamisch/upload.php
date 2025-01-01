<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// CSRF-Token generieren
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Datenbankverbindung
$conn = new mysqli('sql208.epizy.com', 'epiz_34327624', 'P5lOsuIC072rdn', 'epiz_34327624_velocity');
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}
?>

<?php include 'includes/header.php'; ?>

<main class="container my-5">
    <br><br><br>
    <h2 class="text-center mb-4">Fahrrad Artikel erstellen</h2>
    <h3 class="text-center mb-4" style="color:beige">Bild und Beschreibung hochladen</h2>
        <!-- Upload Bild Formular -->
        <form method="post" action="./insert.php" enctype="multipart/form-data" class="row g-3">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="col-md-6">
                <label for="image" class="form-label">Bild:</label>
                <input type="file" accept="image/*" class="form-control" id="image" name="image" required>
            </div>

            <div class="col-md-12">
                <label for="titel" class="form-label">Titel:</label>
                <textarea class="form-control" id="titel" name="titel" rows="1" required></textarea>
            </div>

            <div class="col-md-12">
                <label for="description" class="form-label">Beschreibung:</label>
                <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
            </div>

            <div class="col-md-12 text-center">
                <button type="submit" name="submit" class="btn btn-primary">Upload</button>
            </div>
        </form>
        <br><br><br>
</main>

<?php include 'includes/footer.php'; ?>