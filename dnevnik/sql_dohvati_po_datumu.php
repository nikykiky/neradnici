<?php
    //include("../sigurnost/spoj_na_bazu.php");
   /* $servername = "localhost";
    $username = "gogstorg_profesorica";
    $password = "U9Tqu$;%i4a7";
    $dbname = "gogstorg_zavrsni";
    */
    $servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "gogstorg_zavrsni";
    $conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['datum'])) {
    $datum = $conn->real_escape_string($_POST['datum']);

    // Prvi upit: Dnevnik rada
    $sql_dnevnik = "SELECT * FROM stsl_dnevnik_rada WHERE datum_unosa LIKE ? ORDER BY datum_unosa";
    $stmt_dnevnik = $conn->prepare($sql_dnevnik);
    $datum_param = $datum . '%';
    $stmt_dnevnik->bind_param("s", $datum_param);
    $stmt_dnevnik->execute();
    $result_dnevnik = $stmt_dnevnik->get_result();

    $dnevnik_data = array();
    while ($row = $result_dnevnik->fetch_assoc()) {
        $dnevnik_data[] = array(
            'id' => $row["id_dr"],
            'opis' => $row["opis"],
            'datum_unosa' => $row["datum_unosa"]
        );
    }

    // Drugi upit: Bilješke
    $sql_biljeske = "SELECT 
                        stsl_ucenik.id_uc,
                        stsl_ucenik.ime AS ime,
                        stsl_ucenik.prezime AS prezime,
                        stsl_razred.oznaka_raz,
                        stsl_dosje_ucenika.opis
                    FROM stsl_ucenik
                    INNER JOIN stsl_ucenik_razred ON stsl_ucenik.id_uc = stsl_ucenik_razred.id_uc
                    INNER JOIN stsl_razred ON stsl_razred.id_raz = stsl_ucenik_razred.id_ra
                    INNER JOIN stsl_dosje_ucenika ON stsl_dosje_ucenika.id_uc = stsl_ucenik.id_uc
                    WHERE stsl_dosje_ucenika.datum_unosa LIKE ?";
    $stmt_biljeske = $conn->prepare($sql_biljeske);
    $stmt_biljeske->bind_param("s", $datum_param);
    $stmt_biljeske->execute();
    $result_biljeske = $stmt_biljeske->get_result();

    $biljeske_data = array();
    while ($row = $result_biljeske->fetch_assoc()) {
        $biljeske_data[] = array(
            'id_uc' => $row["id_uc"],
            'ime' => $row["ime"],
            'prezime' => $row["prezime"],
            'oznaka_raz' => $row["oznaka_raz"],
            'opis' => $row["opis"]
        );
    }

    // JSON odgovor s oba skupa podataka
    echo json_encode(array('dnevnik' => $dnevnik_data, 'biljeske' => $biljeske_data));

    // Zatvaranje priprema i veze
    $stmt_dnevnik->close();
    $stmt_biljeske->close();
} else {
    echo json_encode("Invalid input");
}
$conn->close();
?>
