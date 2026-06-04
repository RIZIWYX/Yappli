<?php
require_once __DIR__ . '/../src/config.php';

$username = utilisateur_courant();
if ($username === null) {
    header('Location: connexion.html');
    exit;
}

$texte = isset($_POST['post']) ? trim($_POST['post']) : '';
$photo = null;

// Upload SANS contrôle de type/taille (limite connue -> à corriger dans la version sécurisée)
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
    $photo = basename($_FILES['photo']['name']);
    move_uploaded_file($_FILES['photo']['tmp_name'], DOSSIER_IMAGES . '/' . $photo);
}

if ($texte !== '' || $photo !== null) {
    if ($texte !== '' && $photo !== null) { $contenu = $texte . '|' . $photo; }
    elseif ($photo !== null)              { $contenu = $photo; }
    else                                  { $contenu = $texte; }

    file_put_contents(FICHIER_POSTS, "$username;$contenu|0\n", FILE_APPEND);

    // Notifier les autres utilisateurs
    if (is_dir(DOSSIER_NOTIFS)) {
        foreach (scandir(DOSSIER_NOTIFS) as $f) {
            if ($f[0] === '.' || $f === $username . '.txt') { continue; }
            file_put_contents(DOSSIER_NOTIFS . '/' . $f, "$username a publié un post\n", FILE_APPEND);
        }
    }
    header('Location: accueil.php');
    exit;
}

require_once __DIR__ . '/../src/header.php';
debut_page('Publication', 'php.css');
echo "<div>Erreur : message vide.<br><a href='accueil.php'>Retour</a></div>";
fin_page();
