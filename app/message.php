<?php
require_once __DIR__ . '/../src/header.php';

$current_username = utilisateur_courant();
if ($current_username === null) {
    header('Location: connexion.php');
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

$avatars = charger_avatars();

debut_page('Discussion avec ' . $autre, 'discussion.css');
barre_navigation('discussion');
?>
<main class="page conversation-page">
  <section class="carte-pleine chat">
    <header class="chat-entete">
      <a class="btn btn-icone btn-discret" href="discussion.php" title="Retour"><i class='bx bx-arrow-back'></i><span class="visually-hidden">Retour</span></a>
      <img class="avatar-rond" src="<?php echo url_avatar($avatars[$autre] ?? AVATAR_DEFAUT); ?>" alt="">
      <h2><?php echo $autre; ?></h2>
    </header>

    <div class="messages" id="messages">
      <?php
      $messages = file($fichier, FILE_IGNORE_NEW_LINES);
      if (empty($messages)) {
          echo "<p class='vide'>Aucune discussion trouvée avec $autre.</p>";
      } else {
          foreach ($messages as $msg) {
              $msg = trim($msg);
              if (strpos($msg, ' -> ') !== false) {
                  list($expediteur, $reste) = explode(' -> ', $msg, 2);
                  $parts = explode(':', $reste, 2);
                  if (count($parts) === 2) {
                      $texte  = trim($parts[1]);
                      $classe = ($expediteur === $current_username) ? 'envoye' : 'recu';
                      echo "<div class='message $classe'><strong>$expediteur</strong>$texte</div>";
                  }
              }
          }
      }
      ?>
    </div>

    <form class="zone-saisie" method="POST">
      <input type="hidden" name="avec" value="<?php echo $autre; ?>">
      <label class="champ">
        <span class="visually-hidden">Votre message</span>
        <input type="text" name="message" placeholder="Écrire un message" autocomplete="off" required autofocus>
      </label>
      <button class="btn btn-icone" type="submit" title="Envoyer"><i class='bx bxs-send'></i><span class="visually-hidden">Envoyer</span></button>
    </form>
  </section>
</main>
<script>
  // Fait défiler la conversation jusqu'au dernier message
  const zone = document.getElementById('messages');
  if (zone) { zone.scrollTop = zone.scrollHeight; }
</script>
<?php fin_page(); ?>
