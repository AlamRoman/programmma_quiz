<?php 

    session_start();

    include "include/functions.ini";
    include "include/db.php";

    if (!isset($_SESSION["is_logged_in"]) || $_SESSION["is_logged_in"] == false) {
        go_to("login_page.php");
    }

    if (isset($_POST['btn_salva'])) {

        $titolo = $_POST['titolo'];
        $id_classe = $_POST['id_classe'];
        $id_test = $_POST['id_test'];
        $data_inizio = $_POST['data_inizio'];
        $data_fine = $_POST['data_fine'];
    
        $sql = "INSERT INTO sessione_test (id_test, nome, id_classe, data_inizio, data_fine, stato, creato_da) VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stato = "";

        $current_date = date("Y-m-d H:i:s");

        if ($current_date < $data_inizio) {
            $stato = 'programmato'; 
        } elseif ($current_date >= $data_inizio && $current_date <= $data_fine) {
            $stato = 'in corso'; 
        } else {
            $stato = 'completato';
        }
    
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("isssssi", $id_test, $titolo, $id_classe, $data_inizio, $data_fine, $stato, $_SESSION["user_id"]);
    
            if ($stmt->execute()) {
                echo "<script>alert('Sessione test creata con successo!');</script>";
                $_SESSION["sessione_test_creato"] = 1;
            } else {
                $_SESSION["sessione_test_creato"] = 2;
            }
        } else {
            $_SESSION["sessione_test_creato"] = 3;
        }

        go_to("sessione_test.php");
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Docenti</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body>

    <?php include "include/navbar-docente.php"; ?>

    <div class="ms-5 mt-5 pt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item" aria-current="page"><a href="sessione_test.php">Lista sessioni</a></li>
                <li class="breadcrumb-item active" aria-current="page">Crea sessione test</li>
            </ol>
        </nav>
    </div>

    <div class="container mt-5 border p-5 d-flex flex-column gap-5">
        <h3 class="text-center">Crea sessione test</h3>
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST" class="d-flex flex-column gap-5">

            <div>
                <label for="titolo" class="form-label">Titolo sessione</label>
                <input class="form-control" type="text" id="titolo" name="titolo" placeholder="Titolo" required>
            </div>

            <div>
                <label for="id_classe" class="form-label">Seleziona Classe</label>
                <select class="form-select" id="id_classe" name="id_classe" required>
                    <option value="" disabled selected>Seleziona una classe</option>

                    <?php
                        $sql_classe = "SELECT id, nome, anno_inizio, anno_fine FROM classe";
                        $result_classe = $conn->query($sql_classe);
                        while ($row_classe = $result_classe->fetch_assoc()) {
                            echo "<option value='" . $row_classe['id'] . "'>" . $row_classe['nome'] . " [ " . $row_classe['anno_inizio'] . " - ". $row_classe['anno_fine'] . " ] " . "</option>";
                        }
                    ?>
                </select>
            </div>

            <div>
                <label for="id_test" class="form-label">Seleziona Test</label>
                <select class="form-select" id="id_test" name="id_test" required>
                    <option value="" disabled selected>Seleziona un test</option>

                    <?php
                        $sql_test = "SELECT id, titolo FROM test";
                        $result_test = $conn->query($sql_test);
                        while ($row_test = $result_test->fetch_assoc()) {
                            echo "<option value='" . $row_test['id'] . "'>" . $row_test['titolo'] . "</option>";
                        }
                    ?>
                </select>
            </div>

            <div>
                <label for="data_inizio" class="form-label">Data Inizio</label>
                <input class="form-control" type="datetime-local" id="data_inizio" name="data_inizio" required>
            </div>

            <div>
                <label for="data_fine" class="form-label">Data Fine</label>
                <input class="form-control" type="datetime-local" id="data_fine" name="data_fine" required>
            </div>

            <div class="ms-auto">
                <button class="btn btn-success my-auto" id="btn_salva" name="btn_salva"><i class="bi bi-floppy me-2"></i>Salva Sessione</button>
            </div>
        </form>
    </div>
    
</body>