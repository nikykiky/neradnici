<?php
session_start();
require_once("../sigurnost/sigurnosniKod.php");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gogstorg_zavrsni";
$conn = new mysqli($servername, $username, $password, $dbname);


$id_unosa = $_POST['id_unosa_za_edit'];
$opis = $_POST['opis_dnevnik_rada'];
$korisnik_id = $_SESSION['user_id'];


$provjera_sql = "SELECT id_ko FROM stsl_dnevnik_rada WHERE id_dr = ?";
$provjera_stmt = $conn->prepare($provjera_sql);
$provjera_stmt->bind_param("i", $id_unosa);
$provjera_stmt->execute();
$provjera_stmt->bind_result($vlasnik_id);
$provjera_stmt->fetch();
$provjera_stmt->close();

if ($vlasnik_id != $korisnik_id) {
    http_response_code(403);
    echo "Nemate ovlasti za uređivanje ovog unosa.";
    exit();
}


$update_sql = "UPDATE stsl_dnevnik_rada SET opis=? WHERE id_dr=?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("si", $opis, $id_unosa);
$update_stmt->execute();

if ($update_stmt->affected_rows == 1) {
    echo "Promjene su uspješno unesene.";
} else {
    echo "Nema promjena.";
}

$update_stmt->close();
$conn->close();
?>
