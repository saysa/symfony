<?php
namespace Blog\models;

require_once('models/Manager.php');

class UserRightManager extends Manager
{
    public function can($action)
    {
        if (isset($_SESSION['current_user'])) {
            $req = $this->db->prepare('SELECT COUNT(*) FROM rights INNER JOIN profiles_rights ON rights.id = profiles_rights.right_id WHERE profiles_rights.profile_id = ? AND description = ?');
            $req->execute(array($_SESSION['current_user']['profile_id'], $action));
            $res = $req->fetchColumn();

            $req->closeCursor();

            return $res > 0;
        } else {
            return false;
        }
    }
}