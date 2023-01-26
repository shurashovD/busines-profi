<?php

class Model_Main extends Model
{
    public function validate(string $mail)
    {
        $regex = '/^[a-zA-Z0-9]+@[a-zA-Z0-9-].[a-zA-Z0-9-]$/';
        if (preg_match($regex, $mail)) {
            return null;
        }
        return 'Неверный формат почты';
    }

    public function login(string $mail)
    {
        $db = new Db();
        $predicate = "`mail`='".$mail."'";
        $users = $db->get_data('users', $predicate);
        if ( isset($users[0]) )
        {
            return $users[0]['id'];
        }
        else
        {
            $this->register($mail);
            return $db->get_data('users', $predicate)[0]['id'];
        }
    }

    private function register(string $mail)
    {
        $data = array('mail' => $mail);
        $db = new Db();
        $db->insert_to_db($data, 'users');
    }
}