<?php
header("Content-Type: application/json");
include 'db_config.php';

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if ($action === 'adminLogin') {
        $username = $conn->real_escape_string($data['username']);
        $password = $conn->real_escape_string($data['password']);

        $result = $conn->query("SELECT * FROM admins WHERE username = '$username' AND password = '$password'");
        if ($result->num_rows > 0) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Invalid admin credentials!"]);
        }
    }

    if ($action === 'updateUser') {
        $userId = intval($data['id']);
        $balance = floatval($data['balance']);
        $invested = floatval($data['invested']);
        $vip = intval($data['vip']);
        $credit_score = intval($data['credit_score']);

        $sql = "UPDATE users SET balance = $balance, invested = $invested, vip = $vip, credit_score = $credit_score WHERE id = $userId";
        if ($conn->query($sql)) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => $conn->error]);
        }
    }

    if ($action === 'replyMessage') {
        $userId = intval($data['user_id']);
        $message = $conn->real_escape_string($data['message']);

        $sql = "INSERT INTO messages (user_id, message, sender) VALUES ($userId, '$message', 'Admin')";
        if ($conn->query($sql)) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => $conn->error]);
        }
    }
} else {
    if ($action === 'getUsers') {
        $result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        echo json_encode($users);
    }

    if ($action === 'getMessages') {
        $result = $conn->query("SELECT m.*, u.username FROM messages m JOIN users u ON m.user_id = u.id ORDER BY m.created_at DESC");
        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
        echo json_encode($messages);
    }
}
$conn->close();
