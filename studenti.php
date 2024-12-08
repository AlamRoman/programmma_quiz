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
    <title>Studenti</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
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

    <div class="container mt-5">
        <h3 class="my-3">Test da svolgere</h3>
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
                            echo '<div class="my-auto d-flex gap-5"><button class="btn btn-success"><i class="bi bi-journal-text"></i> Svolgi</button><div>';
                            echo '</li>';
                        }
                    } else {
                        echo '<li class="list-group-item">Nessun test trovato</li>';
                    }
                ?>
            </ul>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script> 
    
</body>
</html>