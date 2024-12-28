<?php
session_set_cookie_params([
    'lifetime' => 0, // Cookie läuft ab, wenn der Browser geschlossen wird
    'path' => '/',
    'domain' => '', // Leer lassen für die aktuelle Domain
    'secure' => true, // Nur über HTTPS
    'httponly' => true, // Verhindert Zugriff via JavaScript
    'samesite' => 'Strict' // Verhindert Cross-Site Request Forgery (CSRF)
]);
session_start();

// Passwort-Hashing
$hashed_password = password_hash('sysop', PASSWORD_DEFAULT);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $password = $_POST['password'];

    if ($username === 'admin' && password_verify($password, $hashed_password)) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['username'] = $username; // Benutzernamen in der Session speichern
        session_regenerate_id(true); // Session-ID regenerieren
        header('Location: admin.php');
        exit();
    } else {
        $error = "Ungültiger Benutzername oder Passwort.";
    }
}
?>

<?php include 'includes/header.php'; ?>

<!-- Login Formular -->
<div class="container my-5" style="max-width: 400px;min-width: 300px;padding-top: 73px;">

    <form method="post" action="">
        <div class="mb-3">
            <label for="username" class="form-label">Username:</label>
            <input type="text" class="form-control" id="username" name="username" autofocus required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
    <?php if (isset($error)) echo "<br><br><br><p style='color:red;'>$error</p>"; ?>

</div>
<br><br><br><br><br>
<?php include 'includes/footer.php'; ?>