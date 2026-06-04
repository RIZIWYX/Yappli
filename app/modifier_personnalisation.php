<?php
require_once __DIR__ . '/../src/config.php';

$username = utilisateur_courant();
if ($username === null) {
    header('Location: connexion.html');
    exit;
}

function maj_fichier(string $fichier, string $pseudo, string $nouvelleLigne): void {
    if (!file_exists($fichier)) {
        file_put_contents($fichier, $nouvelleLigne . "\n", FILE_APPEND);
        return;
    }
    $sortie = '';
    $trouve = false;
    foreach (file($fichier, FILE_IGNORE_NEW_LINES) as $l) {
        $champs = explode(';', $l);
        if (($champs[0] ?? '') === $pseudo) { $sortie .= $nouvelleLigne . "\n"; $trouve = true; }
        else { $sortie .= $l . "\n"; }
    }
    if (!$trouve) { $sortie .= $nouvelleLigne . "\n"; }
    file_put_contents($fichier, $sortie);
}

$bio    = $_POST['biographie'] ?? '';
$avatar = $_POST['avatar_number'] ?? '';

$ancien = file_exists(FICHIER_SESSION_PERSO)
    ? explode(';', trim((string) file_get_contents(FICHIER_SESSION_PERSO)))
    : [];
if ($avatar === '') { $avatar = $ancien[1] ?? AVATAR_DEFAUT; }
if ($bio === '')    { $bio    = $ancien[2] ?? ''; }

$nouvelleLigne = "$username;$avatar;$bio";
maj_fichier(FICHIER_PERSO, $username, $nouvelleLigne);
maj_fichier(FICHIER_SESSION_PERSO, $username, $nouvelleLigne);

header('Location: accueil.php');
exit;
