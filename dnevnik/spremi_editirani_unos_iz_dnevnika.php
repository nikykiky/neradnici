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
	$dbname = "dnevnik_rada_psiholog";
    $conn = new mysqli($servername, $username, $password, $dbname);

    $result = "UPDATE stsl_dnevnik_rada SET opis='$_POST[opis_dnevnik_rada]' where id_dr='$_POST[id_unosa_za_edit]'";
    $sql = mysqli_query($conn,$result);
    if (mysqli_affected_rows($conn) == 1) {
        echo "Promijene unesene.";
    }
    else {
        echo "Niste ništa mijenjali.";
    }
?>