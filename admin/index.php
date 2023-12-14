<?php

// Démarrer une session
session_start();

// Vérifie si l'utilisateur peut accéder à cette page
if (isset($_SESSION['user'])) {
    header('Location: admin/dashboard.php');
    exit;
}

?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Connexion à l'administration</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    </head>
    <body>
        <div class="container p-3 pt-5 d-flex justify-content-center align-items-center">
            <form action="login.php" method="post">
                <h2 class="mb-4">
                    Connexion à l'administration
                </h2>

                <!-- Mes erreurs ici -->
                <?php
                    if (isset($_SESSION['error'])):
                ?>
                    <div class="alert alert-danger">
                        <?php echo $_SESSION['error']; ?>
                    </div>
                <?php
                    // Destruction de la session "error"
                    unset($_SESSION['error']);
                    endif
                ?>

                <div class="mb-3">
                    <label for="email" class="form-label">Adresse email</label>
                    <input type="email" class="form-control" id="email" name="email">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <div class="d-grid gap-2 d-md-block">
                    <button class="btn btn-primary">
                        Se connecter
                    </button>
                </div>
            </form>
        </div>
    </body>
</html>
