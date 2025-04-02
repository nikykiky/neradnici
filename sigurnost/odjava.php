<?php
// Startaj sesiju na početku
session_start();

// Uništi sve sesijske promenljive
session_unset();

// Uništi sesiju
session_destroy();

// Redirektuj na login stranicu
header("Location: ../sigurnost/login.php");

// Završavaj skriptu kako bi osigurao da ne bude više izvršavanja koda
exit();
?>
