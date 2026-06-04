<?php
require_once __DIR__ . '/../src/config.php';

$username = utilisateur_courant();
if ($username === null) {
    header('Location: connexion.html');
    exit;
}

$avatar = $_POST['avatar'] ?? '';
$bio    = $_POST['text'] ?? '';

$ligne = "$username;$avatar;$bio\n";
file_put_contents(FICHIER_PERSO, $ligne, FILE_APPEND);
file_put_contents(FICHIER_SESSION_PERSO, $ligne);

header('Location: accueil.php');
exit;
