<?php

/**
 * comments_fixtures.php
 */

// Chargement des dépendances Composer
require_once '../vendor/autoload.php';

// Connexion à la base de données
require_once '../connexion.php';
$bdd = connectBdd('root', '', 'blog_db');

// Utilisation de la bibliothèque Faker
$faker = Faker\Factory::create();

// Préparation de la requête d'insertion des commentaires
$insertUser = $bdd->prepare("
    INSERT INTO comments (content, comment_date, user_id, article_id) 
    VALUES (:content, :comment_date, :user_id, :article_id)
");

// Sélectionne tous les utilisateurs
$query = $bdd->query("SELECT id FROM users");
$users = $query->fetchAll();

// Sélectionne tous les articles
$query = $bdd->query("SELECT id, publication_date FROM articles");
$articles = $query->fetchAll();

// Générer 200 commentaires
for ($i = 0; $i < 200; $i++) {
    // Sélectionne un utilisateur aléatoirement
    $user = $faker->randomElement($users);

    // Sélectionne un article aléatoirement
    $article = $faker->randomElement($articles);

    // Génère une date entre la date de création de l'article et aujourd'hui
    $date = $faker->dateTimeBetween($article['publication_date'])->format('Y-m-d H:i:s');

    $insertUser->bindValue(':content', $faker->text);
    $insertUser->bindValue(':comment_date', $date);
    $insertUser->bindValue(':user_id', $user['id']);
    $insertUser->bindValue(':article_id', $article['id']);
    $insertUser->execute();
}
