<?php
// Page de vérification : confirme que l'appli joint bien la base Neon.
// N'affiche aucune donnée sensible. À supprimer une fois le PHP migré sur PDO.
require_once __DIR__ . '/../src/db.php';
header('Content-Type: text/plain; charset=utf-8');
try {
    $nb = db()->query('SELECT count(*) FROM utilisateurs')->fetchColumn();
    echo "OK - base connectée ($nb utilisateurs)";
} catch (Throwable $e) {
    http_response_code(500);
    error_log($e->getMessage());
    echo 'ERREUR - connexion à la base impossible';
}
