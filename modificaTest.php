<?php  

    session_start();

    include "include/functions.ini";
    include "include/db.php";

    if (!isset($_SESSION["is_logged_in"]) || $_SESSION["is_logged_in"] == false) {
        go_to("login_page.php");
    }

    $id_test=null;

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET["id_test"])){
        $id_test = $_GET["id_test"];

    }else if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["salva_modifiche"])){

        try {
            $id_test = $_POST["id_test"];

            $titolo = $_POST['titolo'];
            $descrizione = $_POST['descrizione'];

            // aggiorna i dati del test
            $sql_update_test = "UPDATE test SET titolo = ?, descrizione = ? WHERE id = ?";
            $stmt = $conn->prepare($sql_update_test);
            $stmt->bind_param("ssi", $titolo, $descrizione, $id_test);
            $stmt->execute();

            //id delle domande presenti nel test
            $sql = "SELECT id FROM domanda WHERE id_test = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id_test);
            $stmt->execute();
            $result = $stmt->get_result();
            $existing_question_ids = $result->fetch_all(MYSQLI_ASSOC);

            //id delle domande nel form
            $submitted_ids = $_POST['domanda_ids'] ?? [];
            
            $existing_ids = array_column($existing_question_ids, 'id');
            $to_delete = array_diff($existing_ids, $submitted_ids);

            if (!empty($to_delete)) {
                $placeholders = implode(',', array_fill(0, count($to_delete), '?'));
                $sql_delete_questions = "DELETE FROM domanda WHERE id IN ($placeholders)";
                $stmt_delete = $conn->prepare($sql_delete_questions);
                $stmt_delete->bind_param(str_repeat('i', count($to_delete)), ...$to_delete);
                $stmt_delete->execute();
            }

            foreach ($_POST['tipo'] as $index => $tipo) {
                $question_id = $submitted_ids[$index] ?? null;
                $testo = $_POST["testo"][$index];
        
                if ($question_id) {
                    //aggiorna domanda
                    $sql_update_question = "UPDATE domanda SET testo = ?, tipo = ? WHERE id = ?";
                    $stmt_update_question = $conn->prepare($sql_update_question);
                    $stmt_update_question->bind_param("ssi", $testo, $tipo, $question_id);
                    $stmt_update_question->execute();
                } else {
                    //inserisci nuova domanda
                    $sql_insert_question = "INSERT INTO domanda (id_test, testo, tipo) VALUES (?, ?, ?)";
                    $stmt_insert_question = $conn->prepare($sql_insert_question);
                    $stmt_insert_question->bind_param("iss", $id_test, $testo, $tipo);
                    $stmt_insert_question->execute();
                    $question_id = $conn->insert_id;
                }
        
                //aggiorna risposte delle domande multipla
                if ($tipo === 'multipla') {
                    //cancella la risposta vecchia
                    $sql_delete_options = "DELETE FROM risposta WHERE id_domanda = ?";
                    $stmt_delete_options = $conn->prepare($sql_delete_options);
                    $stmt_delete_options->bind_param("i", $question_id);
                    $stmt_delete_options->execute();
        
                    //aggiungi nuova risposta
                    $op = $_POST['opzioni'][$index] ?? [[1=>""],[2=>""],[3=>""],[4=>""]];
                    foreach ($op as $option_index => $option_text) {

                        $is_correct = ($_POST['rispostaCorretta'][$index] == $option_index + 1) ? 1 : 0;
                        $sql_insert_option = "INSERT INTO risposta (id_domanda, testo, corretta) VALUES (?, ?, ?)";
                        $stmt_insert_option = $conn->prepare($sql_insert_option);
                        $stmt_insert_option->bind_param("isi", $question_id, $option_text, $is_correct);
                        $stmt_insert_option->execute();
                    }
                }
            }

            $_SESSION["testModificato"]=1;
            go_to("docenti.php");
        } catch (\Throwable $th) {
            $_SESSION["testModificato"]=0;
            go_to("docenti.php?testModificato=0");
        }

    }else{
        go_to("docenti.php");
        exit();
    }

    //prendi il test dal database
    $sql = "SELECT * FROM test WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_test);
    $stmt->execute();
    $result = $stmt->get_result();
    $test = $result->fetch_assoc();

    // prendi le domande dal database
    $sql_questions = "SELECT * FROM domanda WHERE id_test = ?";
    $stmt_questions = $conn->prepare($sql_questions);
    $stmt_questions->bind_param("i", $id_test);
    $stmt_questions->execute();
    $result_questions = $stmt_questions->get_result();
    $domande = $result_questions->fetch_all(MYSQLI_ASSOC);

    // prendi le risposte delle domande multiple
    $sql_options = "SELECT * FROM risposta WHERE id_domanda IN (SELECT id FROM domanda WHERE id_test = ?)";
    $stmt_options = $conn->prepare($sql_options);
    $stmt_options->bind_param("i", $id_test);
    $stmt_options->execute();
    $result_options = $stmt_options->get_result();
    $opzioni = $result_options->fetch_all(MYSQLI_ASSOC);

    $grouped_options = [];
    foreach ($opzioni as $opzione) {
        $grouped_options[$opzione['id_domanda']][] = $opzione;
    }


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Test</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid d-flex justify-content-between">

            <h3 class="fw-bold">Benvenuto <?php echo $_SESSION["username"]; ?>!</h3>

            <h4 class="fw-bold text-center" style="transform: translateX(-50%);">Modifica Test</h4>

            <form class="d-flex" method="POST" action="php/do_logout.php">
                <input class="btn btn-secondary ms-auto" type="submit" id="logout" name="logout" value="logout">
            </form>
        </div>
    </nav>

    <div class="ms-5 mt-3">
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="docenti.php">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Modifica Test</li>
    </ol>
    </nav>
    </div>

    <div class="container mt-5 border p-5 d-flex flex-column gap-5">
        <form action="modificaTest.php" method="POST" class="d-flex flex-column gap-5">

            <div class="border p-3">
                <h3>Dati test</h3>
                <div class="d-flex gap-5 mt-3">
                    <input type="hidden" name="id_test" value="<?php echo $id_test; ?>">
                    <div>
                        <input class="form-control" type="text" id="titolo" name="titolo" placeholder="Nome del test" required <?php echo 'value="'.$test["titolo"].'"';  ?>>
                    </div>
                    <div>
                        <input class="form-control" type="text" id="descrizione" name="descrizione" placeholder="Descrizione" <?php echo 'value="'.$test["descrizione"].'"';  ?>>
                    </div>
                </div>
            </div>

            <div class="domande-container">
                <?php foreach ($domande as $index => $domanda): ?>
                    <div class="border p-3 mt-3">
                        <input type="hidden" name="domanda_ids[]" value="<?php echo $domanda['id']; ?>">
                        <input type="hidden" name="tipo[]" value="<?php echo $domanda['tipo']; ?>">
                        <div class="form-group">
                            <label for="domanda_<?php echo $index; ?>" class="d-flex justify-content-between">
                                <h5>Domanda <?php echo $index + 1; ?></h5>
                                <button type="button" class="btn btn-danger delete-question mb-2"><i class="bi bi-x-lg"></i></button>
                            </label>
                            <input type="text" class="form-control" name="testo[]" value="<?php echo htmlspecialchars($domanda['testo']); ?>" placeholder="Scrivi domanda">
                        </div>
                        
                        <?php if ($domanda['tipo'] === 'multipla'): ?>
                            <div class="form-group mt-2">
                                <label>Opzioni</label>
                                <?php foreach ($grouped_options[$domanda['id']] as $i => $opzione): ?>
                                    <input type="text" class="form-control mb-2" name="opzioni[<?php echo $index; ?>][<?php echo $i; ?>]" value="<?php echo htmlspecialchars($opzione['testo']); ?>" placeholder="Opzione <?php echo $i + 1; ?>">
                                <?php endforeach; ?>
                            </div>
                            <div class="form-group mt-2">
                                <label for="risposta">Risposta corretta</label>
                                <select class="form-select" name="rispostaCorretta[<?php echo $index; ?>]">
                                    <?php foreach ($grouped_options[$domanda['id']] as $i => $opzione): ?>
                                        <option value="<?php echo $i + 1; ?>" <?php echo $opzione['corretta'] ? 'selected' : ''; ?>>
                                            <?php echo $i + 1; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="ms-auto">
                <button class="btn btn-success my-auto" id="salva_modifiche" name="salva_modifiche"><i class="bi bi-floppy me-2"></i>Salva modifiche</button>
            </div>

        </form>

        <div class="d-flex justify-content-center gap-3">
            <select class="form-select" style="width: auto;" name="tipoNuovaDomanda" id="tipoNuovaDomanda">
                <option value="aperta">Domanda aperta</option>
                <option value="multipla">Domanda risposta multipla</option>
            </select>
            <button class="btn btn-success my-auto" id="aggiungi-domanda"><i class="bi bi-plus-lg"></i></button>
        </div>
    </div>

    <script>

        let nDom = 1;
        let nMultipla = 0;

        document.addEventListener('DOMContentLoaded', function () {
            nDom = document.querySelectorAll('.domande-container .border').length + 1;
            nMultipla = document.querySelectorAll('input[name="tipo[]"][value="multipla"]').length + 1;
        });

        
        document.getElementById('aggiungi-domanda').addEventListener('click', function (event) {
            event.preventDefault(); // Prevent form submission or page reload

            const questionContainer = document.createElement('div');
            questionContainer.classList.add('border', 'p-3', 'mt-3');

            const tipoDomanda = document.getElementById('tipoNuovaDomanda').value;
            
            if (tipoDomanda === 'aperta') {
                // domanda aperta
                questionContainer.innerHTML = `
                    <input type="hidden" name="domanda_ids[]" value="">
                    <input type="hidden" name="tipo[]" value="aperta">
                    <div class="form-group">
                        <label for="aperta" class="d-flex justify-content-between">
                            <h5>Domanda ${nDom}</h5>
                            <button type="button" class="btn btn-danger delete-question mb-2"><i class="bi bi-x-lg"></i></button>
                        </label>
                        <input type="text" class="form-control" name="testo[]" placeholder="Scrivi domanda">
                    </div>
                `;
                nDom++;
            } else if (tipoDomanda === 'multipla') {
                // domanda a risposta multipla
                questionContainer.innerHTML = `
                    <input type="hidden" name="domanda_ids[]" value="">
                    <input type="hidden" name="tipo[]" value="multipla">
                    <div class="form-group">
                        <label for="aperta" class="d-flex justify-content-between">
                            <h5>Domanda ${nDom}</h5>
                            <button type="button" class="btn btn-danger delete-question mb-2"><i class="bi bi-x-lg"></i></button>
                        </label>
                        <input type="text" class="form-control" name="testo[]" placeholder="Inserisci domanda">
                    </div>
                    <div class="form-group mt-2">
                        <label>Opzioni</label>
                        <input type="text" class="form-control mb-2" name="opzioni[${nDom-1}][0]" placeholder="Opzione 1">
                        <input type="text" class="form-control mb-2" name="opzioni[${nDom-1}][1]" placeholder="Opzione 2">
                        <input type="text" class="form-control mb-2" name="opzioni[${nDom-1}][2]" placeholder="Opzione 3">
                        <input type="text" class="form-control mb-2" name="opzioni[${nDom-1}][3]" placeholder="Opzione 4">
                    </div>
                    <div class="form-group mt-2">
                        <label for="risposta">Risposta corretta</label>
                        <select class="form-select" name="rispostaCorretta[${nDom-1}]" id="risposta">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                        </select>
                    </div>
                    `;
                nDom++;
                nMultipla++;
            }

            document.querySelector('.domande-container').appendChild(questionContainer);
        });


        document.querySelector('.domande-container').addEventListener('click', function (event) {
            if (event.target.classList.contains('delete-question') || event.target.closest('.delete-question')) {
                const questionElement = event.target.closest('.border');
                
                // Decrement nDom for any question removed
                nDom--;

                questionElement.remove();

                updateQuestionLabelsAndOptions();
            }
        });

        // Update question labels and options
        function updateQuestionLabelsAndOptions() {
            const questions = document.querySelectorAll('.domande-container .border');
            
            let questionNumber = 0;

            questions.forEach((question) => {
                const label = question.querySelector('label');
                if (label) {
                    questionNumber++;
                    label.innerHTML = `<h5>Domanda ${questionNumber}</h5>`;
                }

                const options = question.querySelectorAll('input[name^="opzioni"]');
                options.forEach((option, index) => {
                    option.name = `opzioni[${questionNumber-1}][${index}]`;
                });

                const correctAnswerSelect = question.querySelector('select[name^="rispostaCorretta"]');
                if (correctAnswerSelect) {
                    correctAnswerSelect.name = `rispostaCorretta[${questionNumber-1}]`;
                }

            });
        }


    </script>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script> 
    
</body>
</html>