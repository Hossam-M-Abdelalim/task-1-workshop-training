<?php
session_start();

$mysqli = new mysqli("localhost", "root", "", "todo");
$method = $_SERVER['REQUEST_METHOD'];
$type = $_GET['type'];
$data = [];
$_POST = json_decode(file_get_contents("php://input"), true);

switch ($method) {
    case 'POST':
        if ($type === 'tasks') {
            $name = $_POST['name'];
            $mysqli->begin_transaction();
            try {
                $query = $mysqli->query("INSERT INTO `tasks` (`name`) VALUES('$name')");
                $row = $mysqli->query("SELECT * FROM `tasks` WHERE `id` = '$mysqli->insert_id}'")->fetch_assoc();
            } catch (Exception $exception) {
                $mysqli->rollback();
                echo json_encode([
                    'message' => 'Something went wrong.',
                    'error' => $exception->getMessage(),
                ]);
                die();
            }
            $mysqli->commit();
            $data = [
                'message' => 'Tasks has been added successfully',
                'data' => $row,
            ];
        }
        if ($type === 'lists') {
            $name = $_POST['name'];
            $task_id = $_POST['task_id'];
            $mysqli->begin_transaction();
            try {
                $query = $mysqli->query("INSERT INTO `lists` (`name`, `task_id`) VALUES('$name', '$task_id')");
                $row = $mysqli->query("SELECT * FROM `lists` WHERE `id` = '$mysqli->insert_id}'")->fetch_assoc();
            } catch (Exception $exception) {
                $mysqli->rollback();
                echo json_encode([
                    'message' => 'Something went wrong.',
                    'error' => $exception->getMessage(),
                ]);
                die();
            }
            $mysqli->commit();
            $data = [
                'message' => 'Lists has been added successfully',
                'data' => $row,
            ];
        }
        break;
    case 'PUT':
    case 'PATCH':
        $data = [];
        break;
    case 'DELETE':
        $data = [];
        break;
    default:
        if ($type === 'tasks') {
            $mysqli->real_query("SELECT * FROM `tasks` WHERE `is_active` = true ORDER BY id ASC");
            $result = $mysqli->use_result();
            foreach ($result as $item) {
                $data[] = $item;
            }
        }
        if ($type === 'lists') {
            $task_id = $_GET['task_id'];
            $mysqli->real_query("SELECT * FROM `lists` WHERE `task_id` = '$task_id'");
            $result = $mysqli->use_result();
            foreach ($result as $item) {
                $data[] = $item;
            }
        }
        break;
}

echo json_encode($data);
exit();