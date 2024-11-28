<?php 

    include "../include/functions.ini";

    session_start();

    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["logout"])){
        session_unset();
        session_destroy();
    }

    go_to("../login_page.php");

?>