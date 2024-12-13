<?php 

	session_start();

	include "include/functions.ini";
	include "include/db.php";

	if (!isset($_SESSION["is_logged_in"]) || $_SESSION["is_logged_in"] == false) {
		go_to("login_page.php");
	}

	$sql = "SELECT id, titolo, descrizione FROM test";
	$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Studenti</title>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

</head>
<body>

	<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
		<div class="container-fluid d-flex justify-content-between">

			<h3 class="fw-bold">Benvenuto <?php echo $_SESSION["username"]; ?>!</h3>

			<h4 class="fw-bold text-center" style="transform: translateX(-50%);">Home</h4>

			<form class="d-flex" method="POST" action="php/do_logout.php">
				<input class="btn btn-secondary ms-auto" type="submit" id="logout" name="logout" value="logout">
			</form>
		</div>
	</nav>

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
					if ($result->num_rows > 0) {
						while ($row = $result->fetch_assoc()) {
							// Verifica se esistono dati dell'utente per il test
							$sql_check = "SELECT COUNT(*) AS count FROM risposte_date WHERE id_test = ? AND id_user = ?";
							$stmt = $conn->prepare($sql_check);
							$stmt->bind_param("ii", $row["id"], $_SESSION["user_id"]);
							$stmt->execute();
							$result_check = $stmt->get_result();
							$data_check = $result_check->fetch_assoc();
							$has_data = $data_check["count"] > 0;

							echo '<li class="list-group-item list-group-item-action d-flex justify-content-between border">';
							echo '<div>';
							echo '<h6>' . htmlspecialchars($row['titolo']) . '</h6>';
							echo '<p>' . htmlspecialchars($row['descrizione']) . '</p>';
							echo '</div>';
							echo '<div class="my-auto d-flex gap-2">';
							if ($has_data) {
								echo '<a href="riepilogo.php?id_test='.$row["id"].'" class="btn btn-info"><i class="bi bi-eye"></i> Riepilogo</a>';
								echo '<button class="btn btn-success" style="background-color:#968887; border:none; color: #000;" disabled><i class="bi bi-journal-text"></i> Svolgi</button>';
							} else {
								echo '<button class="btn btn-info" style="background-color:#968887; border:none;" disabled><i class="bi bi-eye"></i> Riepilogo</button>';
								echo '<a href="svolgi_test.php?id_test='.$row["id"].'" class="btn btn-success"><i class="bi bi-journal-text"></i> Svolgi</a>';
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
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script> 
	
</body>
</html>