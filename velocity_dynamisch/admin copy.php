<?php
session_start();

// Überprüfen, ob der Benutzer angemeldet ist
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
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

// Upload form processing
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF-Token ungültig.');
    }

    $titel = htmlspecialchars($_POST['titel'], ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');
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
    <br>
    <span class="nav-link" style="color: white;">Eingeloggt als: <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?></span>

    <h2 class="text-center mb-4">Bild und Beschreibung hochladen</h2>

    <a href="upload.php" class="btn btn-success">Erstelle ein Fahrrad ></a>
    <a href="./logout.php" class="btn btn-danger">Logout</a>

    <!-- Liste der vorhandenen Karten -->
    <h2 class="text-center mt-5">Vorhandene Fahrräder.</h2>
    <div class="row">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="col-md-4 mb-4" style="z-index: 1;">
                        <div class="card">
                            <img src="' . htmlspecialchars($row['image_name'], ENT_QUOTES, 'UTF-8') . '" class="card-img-top" alt="Bike">
                            <div class="card-body">
                                <h5 class="card-title">' . htmlspecialchars($row['titel'], ENT_QUOTES, 'UTF-8') . '</h5>
                                <p class="card-text">' . htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8') . '</p>
                            </div>
                            <div class="card-footer text-center">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="edit.php?id=' . $row['id'] . '" class="btn btn-success">Bearbeiten</a>
                                    <form action="delete.php" method="post" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">
                                        <input type="hidden" name="id" value="' . $row['id'] . '">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm(\'Sind Sie sicher, dass Sie diese Karte löschen möchten?\')">Löschen</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                      </div>';
            }
        } else {
            echo "<p>Keine Fahrräder verfügbar.</p>";
        }
        $conn->close();
        ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>