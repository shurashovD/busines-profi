<?php

class Model_Admin extends Model
{
    private $db;
    function __construct()
    {
        $this->db = new Db();
    }
    public function get_users()
    {
        $sql = "SELECT users.*, count(tasks.id) FROM `users` as users left outer join `tasks` as tasks ON tasks.user_id = users.id GROUP BY users.id";
        return $this->db->get_query($sql);
    }
}