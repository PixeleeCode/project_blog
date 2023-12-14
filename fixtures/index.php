<?php

/**
 * index.php
 * Générer toutes les fixtures
 *
 * Liste des fichiers afin de générer les jeux de données d'essais
 * dans l'ordre d'insertion en BDD
 */

/**
 * http://projet_blog.test/fixtures/index.php?truncate=1
 * Si le paramètre "truncate" est présent dans l'URL, on vide
 * nos tables SQL
 */
if (isset($_GET['truncate'])) {
    // Connexion à la base de données
    require '../connexion.php';
    $bdd = connectBdd('root', '', 'blog_db');

    /**
     * Requêtes pour vider les tables SQL
     * ATTENTION ! L'ordre est TRES important afin de ne pas avoir
     * d'erreurs sur les clés étrangères qui seraient reliées sur d'autres
     * tables SQL
     *
     * SET FOREIGN_KEY_CHECKS : Permet d'activer/désactiver
     * la vérification des contraintes des clés étrangères
     */
    $bdd->query("
        SET FOREIGN_KEY_CHECKS = 0;
        TRUNCATE articles_categories;
        TRUNCATE comments;
        TRUNCATE articles;
        TRUNCATE categories;
        TRUNCATE users;
        SET FOREIGN_KEY_CHECKS = 1;
    ");
}

require_once 'users_fixtures.php';
require_once 'categories_fixtures.php';
require_once 'articles_fixtures.php';
require_once 'comments_fixtures.php';
require_once 'articles_categories_fixtures.php';
