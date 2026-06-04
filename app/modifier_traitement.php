<?php
require_once __DIR__ . '/../src/config.php';

$current = donnees_session();
if ($current === null) {
    header('Location: connexion.html');
    exit;
}

/** Met à jour la ligne de l'utilisateur dans un fichier « pseudo;... ». */
function maj_fichier(string $fichier, string $pseudo, string $nouvelleLigne): void {
    if (!file_exists($fichier)) { return; }
    $sortie = '';
    foreach (file($fichier, FILE_IGNORE_NEW_LINES) as $l) {
        $champs = explode(';', $l);
        $sortie .= ((($champs[0] ?? '') === $pseudo) ? $nouvelleLigne : $l) . "\n";
    }
    file_put_contents($fichier, $sortie);
}

$current_username = $current[0];

// Champ vide => on garde l'ancienne valeur
$user          = ($_POST['username'] ?? '')      ?: $current_username;
$password      = ($_POST['password'] ?? '')      ?: ($current[1] ?? '');
$password_conf = ($_POST['password_conf'] ?? '') ?: ($current[2] ?? '');
$mail          = ($_POST['mail'] ?? '')          ?: ($current[3] ?? '');
$prenom        = ($_POST['prenom'] ?? '')        ?: ($current[4] ?? '');
$nom           = ($_POST['nom'] ?? '')           ?: ($current[5] ?? '');

$erreurs = [];

// Unicité (en ignorant sa propre ligne), index mail corrigé : [3]
if (file_exists(FICHIER_COMPTES)) {
    foreach (file(FICHIER_COMPTES, FILE_IGNORE_NEW_LINES) as $l) {
        $c = explode(';', $l);
        if (($c[0] ?? '') === $current_username) { continue; }
        if ($user !== $current_username && ($c[0] ?? '') === $user) { $erreurs[] = "Nom d'utilisateur déjà utilisé."; break; }
        if ($mail !== ($current[3] ?? '') && ($c[3] ?? '') === $mail) { $erreurs[] = "Cette adresse mail est déjà utilisée."; break; }
    }
}
// Validité
if (strlen($user) < 2)                          { $erreurs[] = "Nom d'utilisateur trop court."; }
if (strlen($password) < 6)                      { $erreurs[] = "Le mot de passe doit contenir au moins 6 caractères."; }
if ($password_conf !== $password)               { $erreurs[] = "Les mots de passe doivent être identiques."; }
if (!filter_var($mail, FILTER_VALIDATE_EMAIL))  { $erreurs[] = "Adresse mail invalide."; }
if (strlen($prenom) < 2)                        { $erreurs[] = "Le prénom doit contenir au moins 2 caractères."; }
if (strlen($nom) < 2)                           { $erreurs[] = "Le nom doit contenir au moins 2 caractères."; }

if (!$erreurs) {
    $ligneCompte = "$user;$password;$password_conf;$mail;$prenom;$nom";
    maj_fichier(FICHIER_COMPTES, $current_username, $ligneCompte);
    maj_fichier(FICHIER_SESSION, $current_username, $ligneCompte);

    // Personnalisation : on conserve avatar + bio
    $perso  = file_exists(FICHIER_SESSION_PERSO)
        ? explode(';', trim((string) file_get_contents(FICHIER_SESSION_PERSO)))
        : [];
    $avatar = $perso[1] ?? AVATAR_DEFAUT;
    $bio    = $perso[2] ?? '';
    $lignePerso = "$user;$avatar;$bio";
    maj_fichier(FICHIER_PERSO, $current_username, $lignePerso);
    maj_fichier(FICHIER_SESSION_PERSO, $current_username, $lignePerso);

    // Renommer le pseudo dans les posts
    if (file_exists(FICHIER_POSTS)) {
        $lignes = [];
        foreach (file(FICHIER_POSTS, FILE_IGNORE_NEW_LINES) as $l) {
            $c = explode(';', $l);
            if (($c[0] ?? '') === $current_username) { $c[0] = $user; }
            $lignes[] = implode(';', $c);
        }
        file_put_contents(FICHIER_POSTS, implode("\n", $lignes) . "\n");
    }

    // Renommer le fichier d'amis
    $ancienAmis  = DOSSIER_AMIS . '/' . $current_username . '.txt';
    $nouveauAmis = DOSSIER_AMIS . '/' . $user . '.txt';
    if (file_exists($ancienAmis)) { @rename($ancienAmis, $nouveauAmis); }

    header('Location: profil_utilisateur.php');
    exit;
}

require_once __DIR__ . '/../src/header.php';
debut_page('Modifier', 'php.css');
echo '<div>';
foreach ($erreurs as $e) { echo $e . '<br>'; }
echo "<a href='modifier.html'>Retour</a>";
echo '</div>';
fin_page();
