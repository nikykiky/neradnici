<?php require_once("../sigurnost/sigurnosniKod.php"); ?>
<!DOCTYPE html>
<head>
<title>Pregled ucenika</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1250" />
<link rel="stylesheet" type="text/css" href="../admin_css.css" />
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
    */
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "gogstorg_zavrsni";
    $conn = new mysqli($servername, $username, $password, $dbname);


	$id_ucenika = $_GET['id_ucenika'];
	$id_korisnika = $_SESSION['user_id'];
	$pdtc_ucenika = mysqli_query($conn,"SELECT * FROM stsl_ucenik WHERE id_uc={$id_ucenika}");
    $dosje_ucenika = mysqli_query($conn, "SELECT opis, DATE_FORMAT(datum_unosa,' %d.%c.%Y %H:%i') AS dan_upisa, stsl_korisnik.ime AS korisnik FROM stsl_ucenik INNER JOIN stsl_dosje_ucenika ON stsl_ucenik.id_uc = stsl_dosje_ucenika.id_uc INNER JOIN stsl_korisnik ON stsl_korisnik.id_ko = stsl_dosje_ucenika.id_ko WHERE stsl_dosje_ucenika.id_uc={$id_ucenika}");
   
	//if($_SERVER['REQUEST_METHOD'] === 'POST')
	while ($redak = mysqli_fetch_assoc($pdtc_ucenika)) {
		echo "Ucenik: ".$redak['ime']." ".$redak['prezime']."<br />";
		echo "Oib: ".$redak['oib']."<br />";
		echo "Adresa:".$redak['adresa']."<br />";
		echo "Grad:".$redak['grad']."<br />";
		echo "<input type='hidden' name='id_uc' value='".$redak['id_uc']."'>";
	}
	
	

	echo "<h1>Bilješka</h1>";
	echo "<table id='tbl_dosje_ucenika' border='1'>
		<thead>
			<tr valign='top'>
			<td width='70%'><b>Opis</b></td>
			<td width='15%'><b>Upisao</b></td>
			<td width='15%'><b>Datum</b></td>
			</tr>
		</thead>";
    while ($redak = mysqli_fetch_assoc($dosje_ucenika)) {
		echo "<tr valign='top''><td>";
        echo $redak['opis'];
        echo "</td><td>";
        echo $redak['korisnik'];
        echo "</td><td>";
		echo $redak['dan_upisa'];
		echo "</td></tr>";
	}
	echo "</table>";
?>


<h4>Dodaj bilješku</h4>
<form action="" method="POST">
	Opis: 
	<textarea rows="4" cols="50" name="dosje_opis"></textarea><br />
	Datum unosa: <input type="date" name="datum_unosa_dosjea" value="<?php echo date('Y-m-d'); ?>" /><br />
	<input type="submit" name="dodaj_dosje" value="Dodaj dosje"/>
</form>

<?php
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
	else{
		if (isset($_POST['dodaj_dosje'])) {
			$dosje_opis = mysqli_real_escape_string($conn, $_POST['dosje_opis']);
			$datum_unosa_dosjea = mysqli_real_escape_string($conn, $_POST['datum_unosa_dosjea']);
		
			$query = "INSERT INTO stsl_dosje_ucenika (id_uc, id_ko, opis, datum_unosa) VALUES ('$id_ucenika', '$id_korisnika', '$dosje_opis', '$datum_unosa_dosjea')";
		
			if (mysqli_query($conn, $query)) {
				echo "Uspješno uneseno!<br /><br />";
				header("Location: " . $_SERVER['PHP_SELF'] . "?id_ucenika=" . $id_ucenika);
				exit;
			} else {
				echo "Nije uneseno!<br /><br />";
			}
			mysqli_close($conn);
		}
	}
    ?>

</div>
</body>
</html>

