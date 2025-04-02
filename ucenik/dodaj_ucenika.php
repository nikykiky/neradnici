<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>Dodaj učenika</title>
<meta http-equiv="Content-Type" content="text/html"/>
<meta charset="utf-8">
<link href="admin_css.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="sve">
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


	// Omogućite prikaz grešaka
	error_reporting(E_ALL);
	ini_set('display_errors', 1);



    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
	else{
		//if (isset($_POST['sbmt_dodaj_ucenika'])) {
			$ime_ucenika = $_POST['ime_ucenika'];
			$prezime_ucenika = $_POST['prezime_ucenika'];
			$oib_ucenika = $_POST['oib_ucenika'];
			//provjera je li datum prazan, inače bind neće radit
			$datum_rodenja = !empty($_POST['datum_rodenja']) ? $_POST['datum_rodenja'] : null;
			$adresa_ucenika = $_POST['adresa_ucenika'];
			$grad_ucenika = $_POST['grad_ucenika'];
			$spol_ucenika = $_POST['spol_ucenika'];
			$rjesenje_ucenika = $_POST['rjesenje_ucenika'];
			$klasa_ucenika = $_POST['klasa_ucenika'];
			$ime_oca = $_POST['ime_oca'];
			$mob_oca = $_POST['mob_oca'];
			$ime_majke = $_POST['ime_majke'];
			$mob_majke = $_POST['mob_majke'];
			$id_razreda = $_POST['id_razreda'];
			$id_sk_god = $_POST['id_sk_god'];
		
			// Insert data into the respective tables using prepared statements
			$stmt_ucenik = $conn->prepare("INSERT INTO stsl_ucenik (ime, prezime, oib, datum_rodenja, adresa, grad, spol, rjesenje, klasa) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$stmt_ucenik->bind_param("ssissssss", $ime_ucenika, $prezime_ucenika, $oib_ucenika, $datum_rodenja, $adresa_ucenika, $grad_ucenika, $spol_ucenika, $rjesenje_ucenika, $klasa_ucenika);
		
			if ($stmt_ucenik->execute()) {
				$id_ucenika = $conn->insert_id;
					
				$stmt_ucenik_razred = $conn->prepare("INSERT INTO stsl_ucenik_razred (id_ra, id_uc, id_skgod) VALUES (?, ?, ?)");
				$stmt_ucenik_razred->bind_param("iii", $id_razreda, $id_ucenika, $id_sk_god);
		
				if ($stmt_ucenik_razred->execute()) {
					echo "Uspješno uneseno!<br /><br />";
				} else {
					echo "Nije unesen ucenik razred!<br /><br />";
				}
				$stmt_ucenik_razred->close();
			} else {
				echo "Greška prilikom unosa učenika: " . $stmt_ucenik->error . "<br /><br />";
			}


			$stmt_otac = $conn->prepare("INSERT INTO stsl_roditelj (ime, telefon) VALUES (?, ?)");
			$stmt_otac->bind_param("ss", $ime_oca, $mob_oca);
		
			$stmt_majka = $conn->prepare("INSERT INTO stsl_roditelj (ime, telefon) VALUES (?, ?)");
			$stmt_majka->bind_param("ss", $ime_majke, $mob_majke);
		
			if($stmt_otac->execute() && $stmt_majka->execute()){
				$id_oca = $conn->insert_id;
				$id_majke = $conn->insert_id;
				// Insert data into roditelj_dijete and ucenik_razred tables
				$stmt_roditelji = $conn->prepare("INSERT INTO stsl_roditelj_dijete (id_ro, id_uc) VALUES (?, ?), (?, ?)");
				$stmt_roditelji->bind_param("iiii", $id_oca, $id_ucenika, $id_majke, $id_ucenika);
				if ($stmt_roditelji->execute()) {
					echo "Uspješno uneseno!<br /><br />";
				} else {
					echo "Nije unesen roditelj!<br /><br />";
				}
				$stmt_roditelji->close();
			}
		
			$stmt_ucenik->close();
			$stmt_otac->close();
			$stmt_majka->close();
			
			$conn->close();
		//}
	}
    ?>
