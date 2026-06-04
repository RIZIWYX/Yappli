<?php
require_once __DIR__ . '/../src/header.php';

$champs = donnees_session();
if ($champs === null) {
    header('Location: connexion.html');
    exit;
}

debut_page('Profil utilisateur', 'profil_utilisateur.css');

$username = $champs[0];

echo "<div class='utilisateur'><div class='info'>";
echo "<h2>Informations de l'utilisateur :</h2>";
echo "<p><strong>Nom d'utilisateur :</strong> " . $champs[0] . "</p>";
echo "<p><strong>Adresse mail :</strong> " . ($champs[3] ?? '') . "</p>";
echo "<p><strong>Prénom :</strong> " . ($champs[4] ?? '') . "</p>";
echo "<p><strong>Nom :</strong> " . ($champs[5] ?? '') . "</p>";
echo "</div>";

$avatar = AVATAR_DEFAUT;
$bio = '';
if (file_exists(FICHIER_SESSION_PERSO)) {
    $p = explode(';', trim((string) @file_get_contents(FICHIER_SESSION_PERSO)));
    $avatar = $p[1] ?? AVATAR_DEFAUT;
    $bio    = $p[2] ?? '';
}
echo "<img class='avatar' src='" . ASSETS . "/img/avatar/$avatar.jpg' alt='Avatar'>";
echo "<br><br><div class='bio'><h2>Bio :</h2><br>" . $bio . "</div>";

echo "<div class='boutton'><h2 class='titre'>Yapply</h2>";
echo "<a class='retour' href='accueil.php'>Retour</a>";
echo "<a class='modifier' href='modifier.html'>Modifier</a>";
echo "</div></div>";

if (file_exists(FICHIER_POSTS)) {
    echo "<h3 class='post default'>Posts de $username :</h3><br>";
    $aDesPosts = false;
    foreach (file(FICHIER_POSTS, FILE_IGNORE_NEW_LINES) as $lp) {
        $pd = explode(';', trim($lp));
        if (($pd[0] ?? '') === $username && isset($pd[1])) {
            $contenu = explode('|', trim($pd[1]));
            echo "<br><br><p class='post'>" . $contenu[0] . "</p>";
            $aDesPosts = true;
        }
    }
    if (!$aDesPosts) { echo "<p>Aucun post trouvé pour cet utilisateur.</p>"; }
}

fin_page();
