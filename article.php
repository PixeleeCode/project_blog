<?php

// Démarrage de la session
session_start();

// Si aucun paramètre ID ou que celui-ci est vide, redirection vers la page d'accueil
if (empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

// Connexion à la base de données
require_once 'connexion.php';
$bdd = connectBdd('root', '', 'blog_db');

// Sélectionne tous les articles avec leurs catégories
$query = $bdd->prepare("
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
    WHERE articles.id = :id
    GROUP BY articles.id
");

$query->bindValue(':id', $_GET['id']);
$query->execute();

$article = $query->fetch();

// Aucun article n'a été trouvé
if (!$article) {
    header('Location: index.php');
    exit;
}

// Créer un tableau de catégories en "explosant" la chaine de caractère créée par la requête SQL
$article['categories'] = explode(', ', $article['categories']);

// Sélection de tous les commentaires
$queryComments = $bdd->prepare("
    SELECT 
        comments.content,
        comments.comment_date,
        users.name as author
    FROM comments 
    INNER JOIN users ON comments.user_id = users.id    
    WHERE article_id = :id
    ORDER BY comments.comment_date ASC
");
$queryComments->bindValue(':id', $_GET['id']);
$queryComments->execute();

// Récupère tous les commentaires
$comments = $queryComments->fetchAll();

?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Mon blog</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <div class="text-center pt-5">
            <h1>Mon merveilleux blog</h1>
        </div>

        <div class="articles p-5">
            <article class="pb-5">
                <!-- Titre de l'article -->
                <h1><?php echo $article['title']; ?></h1>

                <!-- Informations sur l'article -->
                <small class="d-block text-secondary pb-2">
                    <?php
                        $date = DateTime::createFromFormat('Y-m-d H:i:s', $article['publication_date']);
                        $createdAt = $date->format('d.m.Y');
                    ?>
                    Auteur : <?php echo $article['author']; ?> - Posté <?php echo $createdAt ?>
                </small>

                <!-- Image de couverture -->
                <?php if(file_exists("uploads/{$article['cover']}")): ?>
                    <img
                         src="uploads/<?php echo $article['cover']; ?>"
                         alt="<?php echo $article['title']; ?>"
                         class="img-fluid rounded"
                    >
                <?php endif; ?>

                <!-- Catégories de l'article -->
                <ul class="py-2 list-unstyled d-flex gap-2">
                    <?php foreach($article['categories'] as $category): ?>
                        <li>
                            <a href="#">
                                <span class="badge rounded-pill text-bg-light">
                                    <?php echo $category; ?>
                                </span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Contenu tronqué de l'article -->
                <p><?php echo nl2br($article['content']); ?></p>
            </article>

            <section>
                <h2>Commentaires</h2>

                <?php
                    if ($comments):
                        foreach ($comments as $comment):
                ?>
                    <div class="mb-4">
                        <p class="m-0 p-0"><?php echo nl2br($comment['content']); ?></p>
                        <p class="m-0 p-0">
                            <?php
                                $date = DateTime::createFromFormat('Y-m-d H:i:s', $comment['comment_date']);
                                $postedBy = $date->format('d.m.Y');
                            ?>
                            <small>Ecrit par <?php echo $comment['author'] ?> le <?php echo $postedBy; ?></small>
                        </p>
                    </div>
                <?php
                        endforeach;
                    else:
                ?>
                    <p>Soyez le premier à commenter cet article !</p>
                <?php
                    endif;
                ?>

                <div class="pt-5" id="comments">
                    <h3 class="mb-4">Poster un commentaire</h3>

                    <!-- Message de succès -->
                    <?php if(isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <?php
                            echo $_SESSION['success'];
                            unset($_SESSION['success']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <!-- Messages d'erreurs -->
                    <?php if(isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?php
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if(isset($_SESSION['user'])): ?>
                        <form action="add_comment.php?id=<?php echo $article['id']; ?>" method="post">
                            <label for="comment">Commentaire</label>
                            <textarea name="comment" id="comment" rows="4" class="form-control mb-3 mt-1"></textarea>
                            <button class="btn btn-primary">Poster mon commentaire</button>
                        </form>
                    <?php else: ?>
                        <p>Veuillez vous connecter pour poster un commentaire</p>
                    <?php endif; ?>
                </div>

            </section>
        </div>
    </body>
</html>
