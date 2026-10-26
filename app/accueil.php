<?php
require_once __DIR__ . '/../src/header.php';

$current_username = utilisateur_courant();
if ($current_username === null) {
    header('Location: connexion.php');
    exit;
}

$post_commente = (isset($_POST['post_id']) && $_POST['post_id'] !== '') ? (int) $_POST['post_id'] : null;
$afficher_notifs = isset($_POST['afficher']) && !isset($_POST['fermer']);
$afficher_coms   = isset($_POST['commentaire']) && !isset($_POST['fermer']) && $post_commente !== null;

$avatars = charger_avatars();

debut_page('Yapply', 'accueil.css');
barre_navigation($afficher_notifs ? 'notifications' : 'accueil');
?>

<main class="page accueil">

  <!-- Colonne gauche : publier un post -->
  <aside class="colonne colonne-gauche">
    <section class="carte composer">
      <h3>Quoi de neuf ?</h3>
      <form action="post.php" method="post" enctype="multipart/form-data">
        <label class="champ">
          <span class="visually-hidden">Votre publication</span>
          <textarea name="post" placeholder="Écrivez quelque chose..."></textarea>
        </label>
        <div class="composer-actions">
          <label for="photo" class="btn btn-icone btn-secondaire" title="Joindre une image"><i class='bx bx-paperclip'></i><span class="visually-hidden">Joindre une image</span></label>
          <input type="file" id="photo" name="photo" class="visually-hidden">
          <button type="submit" class="btn">Publier</button>
        </div>
      </form>
    </section>
    <?php pied_page(); ?>
  </aside>

  <!-- Colonne centrale : fil des publications -->
  <section class="colonne fil" aria-label="Fil d'actualité">
    <?php
    if (file_exists(FICHIER_POSTS)) {
        $nb_posts = 0;
        foreach (file(FICHIER_POSTS, FILE_IGNORE_NEW_LINES) as $index => $ligne) {
            $data = explode(';', trim($ligne));
            if (count($data) < 2) { continue; }
            $pseudo  = $data[0];
            $message = explode('|', trim($data[1]));
            $avatarNumero = $avatars[$pseudo] ?? AVATAR_DEFAUT;
            $nb_posts++;

            echo "<article class='carte publication'>";
            echo "  <header class='publication-auteur'>";
            echo "    <img class='avatar-rond' src=\"" . url_avatar($avatarNumero) . "\" alt=\"\">";
            echo "    <strong>$pseudo</strong>";
            echo "  </header>";
            echo "  <div class='publication-contenu'>";

            $estImage = false;
            foreach (['jpg', 'jpeg', 'png', 'gif'] as $ext) {
                if (preg_match("/\\.$ext$/i", $message[0])) {
                    if (file_exists(DOSSIER_IMAGES . '/' . $message[0])) {
                        echo "<img class='publication-image' src=\"" . URL_IMAGES . "/" . $message[0] . "\" alt=\"Image postée\">";
                        $estImage = true;
                    }
                    break;
                }
            }
            if (!$estImage) { echo "<p>" . htmlspecialchars($message[0]) . "</p>"; }

            echo "  </div>";
            echo "  <footer class='publication-actions'>";

            // Bouton like : transmet l'index du post
            echo "    <form action='like.php' method='post'>";
            echo "      <input type='hidden' name='post_id' value='$index'>";
            echo "      <button class='btn btn-discret' name='like' type='submit' title=\"J'aime\"><i class='bx bx-heart'></i>" . ($message[1] ?? '0') . "</button>";
            echo "    </form>";

            // Bouton commentaire : transmet aussi l'index du post (post_id)
            echo "    <form method='post'>";
            echo "      <input type='hidden' name='post_id' value='$index'>";
            echo "      <button class='btn btn-discret' name='commentaire' type='submit' title='Commentaires'><i class='bx bx-comment-detail'></i>Commenter</button>";
            echo "    </form>";

            echo "  </footer>";
            echo "</article>";
        }
        if ($nb_posts === 0) { echo "<p class='carte vide'>Aucune publication pour le moment.</p>"; }
    } else {
        echo "<p class='carte vide'>Aucune publication trouvée</p>";
    }
    ?>
  </section>

  <!-- Colonne droite : recherche + amis -->
  <aside class="colonne colonne-droite">
    <section class="carte">
      <form class="recherche" action="profil.php" method="post">
        <label class="champ">
          <span class="visually-hidden">Rechercher des utilisateurs</span>
          <input type="text" name="username" placeholder="Rechercher...">
          <i class='bx bx-search-alt-2'></i>
        </label>
        <button class="btn btn-icone" type="submit" title="Rechercher"><i class='bx bx-right-arrow-alt'></i><span class="visually-hidden">Rechercher</span></button>
      </form>
    </section>

    <section class="carte amis">
      <h3>Mes amis</h3>
      <?php
      $fichierAmis = DOSSIER_AMIS . '/' . $current_username . '.txt';
      $nb_amis = 0;
      if (file_exists($fichierAmis)) {
          echo "<ul class='liste-amis'>";
          foreach (file($fichierAmis, FILE_IGNORE_NEW_LINES) as $ligne) {
              $ami = trim($ligne);
              if ($ami === '') { continue; }
              $avatar = $avatars[$ami] ?? AVATAR_DEFAUT;
              echo "<li><form action='message.php' method='post'>";
              echo "<input type='hidden' name='avec' value='$ami'>";
              echo "<button class='ami' type='submit' title='Écrire à $ami'>";
              echo "<img class='avatar-rond' src='" . url_avatar($avatar) . "' alt=''>";
              echo "<span>$ami</span><i class='bx bx-message-rounded'></i>";
              echo "</button></form></li>";
              $nb_amis++;
          }
          echo "</ul>";
      }
      if ($nb_amis === 0) { echo "<p class='vide'>Aucun ami trouvé</p>"; }
      ?>
    </section>
  </aside>
</main>

<!-- Boîte de commentaires (du post sélectionné) -->
<?php if ($afficher_coms): ?>
  <div class="fenetre-fond">
    <section class="carte-pleine fenetre" aria-label="Commentaires">
      <header class="fenetre-entete">
        <h3>Commentaires</h3>
        <form method="post"><button type="submit" name="fermer" class="btn btn-icone btn-discret" title="Fermer"><i class='bx bx-x'></i><span class="visually-hidden">Fermer</span></button></form>
      </header>

      <div class="fenetre-corps">
        <?php
        // Affiche uniquement les commentaires du post sélectionné
        if (file_exists(FICHIER_POSTS)) {
            $lignes = file(FICHIER_POSTS, FILE_IGNORE_NEW_LINES);
            if (isset($lignes[$post_commente])) {
                $champs = explode('|', trim($lignes[$post_commente]));
                if (count($champs) >= 3) {
                    for ($i = 2; $i < count($champs); $i++) {
                        echo "<p class='commentaire'>";
                        echo $champs[$i];
                        echo "</p>";
                    }
                } else {
                    echo "<p class='vide'>Aucun commentaire pour le moment.</p>";
                }
            }
        }
        ?>
      </div>

      <form class="fenetre-saisie" action="commentaire.php" method="post">
        <input type="hidden" name="post_id" value="<?php echo $post_commente; ?>">
        <label class="champ">
          <span class="visually-hidden">Votre commentaire</span>
          <input type="text" name="commentaire" placeholder="Écrivez un commentaire...">
        </label>
        <button class="btn btn-icone" type="submit" title="Envoyer"><i class='bx bx-send'></i><span class="visually-hidden">Envoyer</span></button>
      </form>
    </section>
  </div>
<?php endif; ?>

<!-- Notifications -->
<?php if ($afficher_notifs): ?>
  <div class="fenetre-fond">
    <section class="carte-pleine fenetre" aria-label="Notifications">
      <header class="fenetre-entete">
        <h3>Notifications</h3>
        <form method="post"><button type="submit" name="fermer" class="btn btn-icone btn-discret" title="Fermer"><i class='bx bx-x'></i><span class="visually-hidden">Fermer</span></button></form>
      </header>
      <div class="fenetre-corps">
        <?php
        $fichierNotif = DOSSIER_NOTIFS . '/' . $current_username . '.txt';
        if (!file_exists($fichierNotif)) { @file_put_contents($fichierNotif, ''); }
        $vide = true;
        foreach (array_reverse(file($fichierNotif, FILE_IGNORE_NEW_LINES)) as $l) {
            $l = trim($l);
            if ($l !== '') { echo "<p class='notif'><i class='bx bxs-bell'></i>$l</p>"; $vide = false; }
        }
        if ($vide) { echo "<p class='vide'>Aucune notification pour le moment.</p>"; }
        ?>
      </div>
    </section>
  </div>
<?php endif; ?>

<?php fin_page(); ?>
