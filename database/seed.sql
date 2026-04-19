USE `gestion_bibliotheque`;

-- Password for all demo users below: password
-- IMPORTANT: change these accounts in production.
INSERT INTO `adherent` (`id_adherent`, `nom`, `prenom`, `email`, `telephone`, `date_inscription`, `role`, `mot_de_passe`, `photo`) VALUES
(1, 'Admin', 'Demo', 'admin@example.com', '00000000', CURDATE(), 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL),
(2, 'Biblio', 'Demo', 'biblio@example.com', '00000001', CURDATE(), 'bibliothecaire', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL),
(3, 'Adherent', 'Demo', 'adherent@example.com', '00000002', CURDATE(), 'adherent', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL);

INSERT INTO `categorie` (`id_categorie`, `libelle`) VALUES
(1, 'Roman'),
(2, 'Informatique'),
(3, 'Science-fiction');

INSERT INTO `auteur` (`id_auteur`, `nom`, `prenom`, `bio`) VALUES
(1, 'Camus', 'Albert', 'Ecrivain et philosophe francais.'),
(2, 'Asimov', 'Isaac', 'Auteur de science-fiction.'),
(3, 'Martin', 'Robert C.', 'Auteur en ingenierie logicielle.');

INSERT INTO `livre` (`id_livre`, `isbn`, `titre`, `annee_pub`, `nb_exemplaires`, `nb_disponible`, `resume`, `id_categorie`) VALUES
(1, '978-2-07-036002-4', 'La Peste', 1947, 3, 3, 'Roman sur une epidemie et la condition humaine.', 1),
(2, '978-0-553-29335-7', 'Foundation', 1951, 2, 2, 'Saga de science-fiction sur la psychohistoire.', 3),
(3, '978-0-13-235088-4', 'Clean Code', 2008, 4, 4, 'Bonnes pratiques de programmation.', 2);

INSERT INTO `ecrire` (`id_auteur`, `id_livre`) VALUES
(1, 1),
(2, 2),
(3, 3);

INSERT INTO `emprunt` (`id_emprunt`, `id_livre`, `id_adherent`, `date_emprunt`, `date_retour_prevue`, `date_retour_effective`, `statut`, `penalite`) VALUES
(1, 1, 3, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 7 DAY), NULL, 'en_cours', 0.00);

INSERT INTO `reservation` (`id_reservation`, `id_livre`, `id_adherent`, `date_reservation`, `statut`) VALUES
(1, 2, 3, NOW(), 'en_attente');
