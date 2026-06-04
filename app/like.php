<?php
require_once __DIR__ . '/../src/config.php';

$username = utilisateur_courant();
if ($username === null) {
    header('Location: connexion.html');
    exit;
}

$post_id = (isset($_POST['post_id']) && $_POST['post_id'] !== '') ? (int) $_POST['post_id'] : -1;

if ($post_id >= 0 && file_exists(FICHIER_POSTS)) {
    $lignes = file(FICHIER_POSTS, FILE_IGNORE_NEW_LINES);
    if (isset($lignes[$post_id])) {
        $champs = explode('|', $lignes[$post_id]);
        if (isset($champs[1])) {
            $champs[1] = (int) $champs[1] + 1; // incrémente le compteur de likes
            $lignes[$post_id] = implode('|', $champs);
            file_put_contents(FICHIER_POSTS, implode("\n", $lignes) . "\n");
        }
        if (is_dir(DOSSIER_NOTIFS)) {
            foreach (scandir(DOSSIER_NOTIFS) as $f) {
                if ($f[0] === '.' || $f === $username . '.txt') { continue; }
                file_put_contents(DOSSIER_NOTIFS . '/' . $f, "$username a aimé un post\n", FILE_APPEND);
            }
        }
    }
}

header('Location: accueil.php');
exit;
