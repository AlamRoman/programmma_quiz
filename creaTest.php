<?php 

    session_start();

    include "include/functions.ini";
    include "include/db.php";

    if (!isset($_SESSION["is_logged_in"]) || $_SESSION["is_logged_in"] == false) {
        go_to("login_page.php");
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crea test</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid d-flex justify-content-between">

            <h3 class="fw-bold">Benvenuto <?php echo $_SESSION["username"]; ?>!</h3>

            <h4 class="fw-bold text-center" style="transform: translateX(-50%);">Crea Test</h4>

            <form class="d-flex" method="POST" action="php/do_logout.php">
                <input class="btn btn-secondary ms-auto" type="submit" id="logout" name="logout" value="logout">
            </form>
        </div>
    </nav>

    <div class="container mt-5 border p-5 d-flex flex-column gap-5">
        <form action="#" class="d-flex flex-column gap-5">
            <div class="border p-3">
                <h3>Dati test</h3>
                <div class="d-flex gap-5 mt-3">
                    <div>
                        <input class="form-control" type="text" id="titolo" name="titolo" placeholder="Nome del test">
                    </div>
                    <div>
                        <input class="form-control" type="text" id="descrizione" name="descrizione" placeholder="Descrizione">
                    </div>
                </div>
            </div>

            <div class="domande-container">
                
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
        
        document.getElementById('aggiungi-domanda').addEventListener('click', function (event) {
            event.preventDefault(); // Prevent form submission or page reload

            // Create a new question container
            const questionContainer = document.createElement('div');
            questionContainer.classList.add('border', 'p-3', 'mt-3');

            const tipoDomanda = document.getElementById('tipoNuovaDomanda').value;
            
            if (tipoDomanda === 'aperta') {
                // Structure for "Domanda aperta"
                questionContainer.innerHTML = `
                    <input type="hidden" name="tipo[]" value="aperta">
                    <div class="form-group">
                        <label for="aperta">Domanda ${nDom}</label>
                        <input type="text" class="form-control" name="aperta[]" placeholder="Scrivi domanda">
                    </div>
                `;
                nDom++;
            } else if (tipoDomanda === 'multipla') {
                // Structure for "Domanda risposta multipla"
                questionContainer.innerHTML = `
                    <input type="hidden" name="tipo[]" value="multipla">
                    <div class="form-group">
                        <label for="multipla">Domanda ${nDom}</label>
                        <input type="text" class="form-control" name="multipla[]" placeholder="Inserisci domanda">
                    </div>
                    <div class="form-group mt-2">
                        <label>Opzioni</label>
                        <input type="text" class="form-control mb-2" name="opzioni[][0]" placeholder="Opzione 1">
                        <input type="text" class="form-control mb-2" name="opzioni[][1]" placeholder="Opzione 2">
                        <input type="text" class="form-control mb-2" name="opzioni[][2]" placeholder="Opzione 3">
                        <input type="text" class="form-control mb-2" name="opzioni[][3]" placeholder="Opzione 4">
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
            }

            document.querySelector('.domande-container').appendChild(questionContainer);
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script> 
    
</body>
</html>