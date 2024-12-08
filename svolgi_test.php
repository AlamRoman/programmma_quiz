<?php 

    session_start();

    include "include/functions.ini";
    include "include/db.php";

    if (!isset($_SESSION["is_logged_in"]) || $_SESSION["is_logged_in"] == false) {
        go_to("login_page.php");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET["id_test"])){
        $id_test = $_GET["id_test"];
    }else{
        go_to("studenti.php");
        exit();
    }

    //prendi il test da database
    $sql = "SELECT * FROM test WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_test);
    $stmt->execute();
    $result = $stmt->get_result();
    $test = $result->fetch_assoc();

    //prendi le domande del test
    $sql = "SELECT * FROM domanda WHERE id_test = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_test);
    $stmt->execute();
    $result = $stmt->get_result();
    $domande = $result->fetch_all(MYSQLI_ASSOC);

    //prendi le risposte di ogni domanda
    for ($i=0; $i < count($domande); $i++) { 
        $sql = "SELECT * FROM risposta WHERE id_domanda = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $domande[$i]["id"]);
        $stmt->execute();
        $result = $stmt->get_result();
        $risposte = $result->fetch_all(MYSQLI_ASSOC);

        $r = array();

        foreach($risposte as $risposta){
            $r[] = $risposta["testo"];
        }

        $domande[$i]["risposte"] = $r;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Svolgi - test</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid d-flex justify-content-between">

            <h3 class="fw-bold">Benvenuto <?php echo $_SESSION["username"]; ?>!</h3>

            <h4 class="fw-bold text-center" style="transform: translateX(-50%);">Svolgi Test</h4>

            <form class="d-flex" method="POST" action="php/do_logout.php">
                <input class="btn btn-secondary ms-auto" type="submit" id="logout" name="logout" value="logout">
            </form>
        </div>
    </nav>

    <div class="mt-3">

        <h2 class="fw-bold text-center my-5"><?php echo $test["titolo"] ?></h2>

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