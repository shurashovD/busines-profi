<?php

class Controller_Admin extends Controller
{
    function __construct()
    {
        parent::__construct();
        $this->model = new Model_Admin();
    }

    public function action_index()
    {
        $data = $this->model->get_users();
        $this->view->generate('admin_view.php', 'template.php', $data);
    }
}