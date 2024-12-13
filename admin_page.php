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

    $username = "";
    $password = "";
    $email = "";
    $nome = "";
    $cognome = "";
    $user_ruolo = "";

    if($_SERVER['REQUEST_METHOD'] === 'GET'){


        if(isset($_GET["update"])){
            $id_user = $_GET["update"];

            $sql = "SELECT * FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id_user);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            $username = $user["username"];
            $password = $user["password"];
            $email = $user["email"];
            $nome = $user["first_name"];
            $cognome = $user["last_name"];

            $sql = "SELECT ruolo FROM ruolo_users WHERE id_user = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user["id"]);
            $stmt->execute();
            $result = $stmt->get_result();
            $ruolo_user = $result->fetch_assoc();

            $user_ruolo = $ruolo_user["ruolo"];

        }else if(isset($_GET["delete"])){
            $sql = "DELETE FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $_GET["delete"]);
            $stmt->execute();
        }

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
    $table .= "<th>ruolo</th><th>elimina</th>";
    $table .= "</thead></tr><tbody>";

    foreach ($users as $user) {
        $table .= "<tr>";

        foreach ($user as $i => $data) {

            if($i==="id"){
                $table .= '<td><a href="'.$_SERVER['PHP_SELF'].'?update='.$user["id"].'"><button class="btn btn-secondary w-100">'.$data.'</button></a></td>';
            }else if($i==="password"){
                $table .= "<td style=\"max-width:100px;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;\">$data</td>";
            }else{
                $table .= "<td>$data</td>";
            }
        }

        $sql = "SELECT ruolo FROM ruolo_users WHERE id_user = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user["id"]);
        $stmt->execute();
        $result = $stmt->get_result();
        $ruolo = $result->fetch_assoc();

        $table .= '<td>'.$ruolo["ruolo"].'</td>';

        $table .= '<td>'.'<a href="#" class="btn btn-danger w-100" onclick="return confirmDelete(' . $user["id"] . ')"><i class="bi bi-trash"></i></a>'.'</td>';

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container-fluid d-flex justify-content-between">

            <h3 class="fw-bold">Benvenuto <?php echo $_SESSION["username"]; ?>!</h3>

            <h4 class="fw-bold text-center" style="transform: translateX(-50%);">Pagina Admin</h4>

            <form class="d-flex" method="POST" action="php/do_logout.php">
                <input class="btn btn-secondary ms-auto" type="submit" id="logout" name="logout" value="logout">
            </form>
        </div>
    </nav>

    <div class="container my-5 pt-5 d-flex flex-wrap">
        <div class="container m-3 border p-3" style="min-width: 30vw; max-width: 50vw;">
            <h3 class="text-center my-3">Registra/modifica utente</h3>
            <hr>
            <form action="php/registra_utente.php" method="POST">
                <div class="my-2">
                    <label for="username" class="form-label">Username </label>
                    <input type="text" id="username" name="username" class="form-control" required <?php if($username != ""){echo 'value='.$username;} ?>>
                </div>
                <div class="my-2">
                    <label for="password" class="form-label">Password </label>
                    <input type="text" id="password" name="password" class="form-control" required <?php if($password != ""){echo 'value='.$password;} ?>>
                </div>
                <div class="my-2">
                    <label for="email" class="form-label">Email </label>
                    <input type="text" id="email" name="email" class="form-control" <?php if($email != ""){echo 'value='.$email;} ?>>
                </div>
                <div class="my-2">
                    <label for="Nome" class="form-label">Nome </label>
                    <input type="text" id="nome" name="nome" class="form-control" <?php if($nome != ""){echo 'value='.$nome;} ?>>
                </div>
                <div class="my-2">
                    <label for="cognome" class="form-label">Cognome </label>
                    <input type="text" id="cognome" name="cognome" class="form-control" <?php if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET["update"])){echo 'value='.$cognome;} ?>>
                </div>
                <div class="my-2">
                    <label for="ruolo" class="form-label">Ruolo </label>
                    <?php if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET["update"])){echo '<input type="hidden" name="ruolo" value="'.$user_ruolo.'"/>';} ?>
                    <select name="ruolo" id="ruolo" class="form-select" <?php if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET["update"])){echo "disabled";} ?> required>
                        <option value="studente" selected <?php if($user_ruolo === "studente"){echo "selected";} ?>>Studente</option>
                        <option value="docente" <?php if($user_ruolo === "docente"){echo "selected";} ?>>Docente</option>
                        <option disabled value="admin" <?php if($user_ruolo === "admin"){echo "selected";} ?>>Admin</option>
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
            <div class="mt-3 ms-auto d-flex justify-content-center">
                <a href="admin_page.php"><button class="btn btn-secondary">Cancella campi</button></a>
            </div>
        </div>

        <div class="container m-3 border p-3">
            <h3 class="text-center my-3">Tabella Utenti</h3>
            <hr>
            <div class="table-responsive">
                <?php  echo $table;?>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            var confirmDelete = confirm("Sei sicuro di voler eliminare quest'utente?");
            if (confirmDelete) {
                window.location.href = "admin_page.php?delete=" + id;
            }
            return false;
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script> 
</body>
</html>