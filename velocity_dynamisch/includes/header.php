<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeloCity Bikes&Bohnen</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <!-- Google Fonts: Rubik -->
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;700&display=swap" rel="stylesheet" type="text/css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="stile/styles.css" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
</head>

<body>
    <?php
    session_start();
    ?>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="navbar">
        <div class="container">
            <a href="index.php"><img src="./bilder/logo.png" alt="Logo" class="header-logo" type="image/png"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="./index.php#fahrraeder">Fahrräder</a></li>
                    <li class="nav-item"><a class="nav-link" href="./index.php#service">Service</a></li>
                    <li class="nav-item"><a class="nav-link" href="./index.php#cafe">Café</a></li>
                    <li class="nav-item"><a class="nav-link" href="./index.php#kontakt">Kontakt</a></li>
                    <?php if (!isset($_SESSION['admin_logged_in'])): ?>
                        <li class="nav-item"><a class="nav-link" href="./login.php">Login</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="./logout.php" class="btn btn-danger">Logout</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>