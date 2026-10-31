<?php
// Page de vérification : confirme que l'appli joint bien la base Neon.
// Affiche un diagnostic sans secret. À supprimer une fois le PHP migré sur PDO.
require_once __DIR__ . '/../src/db.php';
header('Content-Type: text/plain; charset=utf-8');

$pilotes = class_exists('PDO') ? PDO::getAvailableDrivers() : [];
echo 'PHP ' . PHP_VERSION . ' | pilotes PDO : ' . (implode(', ', $pilotes) ?: 'aucun') . "\n";
echo 'DATABASE_URL : ' . (getenv('DATABASE_URL') ? 'présente' : 'ABSENTE') . "\n";

if (!in_array('pgsql', $pilotes, true)) {
    http_response_code(500);
    exit("ERREUR - extension pdo_pgsql absente du runtime\n");
}
try {
    $nb = db()->query('SELECT count(*) FROM utilisateurs')->fetchColumn();
    echo "OK - base connectée ($nb utilisateurs)\n";
} catch (Throwable $e) {
    http_response_code(500);
    // On masque tout ce qui ressemble à un mot de passe avant d'afficher
    $msg = preg_replace('/(password|endpoint=)[^\s;]*/i', '$1***', $e->getMessage());
    echo 'ERREUR - ' . get_class($e) . ' : ' . substr($msg, 0, 300) . "\n";
}
