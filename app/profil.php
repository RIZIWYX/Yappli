<?php
require_once __DIR__ . '/../src/header.php';

if (utilisateur_courant() === null) {
    header('Location: connexion.php');
    exit;
}

debut_page('Profil utilisateur', 'profil.css');
barre_navigation();

$username = $_POST['username'] ?? '';
$trouve = false;

echo "<main class='page profil'>";

if ($username !== '' && file_exists(FICHIER_COMPTES)) {
    foreach (file(FICHIER_COMPTES, FILE_IGNORE_NEW_LINES) as $ligne) {
        $data = explode(';', trim($ligne));
        if (($data[0] ?? '') === $username) {
            $trouve = true;

            $avatar = AVATAR_DEFAUT;
            $bio = '';
            if (file_exists(FICHIER_PERSO)) {
                foreach (file(FICHIER_PERSO, FILE_IGNORE_NEW_LINES) as $lp) {
                    $cp = explode(';', trim($lp));
                    if (($cp[0] ?? '') === $username) {
                        $avatar = $cp[1] ?? AVATAR_DEFAUT;
                        $bio    = $cp[2] ?? '';
                        break;
                    }
                }
            }

            echo "<section class='carte profil-carte'>";
            echo "  <img class='avatar-rond grand' src='" . url_avatar($avatar) . "' alt='Avatar'>";
            echo "  <div class='profil-infos'>";
            echo "    <h2>" . $data[0] . "</h2>";
            echo "    <dl>";
            echo "      <dt>Nom d'utilisateur</dt><dd>" . $data[0] . "</dd>";
            echo "      <dt>Adresse mail</dt><dd>" . ($data[3] ?? '') . "</dd>";
            echo "      <dt>Prénom</dt><dd>" . ($data[4] ?? '') . "</dd>";
            echo "      <dt>Nom</dt><dd>" . ($data[5] ?? '') . "</dd>";
            echo "    </dl>";
            echo "    <div class='bio'><h3>Bio</h3><p>" . $bio . "</p></div>";
            echo "  </div>";
            echo "  <div class='profil-actions'>";
            echo "    <form action='amis.php' method='POST'><input type='hidden' name='username' value='$username'><button class='btn' type='submit'><i class='bx bx-user-plus'></i>Ajouter en ami</button></form>";
            echo "    <form action='message.php' method='POST'><input type='hidden' name='avec' value='$username'><button class='btn btn-secondaire' type='submit'><i class='bx bx-message-rounded-dots'></i>Envoyer un message</button></form>";
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
            break;
        }
    }
}

if (!$trouve) {
    echo "<section class='carte notice profil-introuvable'>";
    echo "<i class='bx bx-search-alt notice-icone'></i>";
    echo "<h2>Aucun utilisateur trouvé avec ce nom d'utilisateur.</h2>";
    echo "<a class='btn' href='accueil.php'>Retour</a>";
    echo "</section>";
}

echo "</main>";

fin_page();
