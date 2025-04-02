<?php
        //include("../sigurnost/spoj_na_bazu.php");
        /*$servername = "localhost";
        $username = "gogstorg_profesorica";
        $password = "U9Tqu$;%i4a7";
        $dbname = "gogstorg_zavrsni";
        */
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "gogstorg_zavrsni";
        $conn = new mysqli($servername, $username, $password, $dbname);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    if (isset($_POST['id_unosa_za_brisanje'])) {
        $id_b = $_POST['id_unosa_za_brisanje'];
    
        $brisanje = "DELETE FROM stsl_dnevnik_rada WHERE id_dr = $id_b";
        $querry = mysqli_query($conn, $brisanje);
    
        if ($querry && mysqli_affected_rows($conn) > 0) {
            print "Promjene su unesene u tablicu.";
        } else {
            print "Niste ništa mijenjali ili se dogodila greška pri brisanju.";
        }
    } else {
        print "Niste naveli id_b za brisanje.";
    }
    
    $con->close();
?>
        