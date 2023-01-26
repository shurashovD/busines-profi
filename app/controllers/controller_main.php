<?php

class Controller_Main extends Controller
{
    function __construct()
    {
        parent::__construct();
        $this->model = new Model_Main();
    }

    public function action_index()
    {
        $data = array(
            'is_auth' => false,
            'is_admin' => false
        );
        
        if ( isset($_POST['mail']) ) {
            $mail = $_POST['mail'];
            $data['mail'] = $mail;
            $validate = $this->model->validate($mail);
            if (is_null($validate)) {
                $_SESSION['user_id'] = $this->model->login($mail);
                $data['is_auth'] = true;
            } else {
                $data['is_auth'] = false;
                $data['validation_error'] = $validate;
            }
        }
        $this->view->generate('auth_view.php', 'template.php', $data);
    }

    public function action_logout()
    {
        $_SESSION = [];
        header("Location: /");
        exit;
    }
}