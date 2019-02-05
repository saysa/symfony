<?php

namespace Blog\controllers;

use \Blog\models\CommentManager;
use \Blog\models\UserManager;
use \Blog\models\UserRightManager;

require_once('controllers/BaseController.php');


//COMMENTS
// AJOUTER UN COMMENTAIRE
class CommentsController extends BaseController
{
    public function add($postId)
    {
        if (isset($postId) && $postId > 0) {
            if (!empty($_SESSION['current_user']) && !empty($_POST['comment'])) {

                //$postId = $_GET['id'];
                $author = $_SESSION['current_user']['pseudo'];
                $userId = $_SESSION['current_user']['id'];
                $comment = htmlspecialchars_decode($_POST['comment'], ENT_QUOTES);

                $commentManager = new CommentManager();
                $affectedLines = $commentManager->postComment($postId, $author, $userId, $comment);

                if ($affectedLines === false) {
                    throw new \Exception('Impossible d\'ajouter le commentaire !');
                } else {
                    header('Location: /Blog/posts/' . $postId);
                }
            } else {
                \Blog\flash_error('Tous les champs ne sont pas remplis !');
                header('Location: /Blog/posts/' . $postId);
            }
        } else {
            \Blog\flash_error('Aucun identifiant de billet envoyé');
        }
    }

    // MODIFIER UN COMMENTAIRE
    public function update()
    {
        $userRightsManager = new UserRightManager();
        if (!$userRightsManager->can('edit comment')) {
            \Blog\flash_error('Vous n\'avez pas les droits');
            header('Location: /Blog');
            return;
        }
        if (isset($_GET['id']) && $_GET['id'] > 0) {
            if (!empty($_POST['comment'])) {
                $id = $_GET['id'];
                $comment = htmlspecialchars_decode($_POST['comment'], ENT_QUOTES);
                $commentManager = new CommentManager();
                $newComment = $commentManager->updateComment($id, $comment);
                if ($newComment == false) {
                    throw new \Exception("Impossible d\'editer le commentaire !");
                } else {
                    $this->token = $token;
                    header('Location: index.php?action=editComment&id=' . $id);
                }
            } else {
                \Blog\flash_error('Tous les champs ne sont pas remplis');
            }
        } else {
            \Blog\flash_error('Aucun identifiant de billet envoyé');
        }
    }

    public function validate($id, $postId)
    {
        $commentManager = new CommentManager();
        $postManager = new PostManager();
        $userRightsManager = new UserRightManager();

        if ($userRightsManager->can('validate')) {
            if (isset($id) && $id > 0) {
                $post = $postManager->getPost($postId);


                $comment = $commentManager->getComment($id);
                $newStatus = $commentManager->updateCommentStatus($id);
            } else {
                \Blog\flash_error("Aucun commentaire à valider");
            }
        } else {
            \Blog\flash_error("Vous n'avez pas les droits");
        }
        header('Location: /Blog/posts/' . $postId);
    }

    // RECUPERER INFOS COMMENTAIRE ET POST
    public function edit()
    {
        if (isset($_GET['id']) && $_GET['id'] > 0) {
            $id = $_GET['id'];
            $postManager = new PostManager();
            $commentManager = new CommentManager();
            $comment = $commentManager->getComment($id);
            if (!empty($_SESSION['current_user']) && $comment['user_id'] === $_SESSION['current_user']['id']) {
                $token = $this->token;
                require('view/backend/commentView.php');
            } else {
                \Blog\flash_error('Nope');
            }
        }
    }

    public function delete($id, $postId)
    {
        if (isset($id) && $id > 0) {

            //$id = $_GET['id'];
            $postManager = new CommentManager();
            $userRightsManager = new UserRightManager();

            if ($userRightsManager->can('delete comment')) {
                $deleteComment = $postManager->deleteComment($id);

                if ($deleteComment === false) {
                    \Blog\flash_error('Impossible de supprimer le post');
                }
            } else {
                \Blog\flash_error("Vous n'avez pas les droits");
            }
        }
        header('Location: /Blog/posts/'. $postId);
    }
}
