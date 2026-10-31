<?php
require_once __DIR__ . '/../src/header.php';

$champs = donnees_session();
if ($champs === null) {
    header('Location: connexion.php');
    exit;
}

debut_page('Mon profil', 'profil.css');
barre_navigation('profil');

$username = $champs[0];

$p      = perso_session();
$avatar = $p[1] ?? AVATAR_DEFAUT;
$bio    = $p[2] ?? '';

echo "<main class='page profil'>";

echo "<section class='carte profil-carte'>";
echo "  <img class='avatar-rond grand' src='" . url_avatar($avatar) . "' alt='Avatar'>";
echo "  <div class='profil-infos'>";
echo "    <h2>" . $champs[0] . "</h2>";
echo "    <dl>";
echo "      <dt>Nom d'utilisateur</dt><dd>" . $champs[0] . "</dd>";
echo "      <dt>Adresse mail</dt><dd>" . ($champs[3] ?? '') . "</dd>";
echo "      <dt>Prénom</dt><dd>" . ($champs[4] ?? '') . "</dd>";
echo "      <dt>Nom</dt><dd>" . ($champs[5] ?? '') . "</dd>";
echo "    </dl>";
echo "    <div class='bio'><h3>Bio</h3><p>" . $bio . "</p></div>";
echo "  </div>";
echo "  <div class='profil-actions'>";
echo "    <a class='btn' href='modifier.php'><i class='bx bx-edit-alt'></i>Modifier</a>";
echo "    <a class='btn btn-discret' href='accueil.php'><i class='bx bx-arrow-back'></i>Retour</a>";
echo "  </div>";
echo "</section>";

if (file_exists(FICHIER_POSTS)) {
    echo "<section class='profil-posts'>";
    echo "<h3>Posts de $username</h3>";
    $aDesPosts = false;
    foreach (file(FICHIER_POSTS, FILE_IGNORE_NEW_LINES) as $lp) {
        $pd = explode(';', trim($lp));
        if (($pd[0] ?? '') === $username && isset($pd[1])) {
            $contenu = explode('|', trim($pd[1]));
            echo "<p class='carte post'>" . $contenu[0] . "</p>";
            $aDesPosts = true;
        }
    }
    if (!$aDesPosts) { echo "<p class='carte vide'>Aucun post trouvé pour cet utilisateur.</p>"; }
    echo "</section>";
}

echo "</main>";

fin_page();
