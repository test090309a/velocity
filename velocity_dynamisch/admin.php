<?php
session_start();

// Überprüfen, ob der Benutzer angemeldet ist
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// CSRF-Token generieren, falls nicht vorhanden. Token werden einmal pro Session erzeugt.
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Datenbankverbindung
$conn = new mysqli('sql208.epizy.com', 'epiz_34327624', 'P5lOsuIC072rdn', 'epiz_34327624_velocity');
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Status einer Kontaktanfrage aktualisieren
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF-Token ungültig.');
    }

    $id = intval($_POST['id']);
    $status = htmlspecialchars($_POST['status'], ENT_QUOTES, 'UTF-8');

    $stmt = $conn->prepare("UPDATE kontaktanfragen SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success text-center'>Status erfolgreich aktualisiert.</div>";
        header('Refresh: 2; url=admin.php');
    } else {
        echo "<div class='alert alert-danger text-center'>Fehler beim Aktualisieren: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

// Kontaktanfrage löschen
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF-Token ungültig.');
    }

    $id = intval($_POST['id']);

    $stmt = $conn->prepare("DELETE FROM kontaktanfragen WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success text-center'>Kontaktanfrage erfolgreich gelöscht.</div>";
        header('Refresh: 2; url=admin.php');
    } else {
        echo "<div class='alert alert-danger text-center'>Fehler beim Löschen: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

// Kontaktanfragen aus der Datenbank laden
$sql = "SELECT * FROM kontaktanfragen ORDER BY erstellt_am DESC";
$kontaktanfragen = $conn->query($sql);
?>

<?php include 'includes/header.php'; ?>

<main class="container my-5">
    <br>
    <span class="nav-link" style="color: white;"><p style="font-size: 36px;text-align: center;">Willkommen <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?>!</p>
        <ul style="list-style-type: none;margin-bottom: 20px;">
            <li class="nav-item" style="display: inline-block;">
<!--                 <form action="admin.php" method="post" class="d-inline nav-link">
                    <button type="submit" class="btn btn-danger">Adminbereich</button>
                </form> -->
            </li>
            <li class="nav-item" style="display: inline-block;">
<!--                 <form action="logout.php" method="post" class="d-inline nav-link">
                    <button type="submit" class="btn btn-danger">Abmelden</button>
                </form> -->
            </li>
        </ul>

    </span>

 
    <a href="upload.php" class="btn btn-success" style=" float: right;">Erstelle ein Fahrrad ></a>

    <!-- Liste der vorhandenen Karten -->
    <h2 class="text-center mt-5">Vorhandene Fahrräder</h2>
    <div class="row" id="sortable-cards">
        <?php
        $sql = "SELECT * FROM cards ORDER BY sort_order ASC";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="col-md-4 mb-4" data-id="' . $row['id'] . '" style="z-index: 1;">
                        <div class="card">
                            <img src="' . htmlspecialchars($row['image_name'], ENT_QUOTES, 'UTF-8') . '" class="card-img-top" alt="Bike">
                            <div class="card-body">
                                <h5 class="card-title">' . htmlspecialchars($row['titel'], ENT_QUOTES, 'UTF-8') . '</h5>
                                <p class="card-text">' . htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8') . '</p>
                            </div>
                            <div class="card-footer text-center">
                                <a href="edit.php?id=' . $row['id'] . '" class="btn btn-success">Bearbeiten</a>
                                <form action="delete.php" method="post" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">
                                    <input type="hidden" name="id" value="' . $row['id'] . '">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm(\'Sind Sie sicher, dass Sie dieses Fahrrad löschen möchten?\')">Löschen</button>
                                </form>
                            </div>
                        </div>
                      </div>';
            }
        } else {
            echo "<p>Keine Fahrräder verfügbar.</p>";
        }
        ?>
    </div>

    <?php
    echo "Das ist der Token:<br>";
    echo $_SESSION['csrf_token']; ?>
    <!-- Liste der Kontaktanfragen -->
    <h2 class="text-center mt-5">Kontaktanfragen</h2>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>E-Mail</th>
                    <th>Nachricht</th>
                    <th>Erstellt am</th>
                    <th>Status</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($kontaktanfragen->num_rows > 0) {
                    while ($row = $kontaktanfragen->fetch_assoc()) {
                        echo '<tr>
                                <td>' . $row['id'] . '</td>
                                <td>' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . '</td>
                                <td>' . htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') . '</td>
                                <td>' . htmlspecialchars($row['nachricht'], ENT_QUOTES, 'UTF-8') . '</td>
                                <td>' . $row['erstellt_am'] . '</td>
                                <td>
                                    <form action="admin.php" method="post" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">
                                        <input type="hidden" name="id" value="' . $row['id'] . '">
                                        <select name="status" onchange="this.form.submit()">
                                            <option value="Neu" ' . ($row['status'] == 'Neu' ? 'selected' : '') . '>Neu</option>
                                            <option value="In Bearbeitung" ' . ($row['status'] == 'In Bearbeitung' ? 'selected' : '') . '>In Bearbeitung</option>
                                            <option value="Bearbeitet" ' . ($row['status'] == 'Bearbeitet' ? 'selected' : '') . '>Bearbeitet</option>
                                        </select>
                                        <input type="hidden" name="update_status" value="1">
                                    </form>
                                </td>
                                <td>
                                    <form action="admin.php" method="post" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">
                                        <input type="hidden" name="id" value="' . $row['id'] . '">
                                        <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm(\'Sind Sie sicher, dass Sie diese Anfrage löschen möchten?\')">Löschen</button>
                                    </form>
                                </td>
                              </tr>';
                    }
                } else {
                    echo '<tr><td colspan="7">Keine Kontaktanfragen vorhanden.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    <br><br><br><br><br>
</main>

<?php include 'includes/footer.php'; ?>