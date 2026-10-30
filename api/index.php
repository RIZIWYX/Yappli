<?php
/**
 * Point d'entrée unique pour Vercel (plan Hobby : 12 fonctions max).
 * Toutes les URL /app/<page>.php arrivent ici et sont redirigées vers le bon
 * fichier de app/. Liste blanche stricte : impossible d'inclure autre chose.
 */
$page = $_GET['__page'] ?? 'index.php';
unset($_GET['__page']);

$dossierApp = realpath(__DIR__ . '/../app');
if (!preg_match('/^[a-z_]+\.php$/', $page)) {
    http_response_code(404);
    exit('Page introuvable');
}
$fichier = realpath($dossierApp . '/' . $page);
if ($fichier === false || dirname($fichier) !== $dossierApp) {
    http_response_code(404);
    exit('Page introuvable');
}

chdir($dossierApp);
$_SERVER['SCRIPT_NAME'] = '/app/' . $page;
$_SERVER['PHP_SELF']    = '/app/' . $page;
require $fichier;
