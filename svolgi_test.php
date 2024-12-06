<?php 

    session_start();

    include "include/functions.ini";

    if (!isset($_SESSION["is_logged_in"]) || $_SESSION["is_logged_in"] == false) {
        go_to("login_page.php");
    }

    $domande = array();

    $domande[] = [
        "nDomanda"=> 1,
        "testo"=> "Come si calcola l'area del quadrato",
        "tipo"=> "multipla",
        "risposte"=> ["b*h/2", "l*l", "PI*r*r", "non lo so"],
        "corretta"=> 2
    ];

    $domande[] = [
        "nDomanda"=> 2,
        "testo"=> "Scrivi un polinomio",
        "tipo"=> "aperta",
        "risposte"=> "",
        "corretta"=> ""
    ];

    $domande[] = [
        "nDomanda"=> 3,
        "testo"=> "Come si calcola l'area del quadrato",
        "tipo"=> "multipla",
        "risposte"=> ["b*h/2", "l*l", "PI*r*r", "non lo so"],
        "corretta"=> 2
    ];

    $domande[] = [
        "nDomanda"=> 4,
        "testo"=> "Scrivi un polinomio",
        "tipo"=> "aperta",
        "risposte"=> "",
        "corretta"=> ""
    ];


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Svolgi - test</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid d-flex justify-content-between">

            <h3 class="fw-bold">Benvenuto <?php echo $_SESSION["username"]; ?>!</h3>

            <h4 class="fw-bold text-center" style="transform: translateX(-50%);">Svolgi Test</h4>

            <form class="d-flex" method="POST" action="php/do_logout.php">
                <input class="btn btn-primary ms-auto" type="submit" id="logout" name="logout" value="logout">
            </form>
        </div>
    </nav>

    <div class="mt-3">

        <h2 class="fw-bold text-center my-5">Test matematica</h2>

        <div class="d-flex flex-column w-90">
            <?php 
                echo crea_card_da_domande($domande);

            ?>

            <form action="#" class="mb-5 mt-3 mx-auto w-75">
                <input type="submit" class="btn btn-primary float-right" value="Termina test">
            </form>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script> 
</body>
</html>