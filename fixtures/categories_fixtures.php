<?php

/**
 * categories_fixtures.php
 */

// Chargement des dépendances Composer
require_once '../vendor/autoload.php';

// Connexion à la base de données
require_once '../connexion.php';
$bdd = connectBdd('root', '', 'blog_db');

// Utilisation de la bibliothèque Faker
$faker = Faker\Factory::create();

// Préparation de la requête d'insertion des catégories
$insertUser = $bdd->prepare(
    "INSERT INTO categories (name) VALUES (:name)"
);

// Générer 10 catégories
for ($i = 0; $i < 10; $i++) {
    $insertUser->bindValue(':name', $faker->word);
    $insertUser->execute();
}
