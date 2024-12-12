<?php 

    require "../include/db.php";

    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["invia"])) {
        $username = $_POST["username"];
        $password = $_POST["password"];
        $email = $_POST["email"];
        $nome = $_POST["nome"];
        $cognome = $_POST["cognome"];
        $ruolo = $_POST["ruolo"];

        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $query);
        $row = mysqli_num_rows($result);

        if ($row > 0) {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            try {

                $sql = 'SELECT * FROM users WHERE username=?';
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();

                $user_id = $user["id"];

                $sql = 'UPDATE users SET username=?,password=?,email=?,first_name=?,last_name=? WHERE id=?;';
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssi", $username, $hashed_password, $email, $nome, $cognome,$user_id);
                $stmt->execute();

            } catch (Exception $e) {
                $msg =  "Database error: " . $e->getMessage();

                $_SESSION["msg"] = $msg;
                header("location:../admin_page.php");
                exit();
            }

            $msg = "Utente $username modificato correttamente";

            $_SESSION["msg"] = $msg;
            header("location:../admin_page.php");
            exit();

        }else{
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            try {
                $sql = 'INSERT INTO users(username, password, email, first_name, last_name) VALUES(?,?,?,?,?);';
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssss", $username, $hashed_password, $email, $nome, $cognome);
                $stmt->execute();

                $user_id = $conn->insert_id;

                $sql = "SELECT id FROM ruolo WHERE ruolo = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $ruolo);
                $stmt->execute();
                $result = $stmt->get_result();
                $ruolo_id = $result->fetch_assoc();

                $sql = 'INSERT INTO ruolo_users(id_ruolo, id_user, ruolo) VALUES(?,?,?);';
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iis", $ruolo_id["id"], $user_id, $ruolo);
                $stmt->execute();

                $_SESSION["msg"] = "Utente aggiunto";
                header("location:../admin_page.php");
                exit();
            } catch (Exception $e) {
                $msg =  "Database error: " . $e->getMessage();

                $_SESSION["msg"] = $msg;
                header("location:../admin_page.php");
                exit();
            }
        }
    }
?>