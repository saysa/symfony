<?php
//namespace Blog;
require_once('core/database/Database.php');

class Manager
{
    protected $db;

    public function __construct() {
        $this->db = Database::get();
    }
}
