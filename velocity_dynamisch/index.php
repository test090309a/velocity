<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<header class="hero-section" style="margin-top: -10%;">
    <div>
        <img src="bilder/hero_text.png" class="hero_text" type="image/png">
        <a href="#fahrraeder" class="btn btn-success btn-lg">Unsere Fahrräder</a>
    </div>
</header>

<?php
// Verbindung zur Datenbank
$conn = new mysqli('sql208.epizy.com', 'epiz_34327624', 'P5lOsuIC072rdn', 'epiz_34327624_velocity');
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// SQL-Abfrage um die Cards zu laden, sortiert nach sort_order
$sql = "SELECT * FROM cards ORDER BY sort_order ASC";
$result = $conn->query($sql);

// Formularverarbeitung
$messageSent = false;
$errorMessages = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Eingaben validieren
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    // Überprüfen, ob alle Felder ausgefüllt sind
    if (empty($name)) {
        $errorMessages[] = "Bitte geben Sie Ihren Namen ein.";
    }
    if (empty($email)) {
        $errorMessages[] = "Bitte geben Sie Ihre E-Mail-Adresse ein.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessages[] = "Bitte geben Sie eine gültige E-Mail-Adresse ein.";
    }
    if (empty($message)) {
        $errorMessages[] = "Bitte geben Sie eine Nachricht ein.";
    }

    // Wenn keine Fehler vorhanden sind, Daten in die Datenbank speichern
    if (empty($errorMessages)) {
        $name = $conn->real_escape_string($name);
        $email = $conn->real_escape_string($email);
        $message = $conn->real_escape_string($message);

        // SQL-Befehl zum Einfügen der Daten in die Tabelle
        $sql = "INSERT INTO kontaktanfragen (name, email, nachricht) VALUES ('$name', '$email', '$message')";

        if ($conn->query($sql) === TRUE) {
            $messageSent = true;
        } else {
            $errorMessages[] = "Fehler beim Speichern der Nachricht: " . $conn->error;
        }
    }
}
?>

<!-- Hauptinhalt -->
<main class="container my-5" style="padding-top: 80px;">

    <!-- Fahrräder -->
    <section id="fahrraeder" class="section">
        <h2 style="color: orange;text-transform: uppercase;">Unsere Fahrräder</h2>
        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="<?php echo $row['image_name']; ?>" class="card-img-top" alt="Bike" type="image/png">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $row['titel']; ?></h5>
                                <p class="card-text"><?php echo $row['description']; ?></p>
                            </div>
                            <div class="card-footer">
                                <!-- Optional: Platz für zusätzliche Elemente -->
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Keine Fahrräder verfügbar.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Services -->
    <section id="service" class="section">
        <h2 style="color: orange;text-transform: uppercase;">Unsere Services</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="bilder/service.jpg" class="card-img-top" alt="Service" type="image/jpg">
                    <div class="card-body">
                        <h5 class="card-title">Radservice</h5>
                        <p class="card-text">Professionelle Wartung und Pflege für Ihr Fahrrad.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="bilder/beratung.jpg" class="card-img-top" alt="Beratung" type="image/jpg">
                    <div class="card-body">
                        <h5 class="card-title">Beratung</h5>
                        <p class="card-text">Individuelle Beratung für das perfekte Fahrrad.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="bilder/reparatur.jpg" class="card-img-top" alt="Reparatur" type="image/jpg">
                    <div class="card-body">
                        <h5 class="card-title">Reparaturen</h5>
                        <p class="card-text">Schnelle und zuverlässige Reparaturen aller Art.</p>
                    </div>
                    <div class="card-footer">
                        <!-- Optional: Platz für zusätzliche Elemente -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Café -->
    <section id="cafe" class="section">
        <h2 style="color: orange;text-transform: uppercase;">Unser Café</h2>
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <img src="bilder/cafe.jpg" alt="Café" class="Café" type="image/jpg">
                    <div class="card-body">
                        <h5 class="card-title">Unser Café</h5>
                        <p class="card-text">Genießen Sie eine Tasse Kaffee oder Tee in unserem gemütlichen Café nach Ihrer Radtour. Unser Café bietet eine entspannte Atmosphäre, in der Sie sich mit Freunden treffen oder einfach nur die Seele baumeln lassen können.</p>
                    </div>
                    <div class="card-footer">
                        <!-- Optional: Platz für zusätzliche Elemente -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Kontakt -->
    <section id="kontakt" class="section">
        <h2 style="color: orange;text-transform: uppercase;">Kontaktieren Sie uns</h2>
        <div class="row">
            <div class="contact-form">
                <?php if ($messageSent): ?>
                    <div class="alert alert-success" role="alert">
                        Vielen Dank! Ihre Nachricht wurde erfolgreich gesendet.
                    </div>
                <?php else: ?>
                    <?php if (!empty($errorMessages)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php foreach ($errorMessages as $error): ?>
                                <p><?php echo $error; ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>#kontakt" method="POST" novalidate>
                        <div class="mb-3">
                            <input type="text" class="form-control" name="name" placeholder="Name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" name="email" placeholder="E-Mail" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                        <textarea class="form-control mb-3" name="message" placeholder="Ihre Nachricht" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                        <button type="submit" class="btn btn-success">Senden</button>
                    </form>
                <?php endif; ?>
            </div>
            <div class="map">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2658.091943200345!2d16.34604831565183!3d48.21164497923088!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x476d079b265aa8b9%3A0x1a9b1b1b1b1b1b1b!2sHernalser%20Hauptstra%C3%9Fe%2023%2C%201170%20Wien!5e0!3m2!1sde!2sat!4v1633021234567!5m2!1sde!2sat" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </section>

    <!-- Adresse und Öffnungszeiten -->
    <section id="adresse" class="section">
        <h2 style="color: orange;text-transform: uppercase;">Adresse & Öffnungszeiten</h2>
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Adresse</h5>
                        <p class="card-text">Hernalser Hauptstraße 23<br>1170 Wien</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Öffnungszeiten</h5>
                        <p class="card-text">Mo-Fr: 9-18 Uhr<br>Sa: 10-14 Uhr</p>
                    </div>
                    <div class="card-footer">
                        <!-- Optional: Platz für zusätzliche Elemente -->
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include 'includes/footer.php'; ?>