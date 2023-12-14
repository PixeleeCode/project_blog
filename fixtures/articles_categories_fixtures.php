<?php

/**
 * articles_categories_fixtures.php
 */

// Chargement des dépendances Composer
require_once '../vendor/autoload.php';

// Connexion à la base de données
require_once '../connexion.php';
$bdd = connectBdd('root', '', 'blog_db');

// Utilisation de la bibliothèque Faker
$faker = Faker\Factory::create();

// Préparation de la requête d'insertion des relations entre les articles et les catégories
$insertUser = $bdd->prepare("
    INSERT INTO articles_categories (article_id, category_id) 
    VALUES (:article_id, :category_id)
");

// Sélectionne tous les articles
$query = $bdd->query("SELECT id FROM articles");
$articles = $query->fetchAll();

// Sélectionne toutes les catégories
$query = $bdd->query("SELECT id FROM categories");
$categories = $query->fetchAll();

foreach ($articles as $article) {

    // Génère un nombre d'itérations aléatoire pour la boucle for()
    $iteration = random_int(1, 4);

    for ($j = 0; $j < $iteration; $j++) {
        // Sélectionne une catégorie aléatoirement
        $categorie = $faker->randomElement($categories);

        $insertUser->bindValue(':article_id', $article['id']);
        $insertUser->bindValue(':category_id', $categorie['id']);
        $insertUser->execute();
    }
}
