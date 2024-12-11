<?php 

    session_start();

    include "include/functions.ini";
    include "include/db.php";

    if (!isset($_SESSION["is_logged_in"]) || $_SESSION["is_logged_in"] == false) {
        go_to("login_page.php");
    }

    $sql = "SELECT id, titolo, descrizione FROM test";
    $result = $conn->query($sql);

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

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid d-flex justify-content-between">

            <h3 class="fw-bold">Benvenuto <?php echo $_SESSION["username"]; ?>!</h3>

            <h4 class="fw-bold text-center" style="transform: translateX(-50%);">Home</h4>

            <form class="d-flex" method="POST" action="php/do_logout.php">
                <input class="btn btn-secondary ms-auto" type="submit" id="logout" name="logout" value="logout">
            </form>
        </div>
    </nav>

    <div class="ms-5 mt-3">
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active" aria-current="page">Home</li>
    </ol>
    </nav>
    </div>

    <?php 
        if(isset($_GET["testCreato"]) && $_GET["testCreato"]==1){
            echo '<div class="container mt-5"><div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i> Nuovo test creato !
                    <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
                </div></div>';
        }

        if(isset($_SESSION["testModificato"])){

            if($_SESSION["testModificato"]==1){
                echo '<div class="container mt-5"><div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i> Test modificato correttamente !
                    <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
                </div></div>';
            }else{
                echo '<div class="container mt-5"><div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-x-circle-fill"></i> Errore durante la modifica del test
                        <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
                    </div></div>';
            }
            
            unset($_SESSION["testModificato"]);
        }

        if (isset($_GET["deleted"])) {
            if ($_GET["deleted"] == 1) {
                echo '<div class="container mt-5"><div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill"></i> Test eliminato con successo!
                        <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
                    </div></div>';
            } elseif ($_GET["deleted"] == 0) {
                echo '<div class="container mt-5"><div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-x-circle-fill"></i> Errore durante l\'eliminazione del test.
                        <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
                    </div></div>';
            }
        }

    ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between">
            <h3 class="my-3">Lista Test</h3>
            <a href="creaTest.php" class="my-auto">
                <button class="btn btn-success my-auto"><i class="bi bi-plus-lg"></i></button>
            </a>
        </div>
        <div>
            <ul class="list-group p-1" style="background-color: #f0f0f0;">
            <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<li class="list-group-item list-group-item-action d-flex justify-content-between border">';
                        echo '<div>';
                        echo '<h6>' . htmlspecialchars($row['titolo']) . '</h6>';
                        echo '<p>' . htmlspecialchars($row['descrizione']) . '</p>';
                        echo '</div>';
                        echo '<div class="my-auto d-flex gap-3">';
                        echo '<a href="#" class="btn btn-danger" onclick="return confirmDelete(' . $row['id'] . ')"><i class="bi bi-trash"></i></a>';
                        echo '<a class="btn btn-secondary" href="modificaTest.php?id_test='.$row["id"].'"><i class="bi bi-pencil-square"></i></a>';
                        echo '</div>';
                        echo '</li>';
                    }
                } else {
                    echo '<li class="list-group-item">Nessun test trovato</li>';
                }

                ?>
            </ul>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            var confirmDelete = confirm("Sei sicuro di voler eliminare questo test?");
            if (confirmDelete) {
                window.location.href = "php/deleteTest.php?id=" + id;
            }
            return false;
        }
    </script>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>