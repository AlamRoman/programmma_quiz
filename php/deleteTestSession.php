<?php
    session_start();
    include "../include/db.php";

    if (!isset($_SESSION["is_logged_in"]) || $_SESSION["is_logged_in"] == false) {
        header("Location: ../login_page.php");
        exit();
    }

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $testId = intval($_GET['id']);

        $stmt = $conn->prepare("DELETE FROM sessione_test WHERE id = ?");
        $stmt->bind_param("i", $testId);

        if ($stmt->execute()) {
            header("Location: ../sessione_test.php?deleted=1");
        } else {
            header("Location: ../sessione_test.php?deleted=0");
        }
        $stmt->close();
    } else {
        header("Location: ../homeDocente.php");
    }

?>
