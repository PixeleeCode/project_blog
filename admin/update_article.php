<?php

/**
 * update_article.php
 * Mise à jour d'un article en BDD
 */

/**
 * 1. Seule une personne connectée peut y accéder
 * 2. Vérifier si la méthode du formulaire reçue est bien "POST"
 * 3. Connexion à la base de données
 * 4. Récupérer et nettoyer les données
 * 5. Mise à jour du titre et du contenu de l'article dans la table "articles"
 * 6. Redirection vers le formulaire d'édition avec un message de succès
 */

// Démarrer une session
session_start();

// Vérifie si l'utilisateur peut accéder à cette page
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

// Vérifier si la méthode du formulaire reçue est bien "POST"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Connexion à la base de données
    require_once  '../connexion.php';
    $bdd = connectBdd('root', '', 'blog_db');

    // Récupérer et nettoyer les données
    $id = $_GET['id'];
    $title = htmlspecialchars(strip_tags($_POST['title']));
    $content = htmlspecialchars(strip_tags($_POST['content']));

    // Vérifier si les champs sont complétés
    if (!empty($title) && !empty($content)) {

        // Mise à jour du titre et du contenu de l'article dans la table "articles"
        $query = $bdd->prepare("
            UPDATE articles SET title = :title, content = :content WHERE id = :id
        ");

        $query->bindValue(':title', $title);
        $query->bindValue(':content', $content);
        $query->bindValue(':id', $id);
        $query->execute();

        // Message de succès
        $_SESSION['success'] = 'Les modifications ont bien été prise en compte';

    } else {
        $_SESSION['error'] = 'Le titre et le contenu est obligatoire';
    }

    // Redirection vers le formulaire d'édition
    header("Location: edit.php?id=$id");
    exit;

} else {
    header('Location: dashboard.php');
    exit;
}
