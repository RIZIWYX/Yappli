<?php
require_once __DIR__ . '/../src/header.php';

$current_username = utilisateur_courant();
if ($current_username === null) {
    header('Location: connexion.php');
    exit;
}

if (isset($_POST['username']) && trim($_POST['username']) !== '') {
    $ami = trim($_POST['username']);
    // Nom de fichier basé sur le pseudo EXACT (corrige l'incohérence de casse qui empêchait l'affichage des amis)
    $fichier = DOSSIER_AMIS . '/' . $current_username . '.txt';
    $dejaAmi = false;

    if (file_exists($fichier)) {
        foreach (file($fichier, FILE_IGNORE_NEW_LINES) as $l) {
            if (trim($l) === $ami) { $dejaAmi = true; break; }
        }
    }

    if ($dejaAmi) {
        page_notice("$ami est déjà dans votre liste d'amis.", [], 'accueil.php', "Retour à l'accueil");
    } else {
        file_put_contents($fichier, $ami . "\n", FILE_APPEND);
        file_put_contents(DOSSIER_NOTIFS . '/' . $ami . '.txt', "$current_username vous a ajouté à sa liste d'amis\n", FILE_APPEND);
        page_notice("$ami a été ajouté à vos amis !", [], 'accueil.php', "Retour à l'accueil", false);
    }
} else {
    page_notice('Aucun utilisateur spécifié.', [], 'accueil.php', "Retour à l'accueil");
}
