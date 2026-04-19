CREATE DATABASE IF NOT EXISTS `gestion_bibliotheque`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `gestion_bibliotheque`;

DROP TABLE IF EXISTS `reservation`;
DROP TABLE IF EXISTS `emprunt`;
DROP TABLE IF EXISTS `ecrire`;
DROP TABLE IF EXISTS `livre`;
DROP TABLE IF EXISTS `categorie`;
DROP TABLE IF EXISTS `auteur`;
DROP TABLE IF EXISTS `adherent`;

CREATE TABLE `adherent` (
  `id_adherent` INT NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(100) NOT NULL,
  `prenom` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `telephone` VARCHAR(20) DEFAULT NULL,
  `date_inscription` DATE NOT NULL DEFAULT (CURDATE()),
  `role` ENUM('adherent', 'bibliothecaire', 'admin') NOT NULL DEFAULT 'adherent',
  `mot_de_passe` VARCHAR(255) NOT NULL,
  `photo` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id_adherent`),
  UNIQUE KEY `uk_adherent_email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `auteur` (
  `id_auteur` INT NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(100) NOT NULL,
  `prenom` VARCHAR(100) DEFAULT NULL,
  `bio` TEXT,
  PRIMARY KEY (`id_auteur`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categorie` (
  `id_categorie` INT NOT NULL AUTO_INCREMENT,
  `libelle` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id_categorie`),
  UNIQUE KEY `uk_categorie_libelle` (`libelle`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `livre` (
  `id_livre` INT NOT NULL AUTO_INCREMENT,
  `isbn` VARCHAR(20) NOT NULL,
  `titre` VARCHAR(255) NOT NULL,
  `annee_pub` YEAR DEFAULT NULL,
  `nb_exemplaires` INT NOT NULL DEFAULT 1,
  `nb_disponible` INT NOT NULL DEFAULT 1,
  `resume` TEXT,
  `id_categorie` INT DEFAULT NULL,
  PRIMARY KEY (`id_livre`),
  UNIQUE KEY `uk_livre_isbn` (`isbn`),
  KEY `idx_livre_categorie` (`id_categorie`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ecrire` (
  `id_auteur` INT NOT NULL,
  `id_livre` INT NOT NULL,
  PRIMARY KEY (`id_auteur`, `id_livre`),
  KEY `idx_ecrire_livre` (`id_livre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `emprunt` (
  `id_emprunt` INT NOT NULL AUTO_INCREMENT,
  `id_livre` INT NOT NULL,
  `id_adherent` INT NOT NULL,
  `date_emprunt` DATE NOT NULL,
  `date_retour_prevue` DATE NOT NULL,
  `date_retour_effective` DATE DEFAULT NULL,
  `statut` ENUM('en_cours', 'rendu', 'retard') NOT NULL DEFAULT 'en_cours',
  `penalite` DECIMAL(7,2) DEFAULT 0.00,
  PRIMARY KEY (`id_emprunt`),
  KEY `idx_emprunt_livre` (`id_livre`),
  KEY `idx_emprunt_adherent` (`id_adherent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `reservation` (
  `id_reservation` INT NOT NULL AUTO_INCREMENT,
  `id_livre` INT NOT NULL,
  `id_adherent` INT NOT NULL,
  `date_reservation` DATETIME NOT NULL,
  `statut` ENUM('en_attente', 'annulee', 'notifiee', 'attribuee') NOT NULL DEFAULT 'en_attente',
  PRIMARY KEY (`id_reservation`),
  KEY `idx_reservation_livre` (`id_livre`),
  KEY `idx_reservation_adherent` (`id_adherent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
