<?php
require_once __DIR__ . '/../src/header.php';

$current_username = utilisateur_courant();
if ($current_username === null) {
    header('Location: connexion.html');
    exit;
}

debut_page('Discussions', 'discussion.css');
?>
<h1 class="liste_de_discussions">votre liste d'amis</h1>
<div>
<?php
if (is_dir(DOSSIER_MESSAGES)) {
    $avatars = [];
    if (file_exists(FICHIER_PERSO)) {
        foreach (file(FICHIER_PERSO, FILE_IGNORE_NEW_LINES) as $l) {
            $info = explode(';', trim($l));
            if (count($info) >= 2) { $avatars[$info[0]] = $info[1]; }
        }
    }
    $trouve = false;
    foreach (scandir(DOSSIER_MESSAGES) as $fichier) {
        if ($fichier === '.' || $fichier === '..') { continue; }
        $nom = str_replace('.txt', '', $fichier);
        $parts = explode('_', $nom);
        if (count($parts) < 2) { continue; }
        if ($parts[0] !== $current_username && $parts[1] !== $current_username) { continue; }
        $autre = ($parts[0] === $current_username) ? $parts[1] : $parts[0];
        $avatar = $avatars[$autre] ?? AVATAR_DEFAUT;

        echo "<form action='message.php' method='post' style='margin-bottom:10px;'>";
        echo "<div class='liste_ami'>";
        echo "<input class='affichage_nom' type='hidden' name='avec' value='$autre'>";
        echo "<button class='boutton_ami' type='submit'>";
        echo "<img src='" . ASSETS . "/img/avatar/$avatar.jpg' alt='Avatar' style='width:40px; height:40px; border-radius:50%; margin-right:10px;'>";
        echo "<span class='affichage_nom'>$autre</span>";
        echo "</button></div></form>";
        $trouve = true;
    }
    if (!$trouve) { echo "<div>Aucune discussion trouvée</div>"; }
} else {
    echo "<div>Dossier de messagerie introuvable</div>";
}
?>
</div>
<a class="return2" href="accueil.php">Retour</a>
<?php fin_page(); ?>
