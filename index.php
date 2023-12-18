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

echo '<pre>';
var_dump($groupedArticles);
echo '</pre>';

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
        <div class="container p-3 pt-5 d-flex justify-content-center align-items-center">

        </div>
    </body>
</html>
