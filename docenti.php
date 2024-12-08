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

    <?php 
        if(isset($_GET["testCreato"]) && $_GET["testCreato"]==1){
            echo '<div class="container mt-5"><div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i> Nuovo test creato !
                    <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
                </div></div>';
        }
    ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between">
            <h3 class="my-3">Test creati</h3>
            <a href="creaTest.php" class="my-auto">
                <button class="btn btn-success my-auto"><i class="bi bi-plus-lg"></i></button>
            </a>
        </div>
        <hr>
        <div>
            <ul class="list-group">
            <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<li class="list-group-item list-group-item-action d-flex justify-content-between border">';
                        echo '<div>';
                        echo '<h6>' . htmlspecialchars($row['titolo']) . '</h6>';
                        echo '<p>' . htmlspecialchars($row['descrizione']) . '</p>';
                        echo '</div>';
                        echo '<div class="my-auto d-flex gap-3">';
                        echo '<button class="btn btn-danger"><i class="bi bi-trash"></i></button>';
                        echo '<button class="btn btn-secondary"><i class="bi bi-pencil-square"></i></button>';
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>