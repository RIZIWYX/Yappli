<?php
require_once __DIR__ . '/../src/config.php';

$userinfo = $_POST['userinfo'] ?? '';
$password = $_POST['password'] ?? '';

$trouve = false;
$ligneCompte = '';
$lignePerso  = '';

if (file_exists(FICHIER_COMPTES)) {
    foreach (file(FICHIER_COMPTES, FILE_IGNORE_NEW_LINES) as $ligne) {
        $c = explode(';', trim($ligne));
        if (((($c[0] ?? '') === $userinfo) || (($c[3] ?? '') === $userinfo)) && (($c[1] ?? '') === $password)) {
            $trouve = true;
            $ligneCompte = implode(';', $c);

            if (file_exists(FICHIER_PERSO)) {
                foreach (file(FICHIER_PERSO, FILE_IGNORE_NEW_LINES) as $lp) {
                    $cp = explode(';', trim($lp));
                    if (($cp[0] ?? '') === ($c[0] ?? '')) { $lignePerso = implode(';', $cp); break; }
                }
            }
            break;
        }
    }
}

if ($trouve) {
    file_put_contents(FICHIER_SESSION, $ligneCompte . "\n");
    if ($lignePerso !== '') { file_put_contents(FICHIER_SESSION_PERSO, $lignePerso . "\n"); }
    header('Location: accueil.php');
    exit;
}

require_once __DIR__ . '/../src/header.php';
debut_page('Connexion', 'php.css');
echo "<div><h2>Information incorrecte</h2><br><a href='connexion.html'>Retour</a></div>";
fin_page();
