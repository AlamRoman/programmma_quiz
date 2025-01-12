<?php
    session_start();
    
    include "include/functions.ini";
    include "include/db.php";

    if (!isset($_SESSION["is_logged_in"]) || $_SESSION["is_logged_in"] == false) {
        header("Location: login_page.php");
        exit();
    }

    $sql = "
    SELECT 
        r.id AS risultato_id,
        r.punteggio,
        st.id AS sessione_id,
        st.nome AS sessione_titolo,
        u.id AS studente_id,
        u.first_name AS studente_nome,
        u.last_name AS studente_cognome,
        c.nome AS studente_classe
    FROM 
        risultati r
    JOIN 
        sessione_test st ON r.id_sessione = st.id
    JOIN 
        users u ON r.id_studente = u.id
    JOIN 
        studente_classe sc ON u.id = sc.id_studente
    JOIN 
        classe c ON sc.id_classe = c.id
    WHERE 
        st.creato_da = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $_SESSION["user_id"]);
    $stmt->execute();
    $result = $stmt->get_result();

    $sessioni_results = [];
    while ($row = $result->fetch_assoc()) {
        $sessioni_results[] = $row;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Svolti</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
</head>
<body>

    <?php include "include/navbar-docente.php"; ?>

    <div class="ms-5 mt-5 pt-4">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item active" aria-current="page">Lista sessione test</li>
			</ol>
		</nav>
	</div>

    <div class="container mt-5">
        <h3 class="">Sessioni Test Svolti</h3>
        
        <div class="list-group mt-4">
            <?php if (count($sessioni_results) > 0): ?>
                <?php foreach ($sessioni_results as $result): ?>
                    <div class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">
                                <?php echo htmlspecialchars($result['sessione_titolo']); ?>
                            </h5>
                            <?php echo '<a href="riepilogo.php?id_sessione='.$result["sessione_id"].'&id_studente='.$result["studente_id"].'" class="btn btn-info"><i class="bi bi-eye"></i> Riepilogo</a>'; ?>
                        </div>
                        <p class="mb-1">
                            Studente: <?php echo htmlspecialchars($result['studente_nome'] . ' ' . $result['studente_cognome']); ?>
                        </p>
                        <small class="text-muted">
                            Classe: <?php echo htmlspecialchars($result['studente_classe']); ?>
                        </small>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center mt-4">Nessun risultato trovato</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"></script>

</body>
</html>
