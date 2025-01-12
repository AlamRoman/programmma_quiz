<?php 

    session_start();

    include "include/functions.ini";
    include "include/db.php";

    if (!isset($_SESSION["is_logged_in"]) || $_SESSION["is_logged_in"] == false) {
        go_to("login_page.php");
    }

    $punteggio = 0;

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET["id_sessione"])){
        $id_sessione = $_GET["id_sessione"];
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["id_sessione"]) && isset($_POST["invia"])) {
        $id_sessione = $_POST["id_sessione"];
        $id_user = $_SESSION["user_id"];
        unset($_POST['invia']);

        // Salva le risposte date
        foreach ($_POST["risposte"] as $id_domanda => $risposta_data) {
            $sql = "INSERT INTO risposte_date (id_sessione, id_studente, id_domanda, tipologia_domanda, risposta_data) 
                    VALUES (?, ?, ?, 
                    (SELECT tipo FROM domanda WHERE id = ?), ?) 
                    ON DUPLICATE KEY UPDATE risposta_data = ? ";
            $stmt = $conn->prepare($sql);

            $sql2 = "SELECT * from domanda WHERE id = ?";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param('i', $id_domanda);
            $stmt2->execute();
            $result = $stmt2->get_result();
            $domanda = $result->fetch_assoc();

            // Converti l'indice numerico per risposte multiple
            if ($domanda["tipo"] == "multipla") {

                echo $risposta_data;

                $verifica_risposta_data = "";

                list($temp, $verifica_risposta_data) = explode("_", $risposta_data);

                $sql2 = "SELECT * from risposta WHERE id = ?";
                $stmt2 = $conn->prepare($sql2);
                $stmt2->bind_param('i', $verifica_risposta_data);
                $stmt2->execute();
                $result = $stmt2->get_result();
                $risposta = $result->fetch_assoc();

                if ($risposta["corretta"] == 1) {
                    $punteggio += 1;
                }
                
                $risposta_data = $temp;
            }

            $stmt->bind_param("iiiiss", $id_sessione, $id_user, $id_domanda, $id_domanda, $risposta_data, $risposta_data);
            $stmt->execute();
        }

        $sql = "INSERT INTO risultati(id_sessione, id_studente, punteggio) VALUES (?,?,?);";
        $stmt = $conn->prepare(query: $sql);
        $stmt->bind_param("iii", $id_sessione, $id_user, $punteggio);
        $stmt->execute();

        // Redirect al riepilogo
        go_to("homeStudente.php");
        exit();
    } else {
        go_to("homeStudente.php");
        exit();
    }

    //prendi id test dalla sessione
    $sql = "SELECT * FROM sessione_test WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_sessione);
    $stmt->execute();
    $result = $stmt->get_result();
    $sessione = $result->fetch_assoc();

    $id_test = $sessione["id_test"];

    // Prendi il test dal database
    $sql = "SELECT * FROM test WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_test);
    $stmt->execute();
    $result = $stmt->get_result();
    $test = $result->fetch_assoc();

    // Prendi le domande del test
    $sql = "SELECT * FROM domanda WHERE id_test = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_test);
    $stmt->execute();
    $result = $stmt->get_result();
    $domande = $result->fetch_all(MYSQLI_ASSOC);

    // Prendi le risposte di ogni domanda
    for ($i = 0; $i < count($domande); $i++) { 
        $sql = "SELECT * FROM risposta WHERE id_domanda = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $domande[$i]["id"]);
        $stmt->execute();
        $result = $stmt->get_result();
        $risposte = $result->fetch_all(MYSQLI_ASSOC);

        $r = array();
        foreach ($risposte as $key => $risposta) {
            $risposta["indice"] = $key; // Assegna un indice numerico
            $r[] = $risposta;
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

    <link rel="stylesheet" href="css/textarea_grow.css"/>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

    <?php include "include/navbar-studente.php"; ?>

    <div class="ms-5 mt-5 pt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="homeStudente.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Svolgi test</li>
            </ol>
        </nav>
    </div>

    <div class="my-3" style="height: 100%;">
        <h2 class="fw-bold text-center mt-5"><?php echo $test["titolo"] ?></h2>
        <p class="text-center mb-5"><?php echo $test["descrizione"] ?></p>
        <form method="POST" action="svolgi_test.php" class="w-90 container mt-3 mb-3">
            <input type="hidden" name="id_sessione" value="<?php echo $id_sessione; ?>">
            <?php 
                foreach ($domande as $domanda) {
                    echo "<div class='card mb-3'>";
                    echo "<div class='card-body'>";
                    echo "<h5 class='card-title'>" . $domanda["testo"] . "</h5>";
                    foreach ($domanda["risposte"] as $key => $risposta) {
                        echo "<div class='form-check'>";
                        echo "<input class='form-check-input' type='radio' name='risposte[" . $domanda["id"] . "]' value='" . $key + 1 ."_".$risposta["id"]. "'>";
                        echo "<label class='form-check-label'>" . $risposta["testo"] . "</label>";
                        echo "</div>";
                    }
                    if ($domanda["tipo"] === "aperta") {
                        echo "<div class='grow-wrap'><textarea class='form-control' name='risposte[" . $domanda["id"] . "]' onInput=\"this.parentNode.dataset.replicatedValue = this.value\"></textarea></div>";
                    }
                    echo "</div></div>";
                }
            ?>
            <input type="submit" name="invia" class="btn btn-primary float-right my-5" value="Termina test">
        </form>
    </div>

    <script>
        const growers = document.querySelectorAll(".grow-wrap");

        growers.forEach((grower) => {
        const textarea = grower.querySelector("textarea");
        textarea.addEventListener("input", () => {
            grower.dataset.replicatedValue = textarea.value;
        });
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"></script>

</body>
</html>
