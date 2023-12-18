<?php

/**
 * delete_article.php
 * Suppression d'un article
 */

// Démarrer une session
session_start();

// Vérifie si l'utilisateur peut accéder à cette page
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

// Vérifie si le paramètre "id" est présent et/ou non vide
if (empty($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

// Connexion à la base de données
require_once '../connexion.php';
$bdd = connectBdd('root', '', 'blog_db');

// Récupération de l'ID de l'article
$articleId = $_GET['id'];

// Sélection de l'article en BDD
$query = $bdd->prepare("SELECT * FROM articles WHERE id = :id");
$query->bindValue(':id', $articleId);
$query->execute();

// fetch() car je récupère qu'un seul article
$article = $query->fetch();

// Si aucun article n'existe avec cet ID, redirection vers la dashboard.php
// Vérifier que l'article sélectionné appartient bien à l'utilisateur connecté
if (!$article || $article['user_id'] !== $_SESSION['user']['id']) {
    header('Location: dashboard.php');
    exit;
}

// Supprime l'article
$query = $bdd->prepare("DELETE FROM articles WHERE id = :id");
$query->bindValue(':id', $articleId);
$query->execute();



$_SESSION['success'] = "L'article a été correctement supprimé";

header('Location: dashboard.php');
