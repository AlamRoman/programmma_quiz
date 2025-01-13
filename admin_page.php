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
            
            if($_GET["delete"] !== $_SESSION["user_id"]){

                $sql = "DELETE FROM sessione_test WHERE creato_da = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $_GET["delete"]);
                $stmt->execute();

                $sql = "DELETE FROM test WHERE creato_da = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $_GET["delete"]);
                $stmt->execute();
                
                $sql = "DELETE FROM users WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $_GET["delete"]);
                $stmt->execute();
            }
        }

    }else if($_SERVER['REQUEST_METHOD'] === 'POST'){
        if(isset($_POST["registra_classe"])){

            $nome_classe = trim($_POST['nome_classe']);
            $anno_inizio = intval($_POST['anno_inizio']);
            $anno_fine = intval($_POST['anno_fine']);

            if (empty($nome_classe) || empty($anno_inizio) || empty($anno_fine)) {
                $msg_registra = "Tutti i campi sono obbligatori.";
                exit;
            }
        
            if ($anno_inizio >= $anno_fine) {
                $msg_registra = "L'anno di inizio deve essere precedente all'anno di fine.";
                exit;
            }
        
            try {
                $sql = "INSERT INTO classe (nome, anno_inizio, anno_fine) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
        
                if (!$stmt) {
                    throw new Exception("Errore nella preparazione della query.");
                }
                $stmt->bind_param("sss", $nome_classe, $anno_inizio, $anno_fine);
        
                if ($stmt->execute()) {
                    $msg_registra = "Classe registrata con successo!";
                } else {
                    throw new Exception("Errore durante l'inserimento: " . $stmt->error);
                }
        
                $stmt->close();
            } catch (Exception $e) {
                $msg_registra = $e->getMessage();
            }
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assegna_studente'])) {
        $id_studente = intval($_POST['id_studente']);
        $id_classe = intval($_POST['id_classe']);
    
        if (empty($id_studente) || empty($id_classe)) {
            $msg_assegna = "Seleziona uno studente e una classe.";
        } else {
            try {
                $sql = "INSERT INTO studente_classe (id_studente, id_classe) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
    
                if (!$stmt) {
                    throw new Exception("Errore nella preparazione della query.");
                }
    
                $stmt->bind_param("ii", $id_studente, $id_classe);
    
                if ($stmt->execute()) {
                    $msg_assegna = "Studente assegnato alla classe con successo!";
                } else {
                    throw new Exception("Errore durante l'inserimento: " . $stmt->error);
                }
    
                $stmt->close();
            } catch (Exception $e) {
                $msg_assegna = $e->getMessage();
            }
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

    //tabella classi
    $sql = "SELECT nome, anno_inizio, anno_fine FROM classe";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $fields = $result->fetch_fields();
    $classi = $result->fetch_all(MYSQLI_ASSOC);

    $table_classi = '<table class="table table-striped table-hover table-bordered">';

    $table_classi .= '<tr><thead class="table-dark">';
    foreach ($fields as $field) {
        $table_classi .= "<th>$field->name</th>";
    }
    $table_classi .= "</thead></tr><tbody>";

    foreach ($classi as $classe) {
        $table_classi .= "<tr>";

        foreach ($classe as $i => $data) {
            $table_classi .= "<td>$data</td>";
        }

        $table_classi .= "</tr>";
    }

    $table_classi .= "</tbody></table>";


    // Prendi gli studenti
    $sql_studenti = "
        SELECT users.id, users.username
        FROM users 
        INNER JOIN ruolo_users ON users.id = ruolo_users.id_user 
        WHERE ruolo_users.ruolo = 'studente' 
        AND users.id NOT IN (SELECT id_studente FROM studente_classe)
    ";
    $stmt = $conn->prepare($sql_studenti);
    $stmt->execute();
    $result_studenti = $stmt->get_result();
    $studenti = $result_studenti->fetch_all(MYSQLI_ASSOC);
    
    // Prendi le classi
    $sql_classi = "SELECT id, nome, anno_inizio, anno_fine FROM classe";
    $stmt = $conn->prepare($sql_classi);
    $stmt->execute();
    $result_classi = $stmt->get_result();
    $classi_assegna = $result_classi->fetch_all(MYSQLI_ASSOC);
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

        <div class="w-100 d-flex justify-content-center">
            <div class="container m-3 border p-3" style="width: 30vw;">
                <h3 class="text-center my-3">Registra classe</h3>
                <hr>
                <form action="admin_page.php" method="POST">
                    <div class="my-2">
                        <label for="nome_classe" class="form-label">Nome Classe </label>
                        <input type="text" id="nome_classe" name="nome_classe" class="form-control" required>
                    </div>
                    <div class="my-2">
                        <label for="anno_inizio" class="form-label">Anno Inizio </label>
                        <input type="number" id="anno_inizio" name="anno_inizio" class="form-control" required>
                    </div>
                    <div class="my-2">
                        <label for="anno_fine" class="form-label">Anno Fine </label>
                        <input type="number" id="anno_fine" name="anno_fine" class="form-control" required>
                    </div>

                    <?php 
                        if($msg != ""){
                            echo '
                                <div class="alert alert-primary mt-3" role="alert">
                                    '.$msg_registra.'
                                </div>
                            ';
                        }
                    ?>

                    <div class="mt-5 d-flex justify-content-center">
                        <input type="submit" name="registra_classe" id="registra_classe" value="Invia" class="btn btn-primary">
                    </div>
                </form>
            </div>

            <div class="container m-3 border p-3 w-50">
                <h3 class="text-center my-3">Tabella Classi</h3>
                <hr>
                <div class="table-responsive">
                    <?php echo $table_classi; ?>
                </div>
            </div>
        </div>
        
        <div class="container m-3 border p-3" style="width: 30vw;">
            <h3 class="text-center my-3">Assegna Studente alla Classe</h3>
            <hr>
            <form action="admin_page.php" method="POST">
                <div class="my-2">
                    <label for="id_studente" class="form-label">Seleziona Studente</label>
                    <select id="id_studente" name="id_studente" class="form-select" required>
                        <option value="">-- Scegli uno studente --</option>
                        <?php foreach ($studenti as $studente): ?>
                            <option value="<?= $studente['id'] ?>">
                                <?= $studente['username']?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="my-2">
                    <label for="id_classe" class="form-label">Seleziona Classe</label>
                    <select id="id_classe" name="id_classe" class="form-select" required>
                        <option value="">-- Scegli una classe --</option>
                        <?php foreach ($classi_assegna as $classe): ?>
                            <option value="<?= $classe['id'] ?>">
                                <?= $classe['nome'] . ' (' . $classe['anno_inizio'] . '-' . $classe['anno_fine'] . ')' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php 
                    if (isset($msg_assegna)) {
                        echo '
                            <div class="alert alert-primary mt-3" role="alert">
                                '.$msg_assegna.'
                            </div>
                        ';

                        unset($msg_assegna);
                    }
                ?>
                <div class="mt-5 d-flex justify-content-center">
                    <input type="submit" name="assegna_studente" value="Assegna" class="btn btn-primary">
                </div>
            </form>
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