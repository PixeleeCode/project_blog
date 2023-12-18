<?php

// Connexion à la base de données
require_once 'connexion.php';
$bdd = connectBdd('root', '', 'blog_db');

// Sélectionne tous les articles avec leurs catégories
$query = $bdd->query("
    SELECT 
        articles.id, 
        articles.title, 
        articles.publication_date, 
        articles.cover, 
        articles.content,
        users.name as author, 
        GROUP_CONCAT(categories.name SEPARATOR ', ') as categories
    FROM articles
    INNER JOIN users ON articles.user_id = users.id
    INNER JOIN articles_categories ON articles.id = articles_categories.article_id
    INNER JOIN categories ON articles_categories.category_id = categories.id
    GROUP BY articles.id
    ORDER BY articles.publication_date DESC
");

$query->execute();
$articles = $query->fetchAll();

// Créer un tableau de catégories en "explosant" la chaine de caractère créée par la requête SQL
$groupedArticles = [];
foreach ($articles as $key => $article) {
    $groupedArticles[$key] = $article;
    $groupedArticles[$key]['categories'] = explode(', ', $article['categories']);
}

?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Mon blog</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    </head>
    <body>
        <div class="text-center pt-5">
            <h1>Mon merveilleux blog</h1>
        </div>

        <div class="container p-3 pt-5 d-flex flex-column justify-content-center">
            <?php foreach($groupedArticles as $article): ?>
                <article class="pb-5">
                    <!-- Titre de l'article -->
                    <h1><?php echo $article['title']; ?></h1>

                    <!-- Informations sur l'article -->
                    <small class="d-block text-secondary pb-2">
                        <?php
                            $createdAt = DateTime::createFromFormat('Y-m-d H:i:s', $article['publication_date']);
                            $date = $createdAt->format('d.m.Y');
                        ?>
                        Auteur : <?php echo $article['author']; ?> - Posté <?php echo $date; ?>
                    </small>

                    <!-- Image de couverture -->
                    <?php if (file_exists("uploads/{$article['cover']}")): ?>
                        <img
                            src="<?php echo "uploads/{$article['cover']}" ?>"
                            alt="<?php echo $article['title']; ?>"
                            class="img-fluid rounded"
                        >
                    <?php endif; ?>

                    <!-- Catégories de l'article -->
                    <ul class="py-2 list-unstyled d-flex gap-2">
                        <?php foreach ($article['categories'] as $category): ?>
                            <li>
                                <a href="#">
                                    <span class="badge rounded-pill text-bg-light">
                                        <?php echo $category ?>
                                    </span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <!-- Contenu tronqué de l'article -->
                    <p><?php echo mb_strimwidth($article['content'], 0, 100, '...'); ?></p>

                    <a href="article.php?id=<?php echo $article['id']; ?>" class="btn btn-sm btn-primary">
                        Lire la suite...
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </body>
</html>
