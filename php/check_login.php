<?php

    require "../include/db.php";
    include "../include/functions.php";

    session_start();

    $msg = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["login"])) {
        
        $username = $_POST["username"];
        $password = $_POST["password"];

        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user !== null) {
            
            if (password_verify($password, $user["password"])) {

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["user_first_name"] = $user["first_name"];
                $_SESSION["user_last_name"] = $user["last_name"];
                $_SESSION["user_email"] = $user["email"];
                $_SESSION["is_logged_in"] = true;

                go_to("../svolgi_test.php");

            }else{
                $msg = "Incorrect password";
            }

        }else{
            $msg = "Username not found";
        }

    }

    $_SESSION["msg"] = $msg;

    go_to("../login_page.php");
    exit();

?>