<?php
require_once __DIR__ . '/../src/header.php';

$current_username = utilisateur_courant();
if ($current_username === null) {
    header('Location: connexion.php');
    exit;
}

debut_page('Discussions', 'discussion.css');
barre_navigation('discussion');
?>
<main class="page discussions">
  <h1>Vos discussions</h1>
  <section class="carte">
<?php
if (is_dir(DOSSIER_MESSAGES)) {
    $avatars = charger_avatars();
    $trouve = false;
    echo "<ul class='liste-discussions'>";
    foreach (scandir(DOSSIER_MESSAGES) as $fichier) {
        if ($fichier === '.' || $fichier === '..') { continue; }
        $nom = str_replace('.txt', '', $fichier);
        $parts = explode('_', $nom);
        if (count($parts) < 2) { continue; }
        if ($parts[0] !== $current_username && $parts[1] !== $current_username) { continue; }
        $autre = ($parts[0] === $current_username) ? $parts[1] : $parts[0];
        $avatar = $avatars[$autre] ?? AVATAR_DEFAUT;

        echo "<li><form action='message.php' method='post'>";
        echo "<input type='hidden' name='avec' value='$autre'>";
        echo "<button class='conversation' type='submit'>";
        echo "<img class='avatar-rond' src='" . url_avatar($avatar) . "' alt=''>";
        echo "<span>$autre</span><i class='bx bx-chevron-right'></i>";
        echo "</button></form></li>";
        $trouve = true;
    }
    echo "</ul>";
    if (!$trouve) { echo "<p class='vide'>Aucune discussion trouvée</p>"; }
} else {
    echo "<p class='vide'>Dossier de messagerie introuvable</p>";
}
?>
  </section>
  <a class="btn btn-secondaire" href="accueil.php"><i class='bx bx-arrow-back'></i>Retour</a>
</main>
<?php fin_page(); ?>
