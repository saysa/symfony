<?php

require_once('controllers/BaseController.php');
require_once('models/UserManager.php');
require_once('models/PostManager.php');
require_once('models/UserRightManager.php');

class AdminController extends BaseController
{
    //VALIDATION VIEW
    function showUnvalidated()
    {
        $userRightsManager = new UserRightManager();
        $postManager = new PostManager();
        $userManager = new UserManager();

        if($userRightsManager->can('validate'))
        {
            $unvalidated_posts = $postManager->getPosts();
            $new_users = $userManager->getNewUsers();

            require('view/backend/validationView.php');

        } else {
            flash_error("Vous n'avez pas les droits");
            header('Location: index.php');
        }
    }

    function validatePost()
    {
        $postManager = new PostManager();
        $userRightsManager = new UserRightManager();

        if($userRightsManager->can('validate'))
        {
            if(isset($_GET['id']) && $_GET['id'] > 0)

            {
                $id = $_GET['id'];

                $post = $postManager->getPost($id);
                $newStatus = $postManager->updatePostStatus($id);

            } else {

                flash_error("Aucun post à valider");
            }

        } else {
            flash_error("Vous n'avez pas les droits");
            header('Location: index.php');
        }
    }

    function validateUser()
    {
        $userManager = new UserManager();
        $userRightsManager = new UserRightManager();

        if(isset($_GET['id']) && $_GET['id'] > 0)
        {
            $id = $_GET['id'];

            if ($userRightsManager->can('validate')) {

                if(!empty($_GET['profileId']))
                {
                    $profileId = $_GET['profileId'];
                    $newUser = $userManager->profileUser($id, $profileId);

                    flash_success("Le profil de l'utilisateur a bien été modifié");

                } else {

                    flash_error('Impossible de changer le profile de cet utilisateur');
                }
            } else {
                flash_error("Vous n'avez pas les droits");
            }
        } header('Location: /Blog');
    }
}