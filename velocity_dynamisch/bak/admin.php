<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Database connection
$conn = new mysqli('sql208.epizy.com', 'epiz_34327624', 'P5lOsuIC072rdn', 'epiz_34327624_velocity');
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Upload form processing
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titel = $_POST['titel'];
    $description = $_POST['description'];
    $target_dir = "uploads/";
    $unique_name = uniqid() . '.' . strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
    $target_file = $target_dir . $unique_name;

    if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $stmt = $conn->prepare("INSERT INTO cards (image_name, titel, description) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $target_file, $titel, $description);
                if ($stmt->execute()) {
                    echo "<div class='alert alert-success text-center'>Die Karte wurde erfolgreich hinzugefügt.</div>";
                    // Redirect to refresh the page
                    header('Refresh: 2; url=admin.php');
                } else {
                    echo "<div class='alert alert-danger text-center'>Fehler beim Speichern: " . $stmt->error . "</div>";
                }
                $stmt->close();
            } else {
                echo "<div class='alert alert-warning text-center'>Fehler beim Upload.</div>";
            }
        } else {
            echo "<div class='alert alert-warning text-center'>Die Datei ist kein Bild.</div>";
        }
    }
}

// SQL Query to Load Cards
$sql = "SELECT * FROM cards";
$result = $conn->query($sql);
?>

<?php include 'includes/header.php'; ?>

<main class="container my-5">
    <h2 class="text-center mb-4">Bild und Beschreibung hochladen</h2>

    <!-- Upload Bild Formular -->
    <form method="post" action="" enctype="multipart/form-data" class="row g-3">
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

    <!-- Liste der vorhandenen Karten -->
    <h2 class="text-center mt-5">Vorhandene Fahrräder.</h2>
    <div class="row">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
        ?>
                <div class="col-md-4 mb-4" style="z-index: 1;">
                    <div class="card">
                        <img src="<?php echo $row['image_name']; ?>" class="card-img-top" alt="Bike" type="image/png">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row['titel']; ?></h5>
                            <!-- <p class="card-text"><?php echo basename($row['image_name']); ?></p> -->
                            <p class="card-text"><?php echo $row['description']; ?></p>
                        </div>
                        <div class="card-footer text-center">
                            <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-success">Bearbeiten</a>
                            <!-- Updated Delete Form with Confirmation -->
                            <form action="delete.php" method="post">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Sind Sie sicher, dass Sie diese Karte löschen möchten?')">Löschen</button>
                            </form>
                        </div>
                    </div>
                </div>
        <?php
            }
        } else {
            echo "<p>Keine Fahrräder verfügbar.</p>";
        }
        $conn->close();
        ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>