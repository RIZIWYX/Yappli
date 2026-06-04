<?php
/**
 * En-tête / pied de page HTML communs.
 * Évite de recopier le <head> (CDN, CSS, favicon) dans chaque page.
 */
require_once __DIR__ . '/config.php';

function debut_page(string $titre, string $css): void {
    echo '<!DOCTYPE html>' . "\n";
    echo '<html lang="fr">' . "\n";
    echo '<head>' . "\n";
    echo '  <meta charset="UTF-8">' . "\n";
    echo '  <title>' . htmlspecialchars($titre) . '</title>' . "\n";
    echo '  <link rel="stylesheet" href="' . ASSETS . '/css/' . $css . '">' . "\n";
    echo "  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>\n";
    echo '  <link rel="icon" type="image/png" href="' . ASSETS . '/img/logo.png">' . "\n";
    echo '</head>' . "\n";
    echo '<body>' . "\n";
}

function fin_page(): void {
    echo "\n</body>\n</html>\n";
}
