<?php

/**
 * add_article.php
 * Ajouter un nouvel article
 */

// Démarrer une session
session_start();

// Vérifie si l'utilisateur peut accéder à cette page
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

// Chargement des dépendances PHP
require_once '../vendor/autoload.php';

// Connexion à la base de données
require_once '../connexion.php';
$bdd = connectBdd('root', '', 'blog_db');

// Vérifier si la méthode reçue est bien "POST"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Nettoyage des données reçues
    $title = htmlspecialchars(strip_tags($_POST['title']));
    $content = htmlspecialchars(strip_tags($_POST['content']));
    $cover = $_FILES['cover'];

    // Vérifier si le formulaire est entièrement rempli
    if (
        !empty($title) &&
        !empty($content) &&
        !empty($_POST['categories']) &&
        (isset($cover) && $cover['error'] === UPLOAD_ERR_OK)
    ) {
        // Nettoyage des catégories
        $categories = array_map('strip_tags', $_POST['categories']);

        $typeExt = [
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
        ];

        $sizeMax = 1 * 1024 * 1024;
        $extension = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));

        // Vérifier si le fichier est bien une image autorisée
        if (array_key_exists($extension, $typeExt) && in_array($_FILES['cover']['type'], $typeExt)) {

            // Vérifie si le poids de l'image ne dépasse pas la limite fixée
            if ($_FILES['cover']['size'] <= $sizeMax) {

                // Insérer en BDD les données
                $queryNewArticle = $bdd->prepare("
                    INSERT INTO articles (title, content, cover, publication_date, user_id) 
                    VALUES (:title, :content, :cover, :publication_date, :user_id)
                ");

                $queryNewArticle->bindValue(':title', $title);
                $queryNewArticle->bindValue(':content', $content);
                $queryNewArticle->bindValue(':cover', $cover['name']);
                // Récupère la date et l'heure du jour : new DateTime('now') OU new DateTime()
                $queryNewArticle->bindValue(':publication_date', (new DateTime('now'))->format('Y-m-d H:i:s'));
                $queryNewArticle->bindValue(':user_id', $_SESSION['user']['id']);
                $queryNewArticle->execute();

                // Récupérer l'ID de l'article nouvellement créé à l'instant
                $id = $bdd->lastInsertId();

                // Renomme le nom de l'image
                $slugify = new \Cocur\Slugify\Slugify();
                $newName = $slugify->slugify("$title-$id");
                $cover = "$newName.$extension";

                // Télécharge la nouvelle image sous le nouveau nom
                move_uploaded_file(
                    $_FILES['cover']['tmp_name'],
                    "../uploads/$cover"
                );

                /**
                 * Met à jour le nom de l'image dans la BDD
                 * On met à jour le nom de l'image après une insertion, car notre image contient l'ID de l'article
                 * que l'on ne peut pas connaitre au moment de l'insertion plus haut, donc cela doit se faire
                 * en 2 temps
                 */
                $queryUpdateCover = $bdd->prepare("UPDATE articles SET cover = :cover WHERE id = :id");
                $queryUpdateCover->bindValue(':cover', $cover);
                $queryUpdateCover->bindValue(':id', $id);
                $queryUpdateCover->execute();

                // Insertion dans la table de relation "articles_categories"
                $queryInsertRelationCategory = $bdd->prepare("
                    INSERT INTO articles_categories (article_id, category_id) VALUES (:article_id, :category_id)
                ");

                foreach($categories as $category) {
                    $queryInsertRelationCategory->bindValue(':article_id', $id);
                    $queryInsertRelationCategory->bindValue(':category_id', $category);
                    $queryInsertRelationCategory->execute();
                }

                $_SESSION['success'] = "Votre nouvel article a été correctement enregistré";

                header('Location: dashboard.php');
                exit;

            } else {
                $_SESSION['error'] = "L'image ne doit pas dépasser les 1Mo";
            }
        } else {
            $_SESSION['error'] = "Le fichier n'est pas une image conforme";
        }

    } else {
        $_SESSION['error'] = 'Tous les champs sont obligatoires';
    }
}

// Redirection vers le formulaire d'ajout
header('Location: add.php');
