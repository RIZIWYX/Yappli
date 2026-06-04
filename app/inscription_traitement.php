<?php
require_once __DIR__ . '/../src/config.php';

$user          = $_POST['username'] ?? '';
$password      = $_POST['password'] ?? '';
$password_conf = $_POST['password_conf'] ?? '';
$mail          = $_POST['mail'] ?? '';
$prenom        = $_POST['prenom'] ?? '';
$nom           = $_POST['nom'] ?? '';

$erreurs = [];
if (strlen($user) < 2)                          { $erreurs[] = "Veuillez saisir un nom d'utilisateur plus long."; }
if (strlen($password) < 6)                      { $erreurs[] = "Le mot de passe doit contenir au moins 6 caractères."; }
if (!filter_var($mail, FILTER_VALIDATE_EMAIL))  { $erreurs[] = "Adresse mail invalide."; }
if (strlen($prenom) < 2)                        { $erreurs[] = "Le prénom doit contenir au moins 2 caractères."; }
if (strlen($nom) < 2)                           { $erreurs[] = "Le nom doit contenir au moins 2 caractères."; }
if ($password_conf !== $password)               { $erreurs[] = "Les mots de passe doivent être identiques."; }

// Unicité du pseudo / mail (index mail corrigé : [3])
if (!$erreurs && file_exists(FICHIER_COMPTES)) {
    foreach (file(FICHIER_COMPTES, FILE_IGNORE_NEW_LINES) as $ligne) {
        $c = explode(';', trim($ligne));
        $pseudoPris = (($c[0] ?? '') === $user);
        $mailPris   = (($c[3] ?? '') === $mail);
        if ($pseudoPris && $mailPris) { $erreurs[] = "L'utilisateur est déjà inscrit."; break; }
        if ($mailPris)                { $erreurs[] = "Cette adresse mail est déjà utilisée."; break; }
        if ($pseudoPris)              { $erreurs[] = "Nom d'utilisateur déjà utilisé."; break; }
    }
}

if (!$erreurs) {
    $nouvelleLigne = "$user;$password;$password_conf;$mail;$prenom;$nom\n";
    file_put_contents(FICHIER_COMPTES, $nouvelleLigne, FILE_APPEND);
    file_put_contents(FICHIER_SESSION, $nouvelleLigne);
    header('Location: personnalisation.php');
    exit;
}

// Sinon : afficher les erreurs
require_once __DIR__ . '/../src/header.php';
debut_page('Inscription', 'php.css');
echo '<div>';
foreach ($erreurs as $e) { echo $e . '<br>'; }
echo "<a href='inscription.html'>Annuler</a>";
echo '</div>';
fin_page();
