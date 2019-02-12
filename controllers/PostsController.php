<?php

namespace Blog\controllers;

use \Blog\models\PostManager;
use \Blog\models\UserRightManager;
use \Blog\models\CommentManager;
use \Blog\models\Input;

require_once('controllers/BaseController.php');

//POST
//AFFICHER LA LISTE DES POSTS

class PostsController extends BaseController
{
    public function show($id)
    {
        if (isset($id) && $id > 0) {

            $postManager = new PostManager();
            $commentManager = new CommentManager();
            $userRightsManager = new UserRightManager();

            $post = $postManager->getPost($id);
            $comments = $commentManager->getComments($id);
            $postStatus = $post['status'];

            if (isset($postStatus) && $postStatus != null) {
                require('view/frontend/postView.php');
            } else {
                header('Location: index.php');
                \Blog\flash_error('Ce post n\'est pas validé !');
            }
        } else {
            \Blog\flash_error('Aucun identifiant de billet envoyé');
        }
    }

    /**
     * display all posts
     */
    public function index()
    {
        $postManager = new PostManager();
        $userRightsManager = new UserRightManager();

        $postManager->getPosts();

        require('view/frontend/listPostsView.php');
    }

    //POST
    // AJOUTER UN POST
    public function create()
    {
        $userRightsManager = new UserRightManager();

        $input = new Input();
        $session = $input->session('current_user');

        if (!$userRightsManager->can('add post')) {
            \Blog\flash_error('Vous n\'avez pas les droits');
            header('Location: index.php');
            return;
        }
        if (!empty($session) && !empty($input->post('title')) && !empty($input->post('content'))) {

            $author = $session['pseudo'];
            $title = htmlspecialchars_decode($input->post('title'), ENT_QUOTES);
            $content = htmlspecialchars_decode($input->post('content'), ENT_QUOTES);

            $postManager = new PostManager();
            $newPostLines = $postManager->postPost($title, $author, $content);

            if ($newPostLines === false) {
                \Blog\falsh_error('Impossible d\'ajouter le post !');
            } else {
                \Blog\flash_warning('Le post doit être validé avant d\'apparaître dans la liste');
                header('Location: /Blog');
            }
        } else {
            \Blog\flash_error('Tous les champs ne sont pas remplis');

            header('Location: /Blog/posts-new');
        }
    }

    public function new()
    {
        $userRightsManager = new UserRightManager();
        if (!$userRightsManager->can('add post')) {
            \Blog\flash_error('Vous n\'avez pas les droits');

            header('Location: /Blog');
            return;
        } else {
            $token = $this->token;
            require_once('view/backend/addPostView.php');
        }
    }


    // METTRE A JOUR UN POST
    public function update($id)
    {
        $input = new Input();

        if (isset($id) && $id > 0) {
            if (!empty($input->post('title')) && !empty($input->post('content'))) {

                $title = htmlspecialchars_decode($input->post('title'), ENT_QUOTES);
                $content = htmlspecialchars_decode($input->post('content'), ENT_QUOTES);
                $postManager = new PostManager();
                $userRightsManager = new UserRightManager();

                if ($userRightsManager->can('edit post')) {
                    $updatedPost = $postManager->updatePost($id, $title, $content);
                    if ($updatedPost === false) {
                        \Blog\flash_error('Impossible de modifier le post');
                    }
                } else {
                    \Blog\flash_error('Vous n\'avez pas les droits');
                }
            } else {
                \Blog\flash_error('Tous les champs ne sont pas remplis');
            }
        } else {
            \Blog\flash_error('Aucun post sélectionné');
        }
        header('Location: /Blog');
    }

    public function edit($id)
    {
        if (isset($id) && $id > 0) {

            $postManager = new PostManager();
            $post = $postManager->getPost($id);
        }
        if ($post === false) {
            \Blog\flash_error('Impossible d\'afficher le post');
        } else {
            $token = $this->token;
            require('view/backend/editPostView.php');
        }
    }

    // SUPPRIMER Post
    public function delete($id)
    {
        if (isset($id) && $id > 0) {

            $postManager = new PostManager();
            $userRightsManager = new UserRightManager();

            $deletePost = $postManager->deletePost($id);
        }
        if ($deletePost === false) {
            \Blog\flash_error('Impossible de supprimer le post');
        }
        header('Location: /Blog/admin/validation');
    }
}
