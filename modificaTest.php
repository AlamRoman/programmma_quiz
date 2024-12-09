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

    <div class="container mt-5 border p-5 d-flex flex-column gap-5">
        <form action="modificaTest.php" method="POST" class="d-flex flex-column gap-5">

            <div class="border p-3">
                <h3>Dati test</h3>
                <div class="d-flex gap-5 mt-3">
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
                        <input type="hidden" name="tipo[]" value="<?php echo $domanda['tipo']; ?>">
                        <div class="form-group">
                            <label for="domanda_<?php echo $index; ?>" class="d-flex justify-content-between">
                                <h5>Domanda <?php echo $index + 1; ?></h5>
                                <button type="button" class="btn btn-danger delete-question mb-2"><i class="bi bi-x-lg"></i></button>
                            </label>
                            <input type="text" class="form-control" name="<?php echo $domanda['tipo']; ?>[]" value="<?php echo htmlspecialchars($domanda['testo']); ?>" placeholder="Scrivi domanda">
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
                <button class="btn btn-success my-auto" id="salva_test" name="salva_test"><i class="bi bi-floppy me-2"></i>Salva Test</button>
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
                    <input type="hidden" name="tipo[]" value="aperta">
                    <div class="form-group">
                        <label for="aperta" class="d-flex justify-content-between">
                            <h5>Domanda ${nDom}</h5>
                            <button type="button" class="btn btn-danger delete-question mb-2"><i class="bi bi-x-lg"></i></button>
                        </label>
                        <input type="text" class="form-control" name="aperta[]" placeholder="Scrivi domanda">
                    </div>
                `;
                nDom++;
            } else if (tipoDomanda === 'multipla') {
                // domanda a risposta multipla
                questionContainer.innerHTML = `
                    <input type="hidden" name="tipo[]" value="multipla">
                    <div class="form-group">
                        <label for="aperta" class="d-flex justify-content-between">
                            <h5>Domanda ${nDom}</h5>
                            <button type="button" class="btn btn-danger delete-question mb-2"><i class="bi bi-x-lg"></i></button>
                        </label>
                        <input type="text" class="form-control" name="multipla[]" placeholder="Inserisci domanda">
                    </div>
                    <div class="form-group mt-2">
                        <label>Opzioni</label>
                        <input type="text" class="form-control mb-2" name="opzioni[${nMultipla}][0]" placeholder="Opzione 1">
                        <input type="text" class="form-control mb-2" name="opzioni[${nMultipla}][1]" placeholder="Opzione 2">
                        <input type="text" class="form-control mb-2" name="opzioni[${nMultipla}][2]" placeholder="Opzione 3">
                        <input type="text" class="form-control mb-2" name="opzioni[${nMultipla}][3]" placeholder="Opzione 4">
                    </div>
                    <div class="form-group mt-2">
                        <label for="risposta">Risposta corretta</label>
                        <select class="form-select" name="rispostaCorretta[]" id="risposta">
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
                
                if (questionElement.querySelector('input[name="tipo[]"][value="aperta"]')) {
                    nDom--;
                } else if (questionElement.querySelector('input[name="tipo[]"][value="multipla"]')) {
                    nDom--;
                    nMultipla--;
                }

                questionElement.remove();

                updateQuestionLabelsAndOptions();
            }
        });

        //aggiorna domande quando viene eliminato una domanda
        function updateQuestionLabelsAndOptions() {
            const questions = document.querySelectorAll('.domande-container .border');
            let questionNumber = 1;
            let multipleChoiceIndex = 0;

            questions.forEach((question) => {
                const label = question.querySelector('label');
                if (label) {
                    label.innerHTML = `<h5>Domanda ${questionNumber}<h5>`;
                    questionNumber++;
                }

                if (question.querySelector('input[name="tipo[]"][value="multipla"]')) {
                    const options = question.querySelectorAll('input[name^="opzioni"]');
                    options.forEach((option, index) => {
                        option.name = `opzioni[${multipleChoiceIndex}][${index}]`;
                    });

                    const correctAnswerSelect = question.querySelector('select[name^="rispostaCorretta"]');
                    if (correctAnswerSelect) {
                        correctAnswerSelect.name = `rispostaCorretta[${multipleChoiceIndex}]`;
                    }

                    multipleChoiceIndex++;
                }
            });
        }

    </script>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script> 
    
</body>
</html>