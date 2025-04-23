<?php 
    require_once("../sigurnost/sigurnosniKod.php"); 

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "gogstorg_zavrsni";
    $conn = new mysqli($servername, $username, $password, $dbname);

    $danasnji_datum = date("Y-m-d");
    $korisnik = $_SESSION['user_id']; // The logged-in user

    // Insert new dnevnik rada
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sbmt_dnevnik_rad'])) {
        $opis = $_POST["opis_dnevnik_rada"];
        $rezultat = "INSERT INTO stsl_dnevnik_rada (id_ko, opis) values('$korisnik','$opis')";
        if (mysqli_query($conn, $rezultat)) {
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Error: " . $rezultat . "<br>" . mysqli_error($conn);
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dnevnik rada</title>
    <link href="../admin_css.css" rel="stylesheet" type="text/css" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.2/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>

    <style>
        @media print {
            .unos_dnevnika, header, #printButton {
                display: none;
            }
        }
    </style>
</head>
<body>
<div class="sve">
    <?php require_once("../izbornik.php"); ?>

    <h2>Dnevnik rada</h2>
    <button id="printButton">Printaj stranicu</button>

    <div class="unos_dnevnika">
        <form action="" method="POST">
            <input type="text" name="id_korisnika" value="<?= $_SESSION['user_id'] ?>" style="display:none" />
            Opis: <br />
            <textarea rows="3" cols="5" name="opis_dnevnik_rada" ></textarea>
            <br />
            <input type="submit" value="Dodaj dnevnik rada" name="sbmt_dnevnik_rad" />
        </form>
    </div>

    <?php
        // Fetch all dnevnik rada entries (no filter based on user)
        $pdtc_dnevnik_rada = mysqli_query($conn, "
            SELECT * FROM stsl_dnevnik_rada
            INNER JOIN stsl_korisnik ON stsl_korisnik.id_ko = stsl_dnevnik_rada.id_ko
            WHERE datum_unosa LIKE '$danasnji_datum%'
        ");

        echo "<h3 style='display: inline-block;'>Pregled dnevnika rada za datum: <span id='odabrani_datum'>".date("d-m")."</span></h3>";
        echo "<input type='text' id='datepicker'>";
        echo "<table id='tablica_dnevnika_rada' border='1'>
                <thead>
                    <tr valign='top'>
                    <td width='50%'><b>Dnevnik rada</b></td>
                    <td width='20%'><b>Upisao</b></td>
                    <td width='15%'><b>Izmjeni</b></td>
                    <td width='15%'><b>Obrisi</b></td>
                    </tr>
                </thead>
                <tbody>";

        while ($redak = mysqli_fetch_array($pdtc_dnevnik_rada)) {
            $id = $redak['id_dr'];
            $dt = new DateTime($redak['datum_unosa']);
            $vrijeme = $dt->format('H:i');

            echo "<tr valign='top' data-id='".$id."'><td>";
            echo $redak['opis'];
            echo "</td><td>";
            echo $redak['ime'] . " " . $vrijeme;
            echo "</td><td>";

            // Check if the logged-in user is the owner of the entry
            if ($redak['id_ko'] == $korisnik) {
                echo "<a onclick='uredi_unos_iz_dnevnika(this)' style='text-decoration: underline; cursor: pointer' data-dr_opis='$redak[opis]' data-dr_id='$id' data-owner-id='{$redak['id_ko']}'>Uredi</a>";
            } else {
                echo "<span style='color: gray;'>Nema prava</span>";
            }

            echo "</td><td>";
            // Check if the logged-in user is the owner of the entry
            if ($redak['id_ko'] == $korisnik) {
                echo "<a onclick='izbrisi_unos_iz_dnevnika(this)' style='text-decoration: underline; cursor: pointer' data-dr_id='$id' data-owner-id='{$redak['id_ko']}'>Izbrisi</a>";
            } else {
                echo "<span style='color: gray;'>Nema prava</span>";
            }

            echo "</td></tr>";
        }
        echo "</tbody></table>";

        // Select bilješke entries for the logged-in user
        $query_uco_bilj = "
            SELECT
            stsl_ucenik.id_uc,
            stsl_ucenik.ime AS ime,
            stsl_ucenik.prezime AS prezime,
            stsl_ucenik.oib,
            stsl_ucenik.datum_rodenja,
            stsl_ucenik.adresa,
            stsl_ucenik.grad,
            stsl_ucenik.spol,
            stsl_ucenik_razred.id_ra AS razred_id,
            stsl_razred.oznaka_raz,
            opis
            FROM 
            stsl_ucenik
            INNER JOIN 
            stsl_ucenik_razred ON stsl_ucenik.id_uc = stsl_ucenik_razred.id_uc
            INNER JOIN 
            stsl_razred ON stsl_razred.id_raz = stsl_ucenik_razred.id_ra
            INNER JOIN 
            stsl_dosje_ucenika ON stsl_dosje_ucenika.id_uc = stsl_ucenik.id_uc
            WHERE datum_unosa LIKE '$danasnji_datum%' AND stsl_dosje_ucenika.id_ko = '$korisnik'
        ";
        $pdtc_biljeske = mysqli_query($conn, $query_uco_bilj);

        echo "<h3 style='display: inline-block;'>Upisane bilješke na dan: <span id='odabrani_datum'>".date("d-m")."</span></h3>";
        echo "<table id='tablica_biljeske' border='1'>
                <thead>
                    <tr valign='top'>
                    <td width='20%'><b>Razred</b></td>
                    <td width='30%'><b>Učenik</b></td>
                    <td width='50%'><b>Bilješka</b></td>
                    </tr>
                </thead>
                <tbody>";

        while ($redak_bilj = mysqli_fetch_assoc($pdtc_biljeske)) {
            echo "<tr valign='top' data-id='".$redak_bilj['id_uc']."'><td>";
            echo $redak_bilj['oznaka_raz'];
            echo "</td><td>";
            echo '<a href="../ucenik/pregled_ucenika.php?id_ucenika=' . $redak_bilj['id_uc'] . '">';
            echo $redak_bilj['ime'] . " " . $redak_bilj['prezime'];
            echo '</a>';
            echo "</td><td>";
            echo $redak_bilj['opis'];
            echo "</td></tr>";
        }
        echo "</tbody></table>";
    ?>
</div>

<div id="dialog" title="Uredivanje unosa" style="display: none;">
    <textarea id="uredi_unos" style="height:100%;padding:5px; font-family:Sans-serif; font-size:1.2em;"></textarea>
    <input type="text" style="display: none" />
</div>

<script>
    $("#printButton").click(function() {
        window.print();
    });

    function izbrisi_unos_iz_dnevnika(obj) {
        var id_delete_dnevnika_rada = obj.getAttribute('data-dr_id');
        var loggedInUserId = <?= $_SESSION['user_id']; ?>;
        var recordOwnerId = obj.getAttribute('data-owner-id');

        if (loggedInUserId == recordOwnerId && confirm("Jesi sigurna :/") == true) {
            $.ajax({
                type: "POST",
                url: "sql_izbrisi_iz_dnevnika.php",
                data: {"id_unosa_za_brisanje" : id_delete_dnevnika_rada},
                success: function () {
                    location.reload(); 
                }
            });
        } else {
            alert("Nemate dozvolu za brisanje ovog unosa.");
        }
    }

    $("#dialog").dialog({
        autoOpen: false,
        height: 400,
        width: 450,
        modal: true,
        resizable: true,
        buttons: {
            "Unesi": function() {
                var unos = $('#dialog').find("textarea").val();
                var id_unosa_za_edit = $('#dialog').find("input").val();
                $.ajax({
                    type: "POST",
                    url: "spremi_editirani_unos_iz_dnevnika.php",
                    data: {"opis_dnevnik_rada" : unos, "id_unosa_za_edit" : id_unosa_za_edit },
                    success: function () {
                        var redak = $("#tablica_dnevnika_rada tbody tr[data-id='" + id_unosa_za_edit + "']");
                        redak.find("td").eq(0).text(unos); 
                    }
                });
                $(this).dialog("close");
            },
            "Odustani": function() {
                $(this).dialog("close");
            }
        }
    });

    function uredi_unos_iz_dnevnika(obj) {
        var id_unosa_za_edit = obj.getAttribute('data-dr_id');
        var opis_dnevnika_rada = obj.getAttribute('data-dr_opis');
        var loggedInUserId = <?= $_SESSION['user_id']; ?>;
        var recordOwnerId = obj.getAttribute('data-owner-id');

        if (loggedInUserId == recordOwnerId) {
            $('#dialog').find("textarea").val(opis_dnevnika_rada);
            $('#dialog').find("input").val(id_unosa_za_edit);
            $('#dialog').dialog('open');
        } else {
            alert("Nemate dozvolu za uređivanje ovog unosa.");
        }
    }
</script>

</body>
</html>
