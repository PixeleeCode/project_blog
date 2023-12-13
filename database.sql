CREATE DATABASE IF NOT EXISTS blog_db;
USE blog_db;

-- Users
CREATE TABLE IF NOT EXISTS users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL
);

-- Categories
CREATE TABLE IF NOT EXISTS categories (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL
);

-- Articles
CREATE TABLE IF NOT EXISTS articles (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    cover VARCHAR(255),
    publication_date DATETIME NOT NULL,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Comments
CREATE TABLE IF NOT EXISTS comments (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    content TEXT NOT NULL,
    comment_date DATETIME NOT NULL,
    user_id INT NOT NULL,
    article_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (article_id) REFERENCES articles(id)
);

-- Articles & Categories
CREATE TABLE IF NOT EXISTS articles_categories (
    article_id INT NOT NULL,
    category_id INT NOT NULL,
    FOREIGN KEY (article_id) REFERENCES articles(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
