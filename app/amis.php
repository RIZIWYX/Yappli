<?php
require_once __DIR__ . '/../src/header.php';

$current_username = utilisateur_courant();
if ($current_username === null) {
    header('Location: connexion.html');
    exit;
}

debut_page('Yapply', 'php.css');

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
        echo "<div><h2>$ami est déjà dans votre liste d'amis.</h2>";
        echo "<a href='accueil.php'>Retour à l'accueil</a><br></div>";
    } else {
        file_put_contents($fichier, $ami . "\n", FILE_APPEND);
        file_put_contents(DOSSIER_NOTIFS . '/' . $ami . '.txt', "$current_username vous a ajouté à sa liste d'amis\n", FILE_APPEND);
        echo "<div><h2>$ami a été ajouté à vos amis !</h2>";
        echo "<a href='accueil.php'>Retour à l'accueil</a><br></div>";
    }
} else {
    echo "<div><h2>Aucun utilisateur spécifié.</h2>";
    echo "<a href='accueil.php'>Retour à l'accueil</a><br></div>";
}

fin_page();
