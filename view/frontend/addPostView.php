<?php $title = 'Ajouter post'; ?>

<?php ob_start(); ?>


<!DOCTYPE html>
<html>
<head>
  <title>Administration</title>
  <meta charset="utf-8" />

</style>
</head>

<body>
  <p><a href="index.php">Accéder à l'accueil du site</a></p>

  <div class="news">
    <h2>Ajouter Post</h2>

    <form action="index.php?action=createPost" method="post">

        <div>
            <label for="title">Auteur</label><br />
            <input type="text" id="title" name="title" />
        </div>
        <div>
            <label for="author">Titre</label><br />
            <input type="text" id="author" name="author" />
        </div>
        <div>
            <label for="content">Contenu</label><br />
            <textarea id="content" name="content" rows="8" cols="80"></textarea>
        </div>
        <div>
            <input type="submit" />
    </form>
  </div>
</body>
</html>
<?php $content = ob_get_clean(); ?>

<?php require('template.php'); ?>