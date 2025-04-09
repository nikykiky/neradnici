<?php require_once("../sigurnost/sigurnosniKod.php"); ?>
<!DOCTYPE html>
<html>
<head>
<title>Pregled učenika</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1250" />
<link rel="stylesheet" type="text/css" href="../admin_css.css" />
<style>
.popup-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}
.popup-content {
    background: white;
    padding: 30px;
    border-radius: 10px;
    width: 500px;
    box-shadow: 0 0 20px rgba(0,0,0,0.3);
}
</style>
</head>
<body>
<div class="sve">
<?php require_once("../izbornik.php"); ?>
<h2>Pregled učenika</h2>

<?php

    //include("../sigurnost/spoj_na_bazu.php");
   /*
    $servername = "localhost";
    $username = "gogstorg_profesorica";
    $password = "U9Tqu$;%i4a7";
    $dbname = "gogstorg_zavrsni";
	$conn = new mysqli($servername, $username, $password, $dbname);
    */

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gogstorg_zavrsni";
$conn = new mysqli($servername, $username, $password, $dbname);

$id_ucenika = $_GET['id_ucenika'];
$id_korisnika = $_SESSION['user_id'];

// Brisanje bilješke
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $conn->query("DELETE FROM stsl_dosje_ucenika WHERE id_do=$delete_id");
    header("Location: " . $_SERVER['PHP_SELF'] . "?id_ucenika=" . $id_ucenika);
    exit;
}

// Ažuriranje bilješke
if (isset($_POST['spremi_izmjene'])) {
    $id_do = (int)$_POST['id_do'];
    $opis = mysqli_real_escape_string($conn, $_POST['dosje_opis']);
    $datum = mysqli_real_escape_string($conn, $_POST['datum_unosa_dosjea']);

    $conn->query("UPDATE stsl_dosje_ucenika SET opis='$opis', datum_unosa='$datum' WHERE id_do=$id_do");
    header("Location: " . $_SERVER['PHP_SELF'] . "?id_ucenika=" . $id_ucenika);
    exit;
}

// Dodavanje nove bilješke
if (isset($_POST['dodaj_dosje'])) {
    $dosje_opis = mysqli_real_escape_string($conn, $_POST['dosje_opis']);
    $datum_unosa_dosjea = mysqli_real_escape_string($conn, $_POST['datum_unosa_dosjea']);

    $query = "INSERT INTO stsl_dosje_ucenika (id_uc, id_ko, opis, datum_unosa) 
              VALUES ('$id_ucenika', '$id_korisnika', '$dosje_opis', '$datum_unosa_dosjea')";

    if (mysqli_query($conn, $query)) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?id_ucenika=" . $id_ucenika);
        exit;
    } else {
        echo "Greška pri unosu!";
    }
}

// Prikaz učenika
$pdtc_ucenika = mysqli_query($conn,"SELECT * FROM stsl_ucenik WHERE id_uc={$id_ucenika}");
$dosje_ucenika = mysqli_query($conn, "
    SELECT id_do, opis, DATE_FORMAT(datum_unosa,'%d.%m.%Y %H:%i') AS dan_upisa,
           datum_unosa, stsl_korisnik.ime AS korisnik 
    FROM stsl_ucenik 
    INNER JOIN stsl_dosje_ucenika ON stsl_ucenik.id_uc = stsl_dosje_ucenika.id_uc 
    INNER JOIN stsl_korisnik ON stsl_korisnik.id_ko = stsl_dosje_ucenika.id_ko 
    WHERE stsl_dosje_ucenika.id_uc={$id_ucenika}
");

while ($redak = mysqli_fetch_assoc($pdtc_ucenika)) {
    echo "Učenik: ".$redak['ime']." ".$redak['prezime']."<br />";
    echo "OIB: ".$redak['oib']."<br />";
    echo "Adresa: ".$redak['adresa']."<br />";
    echo "Grad: ".$redak['grad']."<br />";
    echo "Telefon 1: ".$redak['telefon']."<br />";
    echo "Telefon 2: ".$redak['telefon_ro2']."<br />";
}

echo "<h1>Bilješke</h1>";
echo "<table id='tbl_dosje_ucenika' border='1'>
    <thead>
        <tr valign='top'>
        <td width='40%'><b>Opis</b></td>
        <td width='15%'><b>Upisao</b></td>
        <td width='15%'><b>Datum</b></td>
        <td width='15%'><b>Akcije</b></td>
        </tr>
    </thead>";

while ($redak = mysqli_fetch_assoc($dosje_ucenika)) {
    $escaped_opis = htmlspecialchars($redak['opis'], ENT_QUOTES);
    $escaped_datum = date('Y-m-d', strtotime($redak['datum_unosa']));

    echo "<tr valign='top'><td>";
    echo $redak['opis'];
    echo "</td><td>";
    echo $redak['korisnik'];
    echo "</td><td>";
    echo $redak['dan_upisa'];
    echo "</td><td>";
    echo "<a href='#' onclick=\"otvoriModal('{$redak['id_do']}', '{$escaped_opis}', '{$escaped_datum}')\">Uredi</a> | ";
    echo "<a href='?id_ucenika={$id_ucenika}&delete_id={$redak['id_do']}' onclick=\"return confirm('Jesi li siguran da želiš obrisati bilješku?');\">Obriši</a>";
    echo "</td></tr>";
}
echo "</table>";
?>

<h4>Dodaj bilješku</h4>
<form action="" method="POST">
    Opis:<br/>
    <textarea rows="4" cols="50" name="dosje_opis"></textarea><br />
    Datum unosa:
    <input type="date" name="datum_unosa_dosjea" value="<?php echo date('Y-m-d'); ?>" /><br />
    <input type="submit" name="dodaj_dosje" value="Dodaj dosje"/>
</form>

<!-- MODAL ZA UREĐIVANJE -->
<div id="editModal" class="popup-overlay">
  <div class="popup-content">
    <h2>Uredi bilješku</h2>
    <form method="post">
      <input type="hidden" id="modal_id_do" name="id_do">
      <label>Opis:</label><br>
      <textarea name="dosje_opis" id="modal_opis" rows="5" cols="60"></textarea><br><br>
      <label>Datum unosa:</label><br>
      <input type="date" name="datum_unosa_dosjea" id="modal_datum"><br><br>
      <input type="submit" name="spremi_izmjene" value="Spremi promjene">
      <button type="button" onclick="zatvoriModal()">Odustani</button>
    </form>
  </div>
</div>

<script>
function otvoriModal(id, opis, datum) {
    document.getElementById("modal_id_do").value = id;
    document.getElementById("modal_opis").value = opis;
    document.getElementById("modal_datum").value = datum;
    document.getElementById("editModal").style.display = "flex";
}

function zatvoriModal() {
    document.getElementById("editModal").style.display = "none";
}
</script>

</div>
</body>
</html>
