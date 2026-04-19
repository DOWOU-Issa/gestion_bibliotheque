# Gestion Bibliotheque

Application web de gestion d'une bibliotheque universitaire developpee en PHP/MySQL avec une architecture modulaire.

## Fonctionnalites

- Authentification par email/mot de passe avec roles `adherent`, `bibliothecaire`, `admin`
- Gestion des livres (ajout, modification, suppression, consultation)
- Gestion des auteurs (liste, details, ajout)
- Emprunts et historique des emprunts
- Reservations et attribution par bibliothecaire
- Tableau de bord avec statistiques et graphiques (Chart.js)
- Espace profil utilisateur et inscription

## Stack technique

- PHP (procedural + PDO)
- MySQL / MariaDB
- HTML/CSS/JavaScript
- Chart.js (local)

## Structure du projet

- `public/` : pages d'entree (accueil, login, dashboard, infos, contact)
- `config/` : connexion base de donnees
- `database/` : scripts SQL (`schema.sql` et `seed.sql`)
- `includes/` : fonctions utilitaires et composants communs
- `utilisateurs/` : inscription, profil, gestion utilisateurs
- `livres/` : CRUD livres
- `auteurs/` : gestion auteurs
- `emprunts/` : flux emprunt/reservation cote adherent
- `reservations/` : flux de traitement cote bibliothecaire
- `assets/` : CSS, JS, images

## Installation locale (WAMP/XAMPP)

1. Copier le projet dans le dossier web local (ex: `www/gestion_bibliotheque`).
2. Importer `database/schema.sql` pour creer la structure.
3. Importer `database/seed.sql` (optionnel) pour charger des donnees de demonstration.
4. Mettre a jour `config/config.php` avec vos identifiants locaux.
5. Ouvrir `http://localhost/gestion_bibliotheque/public/index.php`.

Import rapide en ligne de commande:

```bash
mysql -u root -p < database/schema.sql
mysql -u root -p < database/seed.sql
```

Exemple minimal de configuration PDO:

```php
<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db = 'gestion_bibliotheque';
$charset = 'utf8mb4';

$dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
$pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);
```

## Comptes et roles

Le role est lu depuis la colonne `role` de la table `adherent`.
Valeurs attendues:

- `adherent`
- `bibliothecaire`
- `admin`

Comptes de demonstration (si `database/seed.sql` est importe):

- `admin@example.com` (role: `admin`)
- `biblio@example.com` (role: `bibliothecaire`)
- `adherent@example.com` (role: `adherent`)
- Mot de passe pour les 3 comptes: `password`

## Notes importantes

- Le projet contient des ressources locales (images de profils, css/js, etc.).
- Le schema SQL est versionne dans `database/schema.sql`.
- Les donnees de demonstration sont dans `database/seed.sql`.
- Pour un usage public, ne versionnez jamais de mots de passe ou credentials reels.

## Licence

Ce projet est distribue sous licence MIT. Voir le fichier `LICENSE`.
