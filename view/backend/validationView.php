<?php $title = 'Validation'; ?>

<?php ob_start(); ?>

<aside>
    <div class="container">
        <div class="row align-items-center">
            <div class="col">
                <h1>Page de validation</h1>
            </div>
        </div>
        <div class="row align-items-center">
            <div class="col">
                <?php
                $error = get_flash('error');
                if (!empty($error)) {
                    ?>
                    <div class="alert alert-danger" role="alert"><?= $error ?></div>
                <?php } ?>
                <?php
                $success = get_flash('success');
                if (!empty($success)) {
                    ?>
                    <div class="alert alert-success" role="alert"><?= $success ?></div>
                <?php } ?>
                <?php
                $warning = get_flash('warning');
                if (!empty($warning)) {
                    ?>
                    <div class="alert alert-warning" role="alert"><?= $warning ?></div>
                <?php } ?>
            </div>
        </div>
    </div>
</aside>

<br/>
<section>
    <div class="container">
        <div class="row">
            <div class="col col-md-4 col-sm-12">
                <h2>Derniers billets non validés :</h2>
            </div>

            <div class="col col-md-6 col-sm-12">
                <?php foreach($unvalidated_posts as $unvalidated_post):
                    if($unvalidated_post['status'] == NULL) { ?>
                        <h3>
                            <?= htmlspecialchars($unvalidated_post['title']) ?>
                        </h3>
                        <?= nl2br(htmlspecialchars($unvalidated_post['content'])) ?>
                        <br />
                        <strong>Auteur :  <?= nl2br(htmlspecialchars($unvalidated_post['author'])) ?></strong>
                        <br/>
                        <p><em>Publié le <?php
                        $date = new DateTime($unvalidated_post['creation_date']);
                        echo $date->format('d/m/Y à H:i');
                        ?>
                        <?php if(isset($unvalidated_post['edition_date'])) { ?>
                            modifié le
                            <?php
                            $date_edition = new DateTime($unvalidated_post['edition_date']);
                            echo $date_edition->format('d/m/Y à H:i');
                        }  ?>
                    </em></p>
                    <p>
                        <a role="button" class="btn btn-outline-success" href="index.php?action=validatePost&amp;id=<?= $unvalidated_post['id']; ?>">Valider</a>
                        <a role="button" class="btn btn-outline-danger" href="index.php?action=deletePost&amp;id=<?= $unvalidated_post['id']; ?>">Supprimer</a>
                    </p>

                <?php } ?>
            <?php endforeach;?>
        </div>
    </div>
</div>
</section>

<section>
    <div class="container">
        <h2>Commentaires non validés :</h2>

        <?php foreach($unvalidated_comments as $unvalidated_comment):
            if($unvalidated_comment['status'] == NULL) { var_dump($unvalidated_comment);?>
                <div class="col col-sm-8">
                    <p><strong><?= htmlspecialchars($unvalidated_comment['author']) ?></strong> le <?php $dateComment = new DateTime($unvalidated_comment['comment_date']); echo $dateComment->format('d/m/Y');?> <br/>
                        <?= nl2br(htmlspecialchars($unvalidated_comment['comment'])) ?></p>
                    </div>
                    <p>
                        <a role="button" class="btn btn-outline-success" href="index.php?action=validateComment&amp;id=<?= $unvalidated_comment['id']; ?>">Valider</a>
                        <a role="button" class="btn btn-outline-danger" href="index.php?action=deletePost&amp;id=<?= $unvalidated_comment['id']; ?>">Supprimer</a>
                    </p>

                <?php } ?>
            <?php endforeach;?>

        </div>
    </section>

    <section>
        <div class="container">
            <h2>Nouveaux comptes créés :</h2>
            <?php foreach($new_users as $new_user):
                if(!empty($new_user)) { ?>

                    <div class="table-responsive-md">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Identifiant</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Date inscription</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?= $new_user['pseudo']; ?></td>
                                    <td><?= $new_user['email']; ?></td>
                                    <td><?= $new_user['signup_date']; ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-outline-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Valider
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="index.php?action=validateUser&amp;id=<?= $new_user['id'];?>">Membre</a>
                                                <a class="dropdown-item" href="index.php?action=validateUser&amp;id=<?= $new_user['id']; ?>">Collaborateur</a>
                                                <a class="dropdown-item" href="index.php?action=validateUser&amp;id=<?= $new_user['id']; ?>">Administrateur</a>
                                            </div>
                                        </div>
                                        <a role="button" class="btn btn-outline-danger" href="index.php?action=deleteUser&amp;id=<?= $new_user['id']; ?>">Supprimer</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>


                <?php } ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php $content = ob_get_clean(); ?>

    <?php require('view/template.php'); ?>
