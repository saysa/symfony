<?php $title = 'Modifier un commentaire' ?>

<?php ob_start(); ?>
<h1>Mon blog !</h1>
<p><a href="index.php?action=showPost&amp;postId=<?= $postId['id'] ?>">Retour à la liste des billets</a></p>



<h2>Modifier un commentaire</h2>

<form action="index.php?action=updateComment&amp;id=<?= $comment['id'] ?>" method="post">
    <div>
        <p>Auteur : <?= htmlspecialchars($comment['author'])?></p>
        <label for="comment">Commentaire</label><br />
        <textarea id="comment" name="comment"><?= htmlspecialchars($comment['comment']) ?></textarea>
    </div>
    <div>
        <input type="submit" value="Modifier"/>
      </div>

</form>


<?php $content = ob_get_clean(); ?>

<?php require('template.php'); ?>