<?php
session_start();

// Session zerstören
session_unset();
session_destroy();

// Zur Login-Seite umleiten
header('Location: login.php');
exit();
?>