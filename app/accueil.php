<?php
require_once __DIR__ . '/../src/header.php';

$current_username = utilisateur_courant();
if ($current_username === null) {
    header('Location: connexion.html');
    exit;
}

$post_commente = (isset($_POST['post_id']) && $_POST['post_id'] !== '') ? (int) $_POST['post_id'] : null;

debut_page('Yapply', 'acceuil.css');
?>

<!-- En-tête avec logo et nom -->
<div class="en-tête">
  <img class="logo" src="<?php echo ASSETS; ?>/img/logo.png" alt="logo" width="70" height="70" />
  <h1 class="nom">Yapply</h1>
</div>

<!-- Recherche -->
<div class="part1">
  <div class="fond-recherche">
    <form action="profil.php" method="post">
      <input type="text" name="username" class="search-bar" placeholder="Rechercher des utilisateurs...">
      <button class="research-btn action-buttons" type="submit"><i class='bx bx-search-alt-2'></i></button>
    </form>
  </div>

  <!-- Liste des amis -->
  <div class="liste_ami">
    <?php
    $fichierAmis = DOSSIER_AMIS . '/' . $current_username . '.txt';
    $nb_amis = 0;
    if (file_exists($fichierAmis)) {
        $avatars = [];
        if (file_exists(FICHIER_PERSO)) {
            foreach (file(FICHIER_PERSO, FILE_IGNORE_NEW_LINES) as $l) {
                $info = explode(';', trim($l));
                if (count($info) >= 2) { $avatars[$info[0]] = $info[1]; }
            }
        }
        foreach (file($fichierAmis, FILE_IGNORE_NEW_LINES) as $ligne) {
            $ami = trim($ligne);
            if ($ami === '') { continue; }
            $avatar = $avatars[$ami] ?? AVATAR_DEFAUT;
            echo "<form action='message.php' method='post' style='margin-bottom:-50px;'>";
            echo "<input type='hidden' name='avec' value='$ami'>";
            echo "<button class='boutton_ami' type='submit'>";
            echo "<img src='" . ASSETS . "/img/avatar/$avatar.jpg' alt='Avatar' style='height:40px; border-radius:50%; position:absolute; left:10px;'>";
            echo "<span class='affichage_nom' style='position:absolute; left:60px'>$ami</span>";
            echo "</button></form>";
            $nb_amis++;
        }
    }
    if ($nb_amis === 0) { echo "<div>Aucun ami trouvé</div>"; }
    ?>
  </div>
</div>

<!-- Publier un post -->
<div class="post-container">
  <form action="post.php" method="post" enctype="multipart/form-data">
    <textarea name="post" placeholder="Écrivez quelque chose..."></textarea>
    <div class="custom-file">
      <label for="photo" class="file-label"><i class='bx bx-paperclip'></i></label>
      <input type="file" id="photo" name="photo" class="input-file-hidden">
      <button type="submit" class="publish-btn">Publier</button>
    </div>
  </form>
</div>

<!-- Fil des publications -->
<div class="publica">
  <?php
  if (file_exists(FICHIER_POSTS)) {
      $avatars = [];
      if (file_exists(FICHIER_PERSO)) {
          foreach (file(FICHIER_PERSO, FILE_IGNORE_NEW_LINES) as $l) {
              $parts = explode(';', trim($l));
              if (count($parts) >= 2 && $parts[0] !== '' && $parts[1] !== '') { $avatars[$parts[0]] = $parts[1]; }
          }
      }

      foreach (file(FICHIER_POSTS, FILE_IGNORE_NEW_LINES) as $index => $ligne) {
          $data = explode(';', trim($ligne));
          if (count($data) < 2) { continue; }
          $pseudo  = $data[0];
          $message = explode('|', trim($data[1]));
          $avatarNumero = $avatars[$pseudo] ?? AVATAR_DEFAUT;

          echo "<div class='publication-container'>";
          echo "  <div class='publication'>";
          echo "    <div>";
          echo "      <img src=\"" . ASSETS . "/img/avatar/$avatarNumero.jpg\" alt=\"Avatar\" style='width:40px; height:40px; border-radius:50%;'>";
          echo "      <strong>$pseudo</strong><br>";

          $estImage = false;
          foreach (['jpg', 'jpeg', 'png', 'gif'] as $ext) {
              if (preg_match("/\\.$ext$/i", $message[0])) {
                  if (file_exists(DOSSIER_IMAGES . '/' . $message[0])) {
                      echo "<img src=\"" . URL_IMAGES . "/" . $message[0] . "\" alt=\"Image postée\" style='max-width:300px; height:auto; border-radius:10px; margin-top:10px;'>";
                      $estImage = true;
                  }
                  break;
              }
          }
          if (!$estImage) { echo htmlspecialchars($message[0]); }

          echo "    </div><br>";

          // Bouton commentaire : transmet l'index du post (post_id)
          echo "    <form method='post'>";
          echo "      <input type='hidden' name='post_id' value='$index'>";
          echo "      <div class='com-like-block'>";
          echo "        <button class='commentaire action-buttons' name='commentaire' type='submit'><i class='bx bxs-comment-detail'></i></button>";
          echo "      </div>";
          echo "    </form>";

          // Bouton like : transmet aussi l'index du post
          echo "    <form action='like.php' method='post'>";
          echo "      <input type='hidden' name='post_id' value='$index'>";
          echo "      <button class='like action-buttons' name='like' type='submit'><i class='bx bx-heart'></i>" . ($message[1] ?? '0') . "</button>";
          echo "    </form>";

          echo "  </div>";
          echo " </div>";
      }
  } else {
      echo "<div>Aucune publication trouvée</div>";
  }
  ?>
</div>

<!-- Boîte de commentaire (du post sélectionné) -->
<?php if (isset($_POST['commentaire']) && !isset($_POST['fermer']) && $post_commente !== null): ?>
  <div class="boite_com">
    <div class="boite_commentaire">
      <div>
        <form method="post"><button type="submit" name="fermer" class="fermer-btn">X</button></form>
      </div>
      <div>
        <form action="commentaire.php" method="post">
          <input type="hidden" name="post_id" value="<?php echo $post_commente; ?>">
          <input type="text" name="commentaire" class="commentaire-bar" placeholder="ecrivez un commentaire...">
          <button class='comment-btn action-buttons' type='submit'><i class='bx bx-send logo_commentaire'></i></button>
        </form>
      </div>
    </div>

    <?php
    // Affiche uniquement les commentaires du post sélectionné
    if (file_exists(FICHIER_POSTS)) {
        $lignes = file(FICHIER_POSTS, FILE_IGNORE_NEW_LINES);
        if (isset($lignes[$post_commente])) {
            $champs = explode('|', trim($lignes[$post_commente]));
            if (count($champs) >= 3) {
                for ($i = 2; $i < count($champs); $i++) {
                    echo "<div class='commentaire-container'><div class='publication'><div>";
                    echo $champs[$i];
                    echo "</div><br></div></div>";
                }
            } else {
                echo "<p>Aucun commentaire pour le moment.</p>";
            }
        }
    }
    ?>
  </div>
<?php endif; ?>

<!-- Profil + boutons -->
<div>
  <?php
  $avatarNumero = AVATAR_DEFAUT;
  if (file_exists(FICHIER_SESSION_PERSO)) {
      $p = explode(';', trim((string) @file_get_contents(FICHIER_SESSION_PERSO)));
      if (isset($p[1]) && $p[1] !== '') { $avatarNumero = $p[1]; }
  }
  ?>

  <form action="profil_utilisateur.php" method="post">
    <button class="profil action-buttons" type="submit" style="display:flex; align-items:center;">
      <img src="<?php echo ASSETS; ?>/img/avatar/<?php echo $avatarNumero; ?>.jpg" alt="Avatar" style="width:40px; height:40px; border-radius:50%; margin-right:10px;">
      <?php echo $current_username; ?>
    </button>
  </form>

  <div>
    <form action="index.html" method="post">
      <button class="deconnexion action-buttons" type="submit"><i class='bx bx-log-out'></i></button>
    </form>
  </div>

  <div>
    <form action="discussion.php" method="post">
      <button class="discussion action-buttons" type="submit"><i class='bx bx-message-rounded-dots'></i></button>
    </form>
  </div>

  <div>
    <form method="post">
      <button class="notification action-buttons" name="afficher" type="submit"><i class='bx bx-bell'></i></button>
    </form>
  </div>

  <?php if (isset($_POST['afficher']) && !isset($_POST['fermer'])): ?>
    <div class="boite">
      <form method="post" style="display:inline;"><button type="submit" name="fermer" class="fermer-btn">X</button></form>
      <?php
      $fichierNotif = DOSSIER_NOTIFS . '/' . $current_username . '.txt';
      if (!file_exists($fichierNotif)) { @file_put_contents($fichierNotif, ''); }
      echo "<h3>Notifications :</h3>";
      $vide = true;
      foreach (file($fichierNotif, FILE_IGNORE_NEW_LINES) as $l) {
          $l = trim($l);
          if ($l !== '') { echo "<p>$l</p>"; $vide = false; }
      }
      if ($vide) { echo "<p>Aucune notification pour le moment.</p>"; }
      ?>
    </div>
  <?php endif; ?>
</div>

<div class="footer-legal">
  <pre>

  Projet Outils de développement Web
  Réalisé par :
  Benadjaoud Accyl & Rayane Graine
  2025/05/18

  </pre>
</div>

<?php fin_page(); ?>
