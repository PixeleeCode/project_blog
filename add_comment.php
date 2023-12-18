<?php

/**
 * add_comment.php
 * Ajout d'un commentaire
 */

// Démarrage de la session
session_start();

// Si aucun paramètre "id" n'est pas en paramètre, redirection vers la page d'accueil
if (empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

// Connexion à la BDD
require_once 'connexion.php';
$bdd = connectBdd('root', '', 'blog_db');

// Vérifie si un article existe sous cet ID
$query = $bdd->prepare("SELECT id FROM articles WHERE id = :id");
$query->bindValue(':id', $_GET['id']);
$query->execute();

$article = $query->fetch();

// Si l'article n'existe pas, redirection vers la page d'accueil
if (!$article) {
    header('Location: index.php');
    exit;
}

$comment = htmlspecialchars(strip_tags($_POST['comment']));

// Vérifie si le champ est bien rempli
if (!empty($comment)) {

    // Insertion en BDD
    $queryInsertComment = $bdd->prepare("
        INSERT INTO comments (content, comment_date, user_id, article_id)
        VALUES (:content, :comment_date, :user_id, :article_id)
    ");

    $queryInsertComment->bindValue(':content', $comment);
    $queryInsertComment->bindValue(':comment_date', (new DateTime())->format('Y-m-d H:i:s'));
    $queryInsertComment->bindValue(':user_id', $_SESSION['user']['id']);
    $queryInsertComment->bindValue(':article_id', $_GET['id']);
    $queryInsertComment->execute();

    $_SESSION['success'] = 'Votre commentaire a été correctement publié';

} else {
    $_SESSION['error'] = 'Veuillez écrire un commentaire';
}

header("Location: article.php?id={$_GET['id']}#comments");
