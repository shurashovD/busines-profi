<?php

class Model_Tasks extends Model
{
    private function has_user_task(int $user_id, int $id)
    {
        $db = new Db();

        $sql = "SELECT * FROM `tasks` WHERE `user_id`='" . $user_id . "' AND `id`='" . $id . "'";
        $tasks = $db->get_query($sql);

        return isset($tasks[0]);
    }

    public function get_tasks_by_user(int $user_id, int $limit = null, int $page = null)
    {
        $db = new Db();
        $sql = "SELECT * FROM `tasks` WHERE `user_id`='" . $user_id . "' ORDER BY `date` DESC";

        if (!is_null($limit) && !is_null($page))
        {
            $from = ($page - 1) * $limit;
            $to = $limit;
            $sql .= " LIMIT " . $from . "," . $to;
        }

        $tasks = $db->get_query($sql);
        foreach ($tasks as $key => $value) {
            $tasks[$key]['date'] = date('d.m.y', strtotime($value['date']));
        }

        $sql = "SELECT COUNT(*) FROM `tasks`";
        $tasks_count = $db->get_query($sql)[0]['COUNT(*)'];

        return ['tasks' => $tasks, 'count' => $tasks_count];
    }

    public function add_task_to_user(int $user_id, string $text)
    {
        $db = new Db();
        $values = array("user_id" => $user_id, "text" => $text);
        $db->insert_to_db($values, 'tasks');
    }

    public function toggle_important(int $user_id, int $id)
    {
        $db = new Db();

        $sql = "SELECT * FROM `tasks` WHERE `user_id`='" . $user_id . "' AND `id`='" . $id . "'";
        $tasks = $db->get_query($sql);

        if ( isset($tasks[0]) )
        {
            $value = $tasks[0]['important'] === "0" ? 1 : 0;
            $values = array("user_id" => $user_id, "important" => $value);
            $condition = "`id`=".$id;
            return $db->update_db('tasks', $values, $condition);
        }
    }

    public function toggle_complete(int $user_id, int $id)
    {
        $db = new Db();

        $sql = "SELECT * FROM `tasks` WHERE `user_id`='" . $user_id . "' AND `id`='" . $id . "'";
        $tasks = $db->get_query($sql);

        if (isset($tasks[0])) {
            $value = $tasks[0]['complete'] === "0" ? 1 : 0;
            $values = array("user_id" => $user_id, "complete" => $value);
            $condition = "`id`=" . $id;
            return $db->update_db('tasks', $values, $condition);
        }
    }

    public function update(int $user_id, int $id, string $text)
    {
        if (!$this->has_user_task($user_id, $id))
        {
            return 'forbidden';
        }

        $db = new Db();

        $values = array("user_id" => $user_id, "text" => $text);
        $condition = "`id`=" . $id;
        return $db->update_db('tasks', $values, $condition);
    }

    public function remove(int $user_id, int $id)
    {
        if (!$this->has_user_task($user_id, $id)) {
            return 'forbidden';
        }

        $db = new Db();
        $condition = "`user_id`='" . $user_id . "' AND `id`='" . $id . "'";
        return $db->rm_note('tasks', $condition);
    }
}