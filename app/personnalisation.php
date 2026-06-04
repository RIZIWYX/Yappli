<?php
require_once __DIR__ . '/../src/header.php';

$user = utilisateur_courant();
if ($user === null) {
    header('Location: connexion.html');
    exit;
}

debut_page('Personnalisation', 'personalisation2.css');
echo "<h1 class=\"titre bienvenue\">Bienvenue, " . $user . " !</h1>";
?>
<form id="avatar" action="personnalisation_traitement.php" method="post">
  <div class="avatar-container">
    <h1 class="titre">Choisissez votre avatar</h1>
    <?php for ($i = 1; $i <= 9; $i++): ?>
      <div class="avatar-option">
        <input type="radio" name="avatar" value="avatar<?php echo $i; ?>">
        <img src="<?php echo ASSETS; ?>/img/avatar/avatar<?php echo $i; ?>.jpg" alt="Avatar <?php echo $i; ?>">
      </div>
    <?php endfor; ?>
  </div>
  <div class="bio-container">
    <h1 class="titre">Biographie</h1>
    <textarea name="text" placeholder="Écrivez quelque chose..."></textarea>
    <button class="valide-btn" type="submit">Valider</button>
  </div>
</form>
<?php fin_page(); ?>
