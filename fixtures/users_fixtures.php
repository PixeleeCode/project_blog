<?php

/**
 * users_fixtures.php
 */

// Chargement des dépendances Composer
require_once '../vendor/autoload.php';

// Connexion à la base de données
require_once '../connexion.php';
$bdd = connectBdd('root', '', 'blog_db');

// Utilisation de la bibliothèque Faker
$faker = Faker\Factory::create();

// Préparation de la requête d'insertion d'utilisateur
$insertUser = $bdd->prepare(
    "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)"
);

// Générer 3 utilisateurs
for ($i = 0; $i < 3; $i++) {
    $insertUser->bindValue(':name', $faker->name);
    $insertUser->bindValue(':email', $faker->unique()->email);
    $insertUser->bindValue(':password', password_hash('secret', PASSWORD_DEFAULT));
    $insertUser->execute();
}
