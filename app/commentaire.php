<?php
require_once __DIR__ . '/../src/config.php';

$username = utilisateur_courant();
if ($username === null) {
    header('Location: connexion.html');
    exit;
}

$commentaire = isset($_POST['commentaire']) ? trim($_POST['commentaire']) : '';
$post_id = (isset($_POST['post_id']) && $_POST['post_id'] !== '') ? (int) $_POST['post_id'] : -1;

if ($commentaire !== '' && $post_id >= 0 && file_exists(FICHIER_POSTS)) {
    $lignes = file(FICHIER_POSTS, FILE_IGNORE_NEW_LINES);
    if (isset($lignes[$post_id])) {
        $lignes[$post_id] .= '|' . $username . ':' . str_replace('|', '', $commentaire);
        file_put_contents(FICHIER_POSTS, implode("\n", $lignes) . "\n");

        if (is_dir(DOSSIER_NOTIFS)) {
            foreach (scandir(DOSSIER_NOTIFS) as $f) {
                if ($f[0] === '.' || $f === $username . '.txt') { continue; }
                file_put_contents(DOSSIER_NOTIFS . '/' . $f, "$username a commenté un post\n", FILE_APPEND);
            }
        }
    }
}

header('Location: accueil.php');
exit;
