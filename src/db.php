<?php
/**
 * Connexion PostgreSQL (Neon) via PDO.
 * Lit DATABASE_URL depuis l'environnement (Vercel) ou le fichier .env (local).
 * Usage : $pdo = db();  puis requêtes préparées uniquement.
 */

function charger_env(string $fichier): void {
    if (!is_readable($fichier)) { return; }
    foreach (file($fichier, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $ligne) {
        $ligne = trim($ligne);
        if ($ligne === '' || $ligne[0] === '#' || !str_contains($ligne, '=')) { continue; }
        [$cle, $valeur] = explode('=', $ligne, 2);
        $cle = trim($cle);
        $valeur = trim($valeur, " \t\"'");
        if (getenv($cle) === false) { putenv("$cle=$valeur"); }
    }
}

function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) { return $pdo; }

    charger_env(dirname(__DIR__) . '/.env');
    $url = getenv('DATABASE_URL');
    if ($url === false || $url === '') {
        throw new RuntimeException('DATABASE_URL non définie.');
    }

    $p = parse_url($url);
    $dbname = ltrim($p['path'] ?? '', '/');
    $dsn = sprintf(
        'pgsql:host=%s;port=%d;dbname=%s;sslmode=require',
        $p['host'], $p['port'] ?? 5432, $dbname
    );
    // Neon : on passe l'identifiant du endpoint dans le mot de passe
    // (méthode officielle Neon pour les libpq anciennes, sans SNI).
    $endpoint = str_replace('-pooler', '', explode('.', $p['host'])[0]);
    $motDePasse = 'endpoint=' . $endpoint . ';' . urldecode($p['pass'] ?? '');

    $pdo = new PDO($dsn, urldecode($p['user'] ?? ''), $motDePasse, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    return $pdo;
}
