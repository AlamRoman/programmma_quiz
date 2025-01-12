<?php 

	session_start();

	include "include/functions.ini";
	include "include/db.php";

	if (!isset($_SESSION["is_logged_in"]) || $_SESSION["is_logged_in"] == false) {
		go_to("login_page.php");
	}

	$sql = "SELECT id_classe FROM studente_classe WHERE id_studente = ?";
	$stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $_SESSION["user_id"]);
	$stmt->execute();
	$result = $stmt->get_result();
	$id_classe = $result->fetch_assoc();

    $id_classe = $id_classe["id_classe"];

    $sql = "
        SELECT t.id AS id_test, t.titolo, t.descrizione, st.id AS id_sessione, st.data_fine AS scadenza, st.nome AS nome_sessione
        FROM test t
        INNER JOIN sessione_test st ON t.id = st.id_test
        WHERE st.id_classe = ? AND st.data_inizio <= CURDATE()
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id_classe);
    $stmt->execute();
    $tests = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Studenti</title>

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

</head>
<body>

	<?php include "include/navbar-studente.php"; ?>

	<div class="ms-5 mt-5 pt-4">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item active" aria-current="page">Home</li>
			</ol>
		</nav>
	</div>

	<?php 

		if(isset($_SESSION["testSvolto"])){

			if($_SESSION["testSvolto"]==1){
				echo '<div class="container mt-5"><div class="alert alert-success alert-dismissible fade show" role="alert">
					<i class="bi bi-check-circle-fill"></i> Test svolto, tutte le risposte salvate correttamente!
					<button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
				</div></div>';
			}else{
				echo '<div class="container mt-5"><div class="alert alert-danger alert-dismissible fade show" role="alert">
						<i class="bi bi-x-circle-fill"></i> Errore durante il salvataggio delle risposte!
						<button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
					</div></div>';
			}
			
			unset($_SESSION["testSvolto"]);
		}

	?>

	<div class="container mt-5">
		<h3 class="my-3">Lista Test</h3>
		<div>
			<ul class="list-group p-1" style="background-color: #f0f0f0;">
				<?php
					if ($tests->num_rows > 0) {
						
						while ($row = $tests->fetch_assoc()) {
							// Verifica se esistono dati dell'utente per il test
							$sql_check = "SELECT COUNT(*) AS count FROM risultati WHERE id_sessione = ? AND id_studente = ?";
							$stmt = $conn->prepare($sql_check);
							$stmt->bind_param("ii", $row["id_sessione"], $_SESSION["user_id"]);
							$stmt->execute();
							$result_check = $stmt->get_result();
							$data_check = $result_check->fetch_assoc();
							$has_data = $data_check["count"] > 0 ? 1 : 0;

							echo '<li class="list-group-item list-group-item-action d-flex justify-content-between border">';
							echo '<div>';
							echo '<h6>' . htmlspecialchars($row['nome_sessione']) . " | " . htmlspecialchars($row['titolo']) . '</h6>';
							echo '<p>' . htmlspecialchars($row['descrizione']) . '</p>';
							echo '</div>';
							echo '<div class="me-5 ms-auto my-auto"><pclass="mt-auto"> Scadenza : ' . htmlspecialchars($row['scadenza']) . '</p></div>';
							echo '<div class="my-auto d-flex gap-2">';

							if ($row["scadenza"] <= date('Y-m-d H:i:s')) {

								if($has_data){
									echo '<a href="riepilogo.php?id_sessione='.$row["id_sessione"].'" class="btn btn-info"><i class="bi bi-eye"></i> Riepilogo</a>';
									echo '<button class="btn btn-success" style="background-color:#968887; border:none; color: #000;" disabled><i class="bi bi-journal-text"></i> Svolgi</button>';
								}else{
									echo '<button href="riepilogo.php?id_sessione='.$row["id_sessione"].'" class="btn btn-info" style="background-color:#968887; border:none; color: #000;" disabled><i class="bi bi-eye"></i> Riepilogo</button>';
									echo '<button class="btn btn-success" style="background-color:#968887; border:none; color: #000;" disabled><i class="bi bi-journal-text"></i> Svolgi</button>';
								}

							} else {

								if($has_data){
									echo '<a href="riepilogo.php?id_sessione='.$row["id_sessione"].'" class="btn btn-info"><i class="bi bi-eye"></i> Riepilogo</a>';
									echo '<button class="btn btn-success" style="background-color:#968887; border:none; color: #000;" disabled><i class="bi bi-journal-text"></i> Svolgi</button>';
								}else{
									echo '<button class="btn btn-info" style="background-color:#968887; border:none; color: #000;" disabled><i class="bi bi-eye"></i> Riepilogo</button>';
									echo '<a href="svolgi_test.php?id_sessione='.$row["id_sessione"].'" class="btn btn-success"><i class="bi bi-journal-text"></i> Svolgi</a>';
								}
								
							}
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

	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6Hty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	
</body>
</html>