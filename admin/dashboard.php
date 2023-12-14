<?php

// Démarrer une session
session_start();

// Vérifie si l'utilisateur peut accéder à cette page
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Administration</title>
    </head>
    <body>
        <h1>Administration</h1>
        <a href="logout.php">Déconnexion</a>
    </body>
</html>
