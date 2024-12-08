<?php 

    session_start();

    include "include/functions.ini";
    include "include/db.php";

    if (!isset($_SESSION["is_logged_in"]) || $_SESSION["is_logged_in"] == false) {
        go_to("login_page.php");
    }

    $msg = "";
    if (isset($_SESSION["msg"])) {
        $msg = $_SESSION["msg"];
        unset($_SESSION["msg"]);
    }

    //prendi tutti utenti dal db
    $sql = "SELECT * FROM users";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $fields = $result->fetch_fields();
    $users = $result->fetch_all(MYSQLI_ASSOC);

    //crea la tabella
    $table = '<table class="table table-striped table-hover table-bordered">';

    $table .= '<tr><thead class="table-dark">';
    foreach ($fields as $field) {
        $table .= "<th>$field->name</th>";
    }
    $table .= "<th>ruolo</th>";
    $table .= "</thead></tr><tbody>";

    foreach ($users as $user) {
        $table .= "<tr>";

        foreach ($user as $data) {
            $table .= "<td>$data</td>";
        }

        $sql = "SELECT ruolo FROM ruolo_users WHERE id_user = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user["id"]);
        $stmt->execute();
        $result = $stmt->get_result();
        $ruolo = $result->fetch_assoc();

        $table .= '<td>'.$ruolo["ruolo"].'</td>';

        $table .= "</tr>";
    }

    $table .= "</tbody></table>";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina Admin</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid d-flex justify-content-between">

            <h3 class="fw-bold">Benvenuto <?php echo $_SESSION["username"]; ?>!</h3>

            <h4 class="fw-bold text-center" style="transform: translateX(-50%);">Pagina Admin</h4>

            <form class="d-flex" method="POST" action="php/do_logout.php">
                <input class="btn btn-secondary ms-auto" type="submit" id="logout" name="logout" value="logout">
            </form>
        </div>
    </nav>

    <div class="container my-5 d-flex flex-wrap">
        <div class="container m-3 border p-3" style="min-width: 30vw; max-width: 50vw;">
            <h3 class="text-center my-3">Registra/modifica utente</h3>
            <hr>
            <form action="php/registra_utente.php" method="POST">
                <div class="my-2">
                    <label for="username" class="form-label">Username </label>
                    <input type="text" id="username" name="username" class="form-control">
                </div>
                <div class="my-2">
                    <label for="password" class="form-label">Password </label>
                    <input type="text" id="password" name="password" class="form-control">
                </div>
                <div class="my-2">
                    <label for="email" class="form-label">Email </label>
                    <input type="text" id="email" name="email" class="form-control">
                </div>
                <div class="my-2">
                    <label for="Nome" class="form-label">Nome </label>
                    <input type="text" id="nome" name="nome" class="form-control">
                </div>
                <div class="my-2">
                    <label for="cognome" class="form-label">Cognome </label>
                    <input type="text" id="cognome" name="cognome" class="form-control">
                </div>
                <div class="my-2">
                    <label for="ruolo" class="form-label">Ruolo </label>
                    <select name="ruolo" id="ruolo" class="form-select">
                        <option selected disabled>--- segli ruolo ---</option>
                        <option value="studente">Studente</option>
                        <option value="docente">Docente</option>
                    </select>
                </div>
                <?php 
                    if($msg != ""){
                        echo '
                            <div class="alert alert-primary mt-3" role="alert">
                                '.$msg.'
                            </div>
                        ';
                    }
                 ?>
                <div class="mt-5 d-flex justify-content-center">
                    <input type="submit" name="invia" id="invia" value="Invia" class="btn btn-primary">
                </div>
            </form>
        </div>

        <div class="container m-3 border p-3">
            <h3 class="text-center my-3">Tabella Utenti</h3>
            <hr>
            <div class="table-responsive">
                <?php  echo $table;?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script> 
</body>
</html>