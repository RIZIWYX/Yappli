# Yapply

Petit réseau social développé en **HTML / CSS / PHP**, sans base de données SQL :
les données sont persistées dans des fichiers texte plats.

Projet réalisé dans le cadre de l'UE **Outils de Développement Web** (2024-2025,
L1 IEEEA ) par **Rayane Graine** et **Accyl Benajaoud**.

> **Version restructurée.** Cette version corrige l'organisation du dépôt, plusieurs
> bugs fonctionnels et l'hygiène des données. Les aspects **sécurité** sont
> volontairement laissés en l'état (voir « Limites connues ») : ce dépôt sert de
> **base** à un second projet d'audit et de sécurisation.

---

## Structure du projet

```
Yappli/
├── app/                       # Pages et traitements (tout ce qui est servi)
│   ├── index.html             # Page d'accueil publique
│   ├── inscription.html / connexion.html / modifier.html
│   ├── accueil.php            # Fil d'actualité + recherche, amis, notifications
│   ├── profil.php             # Profil d'un autre membre (recherche)
│   ├── profil_utilisateur.php # Son propre profil
│   ├── personnalisation.php   # Choix d'avatar + bio
│   ├── discussion.php         # Liste des conversations
│   ├── message.php            # Conversation privée
│   └── *_traitement.php, post.php, like.php, commentaire.php, amis.php, ...
│                              #   -> scripts de traitement des formulaires (POST)
├── src/                       # Code commun
│   ├── config.php             # Chemins des données + fonctions partagées
│   └── header.php             # En-tête HTML commun (<head>, fonctions debut_page/fin_page)
├── assets/
│   ├── css/                   # Feuilles de style
│   └── img/                   # logo.png + avatar/avatar1..9.jpg
├── data/                      # « Base de données » en fichiers (contenu NON versionné)
│   ├── comptes.txt            # pseudo;mdp;mdp;mail;prenom;nom
│   ├── personnalisation.txt   # pseudo;avatar;bio
│   ├── posts.txt              # pseudo;contenu|likes|commentaires
│   ├── session.txt            # utilisateur connecté (fichier partagé — limite connue)
│   ├── session_perso.txt
│   ├── images/                # images uploadées dans les posts
│   ├── amis/<pseudo>.txt
│   ├── notifications/<pseudo>.txt
│   └── messagerie/A_B.txt
├── doc/                       # Documentation (sujet du projet, futur write-up de sécurité)
├── tests/                     # (réservé aux tests automatisés)
├── .gitignore
└── README.md
```

---

## Installation et lancement

### Prérequis
- PHP 7.4 ou supérieur (testé avec PHP 8.3)
- Un serveur web, ou le serveur intégré de PHP

### Avec le serveur PHP intégré (le plus simple)
Depuis la **racine du projet** :

```bash
php -S localhost:8000
```

Puis ouvrir : `http://localhost:8000/app/index.html`

> Important : lancer le serveur depuis la racine `Yappli/` (et **non** depuis `app/`),
> pour que `assets/`, `src/` et `data/` restent accessibles.

### Avec XAMPP / WAMP / MAMP
Copier le dossier `Yappli/` dans `htdocs/` (XAMPP) ou `www/` (WAMP), démarrer Apache,
puis ouvrir `http://localhost/Yappli/app/index.html`.

### Permissions
Le dossier `data/` et ses sous-dossiers doivent être accessibles en écriture par le serveur.
Les données démarrent **vides** : créez un compte via la page d'inscription.

---

## Corrections apportées dans cette version

- **Hygiène des données** : aucun compte ni mot de passe n'est versionné ; un
  `.gitignore` exclut désormais tout le contenu de `data/` (les `.gitkeep`
  préservent l'arborescence).
- **Bugs fonctionnels corrigés** :
  - connexion **par adresse mail** (mauvais indice de champ : `[2]` → `[3]`) ;
  - **likes et commentaires** ciblent désormais le **bon post** (identifiant
    transmis dynamiquement, au lieu d'un identifiant codé en dur) ;
  - écriture des **commentaires** réparée (on écrivait sur un fichier ouvert en
    lecture seule) ;
  - cohérence de **casse** des noms de fichiers (amis / notifications), qui
    empêchait leur affichage.
- **Restructuration** : séparation `app/` (pages), `src/` (config + en-tête commun),
  `assets/`, `data/`, `doc/`, `tests/` ; suppression de la duplication du `<head>`
  via `src/header.php` ; centralisation des chemins dans `src/config.php`.
- **Nettoyage** : nommage harmonisé en français, fautes corrigées, fichiers de
  traitement renommés explicitement (`*_traitement.php`).

---

## Limites connues (à traiter dans la version sécurisée)

Ce dépôt est **volontairement** la version « avant sécurisation ». Points à corriger :

- **Mots de passe en clair** → utiliser `password_hash()` / `password_verify()`.
- **Sessions** : l'utilisateur courant est dans un fichier `session.txt` partagé
  (un seul utilisateur à la fois, pas de réelle frontière d'authentification)
  → passer à `$_SESSION`.
- **XSS** : les sorties (bio, posts sur les profils, messages, commentaires,
  notifications) ne sont pas systématiquement échappées → `htmlspecialchars`.
- **Upload non restreint** : aucun contrôle de type/taille des images
  (risque d'exécution de code) → valider extension + type MIME + taille, renommer.
- **Dossier `data/` exposé sur le web** → le placer hors de la racine web.
- **CSRF** : aucune protection sur les actions (formulaires) → jetons CSRF.
- **Concurrence** : écritures concurrentes non protégées (`flock`).

---

## Licence

Projet académique — usage pédagogique.
