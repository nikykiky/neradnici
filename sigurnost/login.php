<?php
session_start();

// Omogućite prikaz grešaka
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["sbmt_login"])) {

    /*
    $servername = "localhost";
    $username = "gogstorg_profesorica";
    $password = "U9Tqu$;%i4a7";
    $dbname = "gogstorg_zavrsni";
    */
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "gogstorg_zavrsni";
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Provera konekcije
    if ($conn->connect_error) {
        die("Neuspješna konekcija: " . $conn->connect_error);
    }

    // Sanacija korisničkog unosa
    $korisnik = $conn->real_escape_string(trim($_POST["korisnik"]));
    $zaporka = trim($_POST["zaporka"]);

    // Pripremljeni upit
    $stmt = $conn->prepare("SELECT id_ko, korisnicko_ime, lozinka FROM stsl_korisnik WHERE korisnicko_ime = ? LIMIT 1");

    if ($stmt === false) {
        die('Greška u pripremi SQL upita: ' . $conn->error);
    }

    $stmt->bind_param("s", $korisnik);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user_data = $result->fetch_assoc();

        // Verifikacija lozinke
        if (password_verify($zaporka, $user_data['lozinka'])) {
            $_SESSION['user'] = $user_data['korisnicko_ime'];
            $_SESSION['user_id'] = $user_data['id_ko'];

            // Redirekcija
            header("Location: ../dnevnik/dnevnik_rada.php");
            exit; // Ne zaboravite exit nakon header
        } else {
            echo "<h3>Neispravno korisničko ime ili lozinka</h3>";
        }
    } else {
        echo "<h3>Neispravno korisničko ime ili lozinka</h3>";
    }

    // Zatvaranje konekcije
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="../admin_css.css" type="text/css" />
</head>
<body>

<div class="sve logiranje">
    <h1>Sustav za stručnu službu</h1>
    <br /><br />
    <form name="loginForma" method="post" action="./login.php">
        <h3>Korisničko ime:</h3>
        <input type="text" name="korisnik"><br />
        <h3>Lozinka:</h3>
        <input type="password" name="zaporka"><br />
        <input type="submit" name="sbmt_login" value="Login">
    </form>
</div>

<script>
    window.onload = function() {
        document.loginForma.korisnik.focus();
    };
</script>
</body>
</html>
