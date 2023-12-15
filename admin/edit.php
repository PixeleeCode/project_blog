<?php

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

// Sélectionne toutes les catégories
$query = $bdd->query("SELECT * FROM categories");
$categories = $query->fetchAll();

// Sélectionne toutes les catégories liées à l'article
$query = $bdd->prepare("SELECT category_id FROM articles_categories WHERE article_id = :id");
$query->bindValue(':id', $articleId);
$query->execute();

/**
 * PDO::FETCH_COLUMN
 * Retourne un tableau indexé contenant les valeurs extraites de la requête SQL pour une seule colonne
 */
$articlesCategories = $query->fetchAll(PDO::FETCH_COLUMN);

?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Administration - Edition</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </head>
    <body>
        <nav class="navbar bg-primary navbar-expand-lg" data-bs-theme="dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="dashboard.php">Administration</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link text-warning" href="logout.php">Déconnexion</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container mt-5">
            <a href="dashboard.php">Retour</a>
            <h2 class="my-4">Edition</h2>

            <form action="update_article.php?id=<?php echo $article['id']; ?>" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="title" class="form-label">Titre</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo $article['title']; ?>">
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Contenu</label>
                    <textarea class="form-control" id="content" name="content" rows="6"><?php echo $article['content']; ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="cover" class="form-label">Couverture</label>
                    <input type="file" class="form-control" id="cover" name="cover">
                </div>

                <div class="mb-3">
                    <label for="categories" class="form-label">Catégories</label>
                    <select multiple class="form-control" id="categories" name="categories[]">
                        <?php foreach($categories as $category): ?>
                            <option
                                value="<?php echo $category['id']; ?>"
                                <?php echo in_array($category['id'], $articlesCategories) ? 'selected' : '' ?>
                            >
                                <?php echo $category['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
        </div>
    </body>
</html>
