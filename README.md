# Yapply

Petit réseau social développé en **HTML / CSS / PHP**, sans base de données SQL :
les données sont persistées dans des fichiers texte plats.

![Démo : publication, commentaires et navigation dans le réseau social](doc/demo.gif)

Projet réalisé dans le cadre de l'UE **Outils de Développement Web** (2024-2025,
L1 IEEEA ) par **Rayane Graine** et **Accyl Benajaoud**.

> Les aspects **sécurité** sont
> volontairement laissés en l'état (voir « Limites connues ») : ce dépôt sert de
> **base** à un second projet d'audit et de sécurisation.

---

## Structure du projet

```
Yapply/
├── index.php                  # Redirige vers app/index.php
├── app/                       # Pages et traitements (tout ce qui est servi)
│   ├── index.php              # Page d'accueil publique
│   ├── inscription.php / connexion.php / modifier.php
│   ├── accueil.php            # Fil d'actualité + recherche, amis, notifications
│   ├── profil.php             # Profil d'un autre membre (recherche)
│   ├── profil_utilisateur.php # Son propre profil
│   ├── personnalisation.php   # Choix d'avatar + bio
│   ├── discussion.php         # Liste des conversations
│   ├── message.php            # Conversation privée
│   └── *_traitement.php, post.php, like.php, commentaire.php, amis.php, ...
│                              #   -> scripts de traitement des formulaires (POST)
├── src/                       # Code commun
│   ├── config.php             # Chemins des données + fonctions partagées (session, avatars)
│   └── header.php             # Gabarit commun : <head>, bandeau de navigation, pied, pages de résultat
├── assets/
│   ├── css/
│   │   ├── base.css           # Variables de la DA, reset, boutons, champs, cartes, bandeau
│   │   └── pages/             # Une feuille par écran (accueil, auth, profil, discussion…)
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

Puis ouvrir : `http://localhost:8000`

> Important : lancer le serveur depuis la racine `Yapply/` (et **non** depuis `app/`),
> pour que `assets/`, `src/` et `data/` restent accessibles.

### Avec XAMPP / WAMP / MAMP
Copier le dossier `Yapply/` dans `htdocs/` (XAMPP) ou `www/` (WAMP), démarrer Apache,
puis ouvrir `http://localhost/Yapply/`.

### Permissions
Le dossier `data/` et ses sous-dossiers doivent être accessibles en écriture par le serveur.
Les données démarrent **vides** : créez un compte via la page d'inscription.

---

## Licence

Projet académique — usage pédagogique.
