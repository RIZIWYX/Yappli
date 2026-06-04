<?php


define('RACINE', dirname(__DIR__));            // dossier racine du projet
define('DOSSIER_DONNEES', RACINE . '/data');   // « base de données » en fichiers texte

/* --- Fichiers de données (chemins disque) --- */
define('FICHIER_COMPTES',       DOSSIER_DONNEES . '/comptes.txt');         // pseudo;mdp;mdp;mail;prenom;nom
define('FICHIER_PERSO',         DOSSIER_DONNEES . '/personnalisation.txt'); // pseudo;avatar;bio
define('FICHIER_POSTS',         DOSSIER_DONNEES . '/posts.txt');            // pseudo;contenu|likes|commentaires
define('FICHIER_SESSION',       DOSSIER_DONNEES . '/session.txt');          // utilisateur connecté
define('FICHIER_SESSION_PERSO', DOSSIER_DONNEES . '/session_perso.txt');

/* --- Dossiers de données (chemins disque) --- */
define('DOSSIER_IMAGES',   DOSSIER_DONNEES . '/images');        // images des posts
define('DOSSIER_AMIS',     DOSSIER_DONNEES . '/amis');
define('DOSSIER_NOTIFS',   DOSSIER_DONNEES . '/notifications');
define('DOSSIER_MESSAGES', DOSSIER_DONNEES . '/messagerie');

/* --- URL relatives (depuis une page de app/) --- */
define('ASSETS',     '../assets');       // css, logo, avatars
define('URL_IMAGES', '../data/images');  // images des posts (affichage web)
define('AVATAR_DEFAUT', 'avatar1');      // avatar par défaut

/** Renvoie tous les champs du compte connecté (tableau), ou null si personne. */
function donnees_session(): ?array {
    if (!file_exists(FICHIER_SESSION)) {
        return null;
    }
    $ligne = trim((string) @file_get_contents(FICHIER_SESSION));
    if ($ligne === '') {
        return null;
    }
    return explode(';', $ligne);
}

/** Renvoie le pseudo de l'utilisateur connecté, ou null. */
function utilisateur_courant(): ?string {
    $champs = donnees_session();
    return $champs[0] ?? null;
}
