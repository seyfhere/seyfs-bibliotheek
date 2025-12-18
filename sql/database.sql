CREATE DATABASE IF NOT EXISTS seyfs_bibliotheek
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE seyfs_bibliotheek;

DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS authors;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  email VARCHAR(255) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB;

CREATE TABLE authors (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT UNSIGNED NOT NULL,
  name VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_authors_user (user_id),
  CONSTRAINT fk_authors_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE books (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT UNSIGNED NOT NULL,
  author_id INT UNSIGNED NOT NULL,
  title VARCHAR(255) NOT NULL,
  publication_year INT NULL,
  status ENUM('wishlist','reading','read') NOT NULL DEFAULT 'wishlist',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_books_user (user_id),
  KEY idx_books_author (author_id),
  CONSTRAINT fk_books_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_books_author
    FOREIGN KEY (author_id) REFERENCES authors(id)
    ON DELETE RESTRICT
) ENGINE=InnoDB;
