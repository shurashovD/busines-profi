<?php

class Controller_tasks extends Controller
{
    static $tasks_count_on_page = 5;
    function __construct()
    {
        parent::__construct();
        $this->model = new Model_Tasks();
    }

    public function action_index()
    {
        $page = 1;
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
        }
        $this->read_tasks($page, $_SESSION['user_id']);
    }

    public function action_get()
    {
        $user_id = $_SESSION['user_id'];
        $page = $_GET['page'] ?? 1;
        $tasks = $this->model->get_tasks_by_user($user_id, $this::$tasks_count_on_page, $page);
        exit(json_encode($tasks));
    }

    public function action_create()
    {
        if ( !isset($_POST['text']) ) {
            http_response_code(400);
            die('Не указан текст задания');
        }

        $user_id = $_SESSION['user_id'];
        $page = $_GET['page'] ?? 1;
        $text = $_POST['text'];
        try
        {
            $this->model->add_task_to_user($user_id, $text);
        }
        catch (Exception $e)
        {
            http_response_code(500);
            die('Ошибка. Задание не создано');
        }

        $tasks = $this->model->get_tasks_by_user($user_id, $this::$tasks_count_on_page, $page);
        exit(json_encode($tasks));
    }

    public function read_tasks(int $page, string $user_id)
    {
        $this->view->generate('tasks_view.php', 'template_tasks.php');
    }

    public function action_important()
    {
        $page = $_GET['page'] ?? 1;
        $user_id = $_SESSION['user_id'];
        $id = $_POST['id'];

        $this->model->toggle_important($user_id, $id);
        try
        {
            $tasks = $this->model->get_tasks_by_user($user_id, $this::$tasks_count_on_page, $page);
            exit(json_encode($tasks));
        }
        catch (Exception $e)
        {
            http_response_code(500);
            die('Ошибка. Действие не выполнено');
        }
    }
    public function action_complete()
    {
        $page = $_GET['page'] ?? 1;
        $user_id = $_SESSION['user_id'];
        $id = $_POST['id'];

        $this->model->toggle_complete($user_id, $id);
        try {
            $tasks = $this->model->get_tasks_by_user($user_id, $this::$tasks_count_on_page, $page);
            exit(json_encode($tasks));
        } catch (Exception $e) {
            http_response_code(500);
            die('Ошибка. Действие не выполнено');
        }
    }

    public function action_update()
    {
        if (!isset($_POST['text'])) {
            http_response_code(400);
            die('Не указан текст задания');
        }

        $page = $_GET['page'] ?? 1;
        $user_id = $_SESSION['user_id'];
        $id = $_POST['id'];
        $text = $_POST['text'];

        try {
            $result = $this->model->update($user_id, $id, $text);
            if ( $result === 'forbidden' )
            {
                http_response_code(403);
                die('Нет доступа');
            }
            $tasks = $this->model->get_tasks_by_user($user_id, $this::$tasks_count_on_page, $page);
            exit(json_encode($tasks));
        } catch (Exception $e) {
            http_response_code(500);
            die('Ошибка. Действие не выполнено');
        }
    }

    public function action_remove()
    {
        if (!isset($_POST['id'])) {
            http_response_code(400);
            die('Не указана задача');
        }

        $page = $_GET['page'] ?? 1;
        $user_id = $_SESSION['user_id'];
        $id = $_POST['id'];

        try {
            $result = $this->model->remove($user_id, $id);
            if ($result === 'forbidden') {
                http_response_code(403);
                die('Нет доступа');
            }
            $tasks = $this->model->get_tasks_by_user($user_id, $this::$tasks_count_on_page, $page);
            exit(json_encode($tasks));
        } catch (Exception $e) {
            http_response_code(500);
            die('Ошибка. Действие не выполнено');
        }
    }
}