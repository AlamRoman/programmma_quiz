<?php 

	session_start();

	include "include/functions.ini";
	include "include/db.php";

	if (!isset($_SESSION["is_logged_in"]) || $_SESSION["is_logged_in"] == false) {
		go_to("login_page.php");
	}

	if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET["id_test"])){
		$id_test = $_GET["id_test"];
	}else{
		go_to("studenti.php");
		exit();
	}

	//prendi il test da database
	$sql = "SELECT * FROM test WHERE id = ?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("i", $id_test);
	$stmt->execute();
	$result = $stmt->get_result();
	$test = $result->fetch_assoc();

	//prendi le domande del test
	$sql = "SELECT * FROM domanda WHERE id_test = ?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("i", $id_test);
	$stmt->execute();
	$result = $stmt->get_result();
	$domande = $result->fetch_all(MYSQLI_ASSOC);

	//prendi le risposte di ogni domanda e la risposta data dall'utente
	$has_responses = false; // Flag per verificare se ci sono risposte
	for ($i=0; $i < count($domande); $i++) { 
		$sql = "SELECT * FROM risposta WHERE id_domanda = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("i", $domande[$i]["id"]);
		$stmt->execute();
		$result = $stmt->get_result();
		$risposte = $result->fetch_all(MYSQLI_ASSOC);

		$r = array();

		foreach($risposte as $key => $risposta){
			$r[$key+1] = $risposta["testo"];
		}

		$domande[$i]["risposte"] = $r;

		// prendi la risposta data dall'utente
		$sql = "SELECT risposta_data FROM risposte_date WHERE id_test = ? AND id_user = ? AND id_domanda = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("iii", $id_test, $_SESSION["user_id"], $domande[$i]["id"]);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$domande[$i]["risposta_data"] = $row && isset($row["risposta_data"]) ? $row["risposta_data"] : null;

		if ($row && isset($row["risposta_data"])) {
			$has_responses = true; // Se esiste almeno una risposta, setta il flag a true
		}
	}

	// For test purposes only
	$punteggio=20;
	$total=20;

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Riepilogo - test</title>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
	<style>
		input[disabled], textarea[disabled] {
			background-color: #fff !important;
			color: #000 !important;
			opacity: 1 !important;
			border: 1px solid #ced4da !important;
		}
		input[type="radio"][disabled] {
			opacity: 100 !important;
			pointer-events: none; /* Evita interazioni */
			color: #000 !important;
		}
		input[type="radio"][disabled]:checked + label {
			font-weight: bold; /* Indica chiaramente il selezionato */
			color: #000 !important;
		}
	</style>
</head>
<body>

	<nav class="navbar navbar-expand-lg navbar-light bg-light">
		<div class="container-fluid d-flex justify-content-between">

			<h3 class="fw-bold">Benvenuto <?php echo $_SESSION["username"]; ?>!</h3>

			<h4 class="fw-bold text-center" style="transform: translateX(-75%);">Riepilogo</h4>

			<form class="d-flex" method="POST" action="php/do_logout.php">
				<input class="btn btn-secondary ms-auto" type="submit" id="logout" name="logout" value="logout">
			</form>
		</div>
	</nav>

	<div class="ms-5 mt-3">
	<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="studenti.php">Home</a></li>
		<li class="breadcrumb-item active" aria-current="page">Riepilogo test</li>
	</ol>
	</nav>
	</div>

	<div class="mt-2">

		<h2 class="fw-bold text-center my-5"><?php if(isset($test["titolo"])){echo $test["titolo"];}; ?></h2>

		<div class="mt-3 card container p-0 w-75 mb-3" style="background-color: #c1ced9">
			<div class="card-body">
				<h4 class="fw-bold">Riepilogo</h4>
				<hr/>
				<p class="card-text m-0">Punteggio: <span class="fw-bold"><?php echo $punteggio;?></span>/<?php echo $total;?> (<?php echo round(($punteggio/$total)*100, 2);?>%)</p>
				<p class="card-text m-0">Voto: <span class="fw-bold"><?php $risultato=round(($punteggio/$total)*10, 2); if($risultato<=5){echo "<span style='color:#ed6053;'>$risultato</span>";}else if($risultato==10){echo "<span style='color:#c561ff;text-shadow:0 0 10px #c561ff;'>$risultato</span>";}else{echo $risultato;};?></span></p>
			</div>
		</div>

		<div class="w-75 container p-0">
			<?php 
				if (!$has_responses) {
					echo "<p class='text-center fw-bold'>Non hai ancora compilato il test.</p>";
				} else {
					foreach ($domande as $domanda) {
						echo "<div class='card mb-3'>";
						echo "<div class='card-body'>";
						echo "<h5 class='card-title'>" . $domanda["testo"] . "</h5>";
						if ($domanda["risposte"] && is_array($domanda["risposte"])) {
							foreach ($domanda["risposte"] as $key => $risposta) {
								$checked = (string)$key === $domanda["risposta_data"] ? "checked" : "";
								echo "<div class='form-check'>";
								echo "<input class='form-check-input' type='radio' name='risposte[" . $domanda["id"] . "]' value='" . htmlspecialchars($risposta) . "' $checked " . ($checked !== 'checked' ? "disabled" : "") . ">";
								echo "<label class='form-check-label' for='risposte[" . $domanda["id"] . "]'>" . htmlspecialchars($risposta) . "</label>";
								echo "</div>";
							}
						}
						if ($domanda["tipo"] === "aperta") {
							echo "<textarea class='form-control' disabled>" . htmlspecialchars($domanda["risposta_data"]) . "</textarea>";
						}
						echo "</div></div>";
					}
				}
			?>

			<form action="#" class="mb-5 mt-3 mx-auto container p-0">
				<input type="submit" class="btn btn-primary float-right" value="Fine">
			</form>

		</div>
	</div>

	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6Hty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script> 
</body>
</html>