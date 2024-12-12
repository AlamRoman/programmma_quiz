<?php 

    session_start();

    include "include/functions.ini";
    include "include/db.php";

    if (!isset($_SESSION["is_logged_in"]) || $_SESSION["is_logged_in"] == false) {
        go_to("login_page.php");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET["id_test"])){
        $id_test = $_GET["id_test"];
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["id_test"]) && isset($_POST["invia"])) {
        $id_test = $_POST["id_test"];
        $id_user = $_SESSION["user_id"];
        unset($_POST['invia']);

        // Salva le risposte date
        foreach ($_POST["risposte"] as $id_domanda => $risposta_data) {
            $sql = "INSERT INTO risposte_date (id_test, id_user, id_domanda, tipologia_domanda, risposta_data) 
                    VALUES (?, ?, ?, 
                    (SELECT tipo FROM domanda WHERE id = ?), ?) 
                    ON DUPLICATE KEY UPDATE risposta_data = ?";
            $stmt = $conn->prepare($sql);

            // Converti l'indice numerico per risposte multiple
            if (is_array($risposta_data)) {
                $risposta_data = implode(",", array_map('strval', $risposta_data));
            }

            $stmt->bind_param("iiiiss", $id_test, $id_user, $id_domanda, $id_domanda, $risposta_data, $risposta_data);
            $stmt->execute();
        }

        // Redirect al riepilogo
        go_to("studenti.php");
        exit();
    } else {
        go_to("studenti.php");
        exit();
    }

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

    <div class="ms-5 mt-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="studenti.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Svolgi test</li>
            </ol>
        </nav>
    </div>

    <div class="my-3" style="height: 100%;">
        <h2 class="fw-bold text-center my-5"><?php echo $test["titolo"] ?></h2>
        <form method="POST" action="svolgi_test.php" class="w-90 container mb-3">
            <input type="hidden" name="id_test" value="<?php echo $id_test; ?>">
            <?php 
                foreach ($domande as $domanda) {
                    echo "<div class='card mb-3'>";
                    echo "<div class='card-body'>";
                    echo "<h5 class='card-title'>" . $domanda["testo"] . "</h5>";
                    foreach ($domanda["risposte"] as $key => $risposta) {
                        echo "<div class='form-check'>";
                        echo "<input class='form-check-input' type='radio' name='risposte[" . $domanda["id"] . "]' value='" . $key+1 . "'>";
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script> 
</body>
</html>
