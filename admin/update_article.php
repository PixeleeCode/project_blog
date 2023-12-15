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

// Charger les dépendances PHP
// require_once '../vendor/autoload.php';

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

    // Applique une fonction sur les valeurs d'un tableau
    $categories = array_map('strip_tags', $_POST['categories']);

    // Vérifier si les champs sont complétés
    if (!empty($title) && !empty($content)) {

        // Sélectionner le nom de l'image actuellement en BDD
        $selectCoverQuery = $bdd->prepare("SELECT cover FROM articles WHERE id = :id");
        $selectCoverQuery->bindValue(':id', $id);
        $selectCoverQuery->execute();

        // Récupération de la valeur de la colonne et stockage de l'info dans une variable
        $cover = $selectCoverQuery->fetchColumn();

        // Vérifie si un upload doit être fait
        if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {

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

                    // Supprime l'ancienne image
                    if (file_exists("../public/uploads/$cover")) {
                        // Supprime l'image à l'endroit indiqué
                        unlink("../public/uploads/$cover");
                    }

                    // Renomme le nom de l'image
                    /*$slugify = new \Cocur\Slugify\Slugify();
                    $newName = $slugify->slugify("$title-$id");
                    $cover = "$newName.$extension";

                    var_dump($cover);
                    exit;*/

                    // Télécharge la nouvelle image sous le nouveau nom

                } else {
                    $_SESSION['error'] = "L'image ne doit pas dépasser les 1Mo";
                    header("Location: edit.php?id=$id");
                    exit;
                }
            } else {
                $_SESSION['error'] = "Le fichier n'est pas une image conforme";
                header("Location: edit.php?id=$id");
                exit;
            }
        }

        // Mise à jour du titre et du contenu de l'article dans la table "articles"
        $query = $bdd->prepare("
            UPDATE articles SET title = :title, content = :content, cover = :cover WHERE id = :id
        ");

        $query->bindValue(':title', $title);
        $query->bindValue(':content', $content);
        $query->bindValue(':cover', $cover);
        $query->bindValue(':id', $id);
        $query->execute();

        // Mise à jour des catégories liées à l'article
        $deleteQuery = $bdd->prepare("DELETE FROM articles_categories WHERE article_id = :id");
        $deleteQuery->bindValue(':id', $id);
        $deleteQuery->execute();

        $insertCategoryQuery = $bdd->prepare("
            INSERT INTO articles_categories (article_id, category_id) VALUES (:article_id, :category_id)
        ");

        foreach($categories as $category) {
            $insertCategoryQuery->bindValue(':article_id', $id);
            $insertCategoryQuery->bindValue(':category_id', $category);
            $insertCategoryQuery->execute();
        }

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
