<?php
require_once __DIR__ . '/../src/header.php';

if (utilisateur_courant() === null) {
    header('Location: connexion.html');
    exit;
}

debut_page('Profil utilisateur', 'profil.css');

$username = $_POST['username'] ?? '';

if ($username !== '' && file_exists(FICHIER_COMPTES)) {
    $trouve = false;
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

            echo "<div class='utilisateur'><div class='info'>";
            echo "<h2>Informations de l'utilisateur :</h2>";
            echo "<p><strong>Nom d'utilisateur :</strong> " . $data[0] . "</p>";
            echo "<p><strong>Adresse mail :</strong> " . ($data[3] ?? '') . "</p>";
            echo "<p><strong>Prénom :</strong> " . ($data[4] ?? '') . "</p>";
            echo "<p><strong>Nom :</strong> " . ($data[5] ?? '') . "</p>";
            echo "<img class='avatar' src='" . ASSETS . "/img/avatar/$avatar.jpg' alt='Avatar' style='width:150px;height:150px;'>";
            echo "<br><br><div class='bio'><h2>Bio :</h2><br>" . $bio . "</div>";
            echo "</div>";

            if (file_exists(FICHIER_POSTS)) {
                echo "<h3 class='post default'>Posts de $username :</h3>";
                $aDesPosts = false;
                foreach (file(FICHIER_POSTS, FILE_IGNORE_NEW_LINES) as $lp) {
                    $pd = explode(';', trim($lp));
                    if (($pd[0] ?? '') === $username && isset($pd[1])) {
                        $contenu = explode('|', trim($pd[1]));
                        echo "<p class='post'>" . $contenu[0] . "</p>";
                        $aDesPosts = true;
                    }
                }
                if (!$aDesPosts) { echo "<p>Aucun post trouvé pour cet utilisateur.</p>"; }
            }

            echo "<div class='boutton'><h2 class='titre'>Yapply</h2>";
            echo "<form action='amis.php' method='POST'><input type='hidden' name='username' value='$username'><button class='retour' type='submit'>Ajouter en amis</button></form>";
            echo "<form action='message.php' method='POST'><input type='hidden' name='avec' value='$username'><button class='modifier' type='submit'>Envoyer un message</button></form>";
            echo "<a class='return' href='accueil.php'>Retour</a><br></div>";
            echo "</div>";
            break;
        }
    }
    if (!$trouve) {
        echo "<div class='cas-erreur'><h2>Aucun utilisateur trouvé avec ce nom d'utilisateur.</h2>";
        echo "<a class='lien-retour' href='accueil.php'>Retour</a></div>";
    }
}

fin_page();
