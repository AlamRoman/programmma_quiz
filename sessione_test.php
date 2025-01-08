<?php 

    session_start();

    include "include/functions.ini";
    include "include/db.php";

    if (!isset($_SESSION["is_logged_in"]) || $_SESSION["is_logged_in"] == false) {
        go_to("login_page.php");
    }

    $sql = "
        SELECT 
        s.id, 
        s.id_test, 
        s.nome,
        t.titolo AS titolo_test, 
        s.id_classe, 
        c.nome AS nome_classe, 
        s.data_inizio, 
        s.data_fine, 
        s.stato
    FROM 
        sessione_test s
    JOIN 
        test t ON s.id_test = t.id
    JOIN 
        classe c ON s.id_classe = c.id;

    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

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
                <li class="breadcrumb-item active" aria-current="page">Lista sessioni</li>
            </ol>
        </nav>
    </div>

    <?php 

        if(isset($_SESSION["sessione_test_creato"])){

            if($_SESSION["sessione_test_creato"]==1){
                echo '<div class="container mt-5"><div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i> Sessione test creata correttamente
                    <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
                </div></div>';
            }else if($_SESSION["sessione_test_creato"]==2){
                echo '<div class="container mt-5"><div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-x-circle-fill"></i> Errore durante la creazione della sessione test
                    <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
                </div></div>';
            }else{
                echo '<div class="container mt-5"><div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-x-circle-fill"></i> Errore durante la preparazione della query
                        <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
                    </div></div>';
            }

            unset($_SESSION["sessione_test_creato"]);
        }

    ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between">
            <h3 class="my-3">Lista Sessioni Test</h3>
            <a href="creaSessioneTest.php" class="my-auto">
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
                        echo '<h6>' . htmlspecialchars($row['nome']) . '</h6>';
                        echo '<p>' . htmlspecialchars($row['nome_classe']) . ' - '. htmlspecialchars($row['titolo_test'])  . '</p>';
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
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</body>