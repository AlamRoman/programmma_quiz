<style>
    .nav_active{
        background-color: grey;
        padding: 10px;
        border-radius: 10px;
        color: white;
        border: 0px;
    }

    .nav_active:hover{
        background-color: #252525;
        color: white;
    }

    .dropdown-menu {
        margin-right: 20px;
        min-width: 150px;
    }

    .dropdown-item {
        padding: 10px;
    }

    .profile-img {
        width: 40px; 
        height: 40px; 
        border-radius: 50%; 
    }

</style>

<div class="h-100">
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <div class="container-fluid d-flex justify-content-between">
    
        <h3 class="fw-bold mb-0">Benvenuto <?php echo $_SESSION["username"]; ?>!</h3>

        <div class="d-flex align-items-center">
            <ul class="navbar-nav gap-3 me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link fw-bold <?php echo (basename($_SERVER['PHP_SELF']) == 'homeDocente.php' || basename($_SERVER['PHP_SELF']) == 'creaTest.php' || basename($_SERVER['PHP_SELF']) == 'modificaTest.php') ? 'nav_active' : ''; ?>" href="homeDocente.php">Lista Test</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-bold <?php echo (basename($_SERVER['PHP_SELF']) == 'sessione_test.php' || basename($_SERVER['PHP_SELF']) == 'creaSessioneTest.php' || basename($_SERVER['PHP_SELF']) == 'modificaSessioneTest.php') ? 'nav_active' : ''; ?>" href="sessione_test.php">Sessione test</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-bold <?php echo (basename($_SERVER['PHP_SELF']) == 'sessioneTestSvolti.php' || basename($_SERVER['PHP_SELF']) == 'riepilogo.php') ? 'nav_active' : ''; ?>" href="sessioneTestSvolti.php">Test svolti</a>
                </li>
            </ul>
        </div>

        <div class="dropdown me-2" style="background-color: transparent;">
            <button style="background-color: transparent;" class="dropdown-toggle border-0" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="resources/docente.png" class="profile-img" alt="Profile Image">
            </button>
            <ul class="dropdown-menu me-5" aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                <li><a class="dropdown-item" href="php/do_logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
