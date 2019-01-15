<?php

//namespace OpenClassrooms\Blog;
// Chargement des classes
require_once('models/PostManager.php');
require_once('models/CommentManager.php');
require_once('models/UserManager.php');
require_once('models/Manager.php');

/*use \OpenClassrooms\Blog\Model\PostManager;
use \OpenClassrooms\Blog\Model\CommentManager;
use \OpenClassrooms\Blog\Model\UserRightManager;
use \OpenClassrooms\Blog\Model\PaginationManager;*/


//CREATION CLASSE
class ControllerHome
{
    private $_postManager;
    private $_view;

    public function __construct($url)
    {
        if(isset($url) && count($url) > 1)
        {
            throw new Exception('Page introuvable');
        }
        else {
            $this->posts();
        }
    }

    private function posts()
    {
        $this->_postManager = new PostManager;
        $posts = $this->_postManager->getPosts();

        require_once('view/frontend/listPostsView.php');
        require_once('view/backend/validationView.php');

    }
}

//POST
//AFFICHER LA LISTE DES POSTS

function listPosts()
{
    $postManager = new PostManager();
    $userRightsManager = new UserRightManager();
    $pagination = new PaginationManager();
    $posts = $postManager->getPosts();

    require('view/frontend/listPostsView.php');
}

//VALIDATION VIEW

function showUnvalidated() {
    $userRightsManager = new UserRightManager();
    $postManager = new PostManager();
    $commentManager = new CommentManager();
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
    //$newComment = $commentManager->listUnvalidatedComments();
}


//POST
function showPost()
{
    if (isset($_GET['id']) && $_GET['id'] > 0) {

        $id = $_GET['id'];
        $postManager = new PostManager();
        $commentManager = new CommentManager();
        $userRightsManager = new UserRightManager();

        $post = $postManager->getPost($id);
        $comments = $commentManager->getComments($id);

        $status = $post['status'];

        if(isset($status)) {
            require('view/frontend/postView.php');

        } else {
            header('Location: index.php');
            flash_error('Ce post n\'est pas validé !');
        }
    }
    else {
        flash_error('Aucun identifiant de billet envoyé');
    }
}

// AJOUTER UN POST

function createPost()
{
    $userRightsManager = new UserRightManager();

    if(!$userRightsManager->can('add post'))
    {
        flash_error('Vous n\'avez pas les droits');
        header('Location: index.php');
        return;
    }


    if (!empty($_SESSION['current_user']) && !empty($_POST['title']) && !empty($_POST['content']))
    {
        $author = $_SESSION['current_user']['pseudo'];
        $title = htmlspecialchars($_POST['title']);
        $content = htmlspecialchars($_POST['content']);
        $postManager = new PostManager();

        $newPostLines = $postManager->postPost($title, $author, $content);

        if ($newPostLines === false) {
            falsh_error('Impossible d\'ajouter le post !');
        } else {
            header('Location: index.php');
            flash_sucess('Le post doit être validé avant d\'apparaître dans la liste');
        }
    }
}

function newPost()
{
    $userRightsManager = new UserRightManager();

    if(!$userRightsManager->can('add post'))
    {
        flash_error('Vous n\'avez pas les droits');
        header('Location: index.php');
        return;
    }
    require_once('view/backend/addPostView.php');
}

// VALIDATION POST
/* function listUnvalidatedPosts()
{

    $postManager = new PostManager();
    $userRightsManager = new UserRightManager();
    $unvalidated_posts = $postManager->getPosts();

    if($userRightsManager->can('validate'))
    {
        require('view/backend/validationView.php');

    } else {
        flash_error("Vous n'avez pas les droits");
        header('Location: index.php');
    }
} */


function validatePost()
{
    if(isset($_GET['id']) && $_GET['id'] > 0) {

        $id = $_GET['id'];

        $postManager = new PostManager();
        $userRightsManager = new UserRightManager();

        if($userRightsManager->can('validate')) {

            $post = $postManager->getPost($id);
            $status = $post['status'];

            if(isset($status) && $status === NULL)
            {
                $updatedStatus = $post->updatePostStatus($id, $status);

                if($updatedStatus === false)
                {
                    flash_error('Impossible de validé ce post');
                }
            } // else {
                //flash_sucess('Le post est validé');
        //    }

        } else {
            flash_error("Vous n'avez pas les droits");
        }
    } else {
        flash_error('Il n\'y a pas de post a valider');
    }
     header('Location: index.php');
}



// METTRE A JOUR UN POST

function updatePost()
{
    if(isset($_GET['id']) && $_GET['id'] > 0)
    {
        if (!empty($_POST['title']) && !empty($_POST['content']))
        {
            $id = $_GET['id'];

            $title = htmlspecialchars($_POST['title']);
            $content = htmlspecialchars($_POST['content']);
            $postManager = new PostManager();
            $userRightsManager = new UserRightManager();

            if($userRightsManager->can('edit post'))
            {
                $updatedPost = $postManager->updatePost($id, $title, $content);

                if($updatedPost === false)
                {
                    flash_error('Impossible de modifier le post');
                }
            } else {
                flash_error('Vous n\'avez pas les droits');
            }
        }
        else
        {
            flash_error('Tous les champs ne sont pas remplis');
        }
    }
    else
    {
        flash_error('Aucun post sélectionné');
    }
    header('Location: index.php');
}


function editPost()
{
    if(isset($_GET['id']) && $_GET['id'] > 0) {

        $id = $_GET['id'];

        $postManager = new PostManager();

        $post = $postManager->getPost($id);
    }
    if ($post === false)
    {
        throw new Exception('Impossible d\'afficher le post');
    }
    else {
        require('view/backend/editPostView.php');
    }
}

// SUPPRIMER Post
function deletePost()
{
    if(isset($_GET['id']) && $_GET['id'] > 0) {
        $id = $_GET['id'];

        $postManager = new PostManager();
        $userRightsManager = new UserRightManager();

        $deletePost = $postManager->deletePost($id);
    }
    if($deletePost === false)
    {
        flash_error('Impossible de supprimer le post');
    } else {
        header('Location: index.php');
    }
}

//Pagination
/*
function paginate()
{
if(isset($_GET['page']) && isset($_GET['per_page']))
{
$page = $_GET['page'] ? (int)$_GET['page'] : 1;
$perPage = $_GET['per_page'] <= 50 ? (int)$_GET['per_page'] : 5;
$pagination = new PaginationManager();

$start = ($page > 1) ? ($page * $perPage) - $perPage : 0;
$totalPages = $pagination->countArticles($totalPages);

var_dump($totalPages);

if(!empty($page) && !empty($perPage))
{
$totalPosts = $pagination->pageTotal();
$pages = ceil($totalPosts/$perPage);
}
}
} */

//COMMENTS
// AJOUTER UN COMMENTAIRE
function addComment()
{
    if (isset($_GET['id']) && $_GET['id'] > 0) {
        if (!empty($_SESSION['current_user']) && !empty($_POST['comment'])) {

            $postId = $_GET['id'];
            $author = $_SESSION['current_user']['pseudo'];
            $userId = $_SESSION['current_user']['id'];
            $comment = htmlspecialchars($_POST['comment']);
            $commentManager = new CommentManager();

            $affectedLines = $commentManager->postComment($postId, $author, $userId, $comment);

            if ($affectedLines === false) {
                throw new Exception('Impossible d\'ajouter le commentaire !');
            }
            else {
                header('Location: index.php?action=showPost&id=' . $postId);
            }
        } else {
            throw new Exception('Tous les champs ne sont pas remplis !');
        }
    } else {
        throw new Exception('Aucun identifiant de billet envoyé');
    }
}

// MODIFIER UN COMMENTAIRE
function updateComment()
{
    $userRightsManager = new UserRightManager();

    if(!$userRightsManager->can('edit comment'))
    {
        flash_error('Vous n\'avez pas les droits');
        header('Location: index.php');
        return;
    }

    if (isset($_GET['id']) && $_GET['id'] > 0) {
        if (!empty($_POST['comment'])) {

            $id = $_GET['id'];
            $comment = htmlspecialchars($_POST['comment']);

            $commentManager = new CommentManager();
            $newComment = $commentManager->updateComment($id, $comment);

            if ($newComment == false) {
                throw new Exception("Impossible d\'editer le commentaire !");
            }
            else {
                echo "commentaire :" . $_POST['comment'];
                header('Location: index.php?action=editComment&id=' . $id);
            }

        } else {
            flash_error('Tous les champs ne sont pas remplis');
        }
    }
    else {
        throw new Exception('Aucun identifiant de billet envoyé');
    }
}



// RECUPERER INFOS COMMENTAIRE ET POST
function editComment()
{
    if(isset($_GET['id']) && $_GET['id'] > 0) {

        $id = $_GET['id'];
        $postManager = new PostManager();
        $commentManager = new CommentManager();

        $comment = $commentManager->getComment($id);

        if(!empty($_SESSION['current_user']) && $comment['user_id'] === $_SESSION['current_user']['id'])
        {
            require('view/backend/commentView.php');

        } else {
            flash_error('Nope');
        }
    }
}



//users
//INSCRIPTION User
function newUser()
{
    if(!empty($_POST['pseudo']) && !empty($_POST['password']) && !empty($_POST['password2']) && !empty($_POST['email']))
    {
        $pseudo = htmlspecialchars($_POST['pseudo']);
        $password = htmlspecialchars($_POST['password']);
        $password2 = htmlspecialchars($_POST['password2']);
        $email = htmlspecialchars($_POST['email']);
        $hash = hash('sha256', $password);

        $userManager = new UserManager();

        if($password !== $password2)
        {
            flash_error('Les mots de passe ne correspondent pas');
            header('Location: index.php?action=signupForm');
        }
        else if(!empty($userManager->getUser($pseudo)))
        {
            flash_error('Ce pseudo est déjà utilisé :(');
            header('Location: index.php?action=signupForm');
        }
        else
        {
            $newUser = $userManager->addUser($pseudo, $hash, $email);
            flash_sucess('Bienvenue ' . $pseudo . ' !');
            header('Location: index.php');
        }
    }
    else
    {
        flash_error('Tous les champs ne sont pas remplis');
        header('Location: index.php?action=signupForm');
    }
}

function signupForm()
{
    require_once('view/frontend/signupView.php');
}

function login()
{
    if(!empty($_POST['pseudo']) && !empty($_POST['password']))
    {
        sleep(1);

        $pseudo = htmlspecialchars($_POST['pseudo']);
        $passform = htmlspecialchars($_POST['password']);

        $passform = hash('sha256', $passform);

        $userManager = new UserManager();

        $user = $userManager->getUser($pseudo);
        $password = $user['password'];

        if($passform === $password)
        {
            $_SESSION['current_user'] = $user;
            header('Location: index.php');
        }
        else
        {
            throw new Exception('Mauvais identifiants');
            header('Location: index.php?action=loginForm');
        }
    }
}

function loginForm()
{
    require_once('view/frontend/connexionView.php');
}

function logout()
{
    unset($_SESSION['current_user']);
    unset($_SESSION['expires_at']);
    header('Location: index.php');
}

function validateUser()
{
    $userManager = new UserManager();

    $new_user = $userManager->getUser();
    require('view/backend/validationView.php');
}

function isSessionExpired()
{
    if (!isset($_SESSION['current_user']))
    {
        return false;
    }

    if(!isset($_SESSION['expires_at']))
    {
        $_SESSION['expires_at'] = time() + 600;
        return false;
    }

    if($_SESSION['expires_at'] < time())
    {
        return true;
    }

    return false;
}

function sessionTicket()
{
    if (isset($_COOKIE['ct-s']) === isset($_SESSION['ct-s']))
    {
        // C'est reparti pour un tour
        $ticket = session_id().microtime().rand(0,9999999999);
        $ticket = hash('sha512', $ticket);
        $_COOKIE['ct-s'] = $ticket;
        $_SESSION['ct-s'] = $ticket;
    }
    else
    {
        // On détruit la session
        $_SESSION = [];
        unset($_SESSION);
        header('location:index.php');
    }
}

/*
function deleteUser()
{
$userRightsManager = new UserRightManager();

if(!$userRightsManager->can('delete user'))
{
flash_error('Vous n\'avez pas les droits');
header('Location: index.php');
return;
}
} */
