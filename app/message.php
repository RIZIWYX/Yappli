<?php
require_once __DIR__ . '/../src/header.php';

$current_username = utilisateur_courant();
if ($current_username === null) {
    header('Location: connexion.html');
    exit;
}

$autre = $_POST['avec'] ?? '';
if ($autre === '') {
    header('Location: discussion.php');
    exit;
}

// Un fichier de discussion par paire (quel que soit l'ordre)
$f1 = DOSSIER_MESSAGES . "/{$current_username}_{$autre}.txt";
$f2 = DOSSIER_MESSAGES . "/{$autre}_{$current_username}.txt";
$fichier = file_exists($f1) ? $f1 : (file_exists($f2) ? $f2 : $f1);
if (!file_exists($fichier)) { @file_put_contents($fichier, ''); }

// Envoi d'un message
if (isset($_POST['message']) && $_POST['message'] !== '') {
    file_put_contents($fichier, "$current_username -> $autre: " . $_POST['message'] . "\n", FILE_APPEND);
    file_put_contents(DOSSIER_NOTIFS . '/' . $autre . '.txt', "$current_username vous a envoyé un message\n", FILE_APPEND);
}

debut_page('Discussion', 'discussion.css');
?>
<form method="POST">
  <h1 class="votre_discussion">Discussions.</h1>
  <div class="boite">
    <div class="messages">
      <?php
      $messages = file($fichier, FILE_IGNORE_NEW_LINES);
      if (empty($messages)) {
          echo "Aucune discussion trouvée avec $autre.";
      } else {
          foreach ($messages as $msg) {
              $msg = trim($msg);
              if (strpos($msg, ' -> ') !== false) {
                  list($expediteur, $reste) = explode(' -> ', $msg, 2);
                  $parts = explode(':', $reste, 2);
                  if (count($parts) === 2) {
                      $texte  = trim($parts[1]);
                      $classe = ($expediteur === $current_username) ? 'envoye' : 'recu';
                      echo "<div class='message $classe'><strong>$expediteur :</strong> $texte</div>";
                  }
              }
          }
      }
      ?>
    </div>
    <div class="zone-saisie">
      <input type="hidden" name="avec" value="<?php echo $autre; ?>">
      <input class="ecrivez" type="text" name="message" placeholder="Écrire un message" required>
      <button class="envoie" type="submit"><i class='bx bxs-send'></i></button>
    </div>
  </div>
</form>
<a class="return" href="discussion.php">Retour</a>
<?php fin_page(); ?>
