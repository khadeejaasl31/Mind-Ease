<?php
session_start();
require_once 'db.php';
header('Content-Type: application/json; charset=utf-8');

function reflection_response($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

$userId = (int) ($_SESSION['user_id'] ?? 0);
if (!$userId) {
    reflection_response(['ok' => false, 'message' => 'Please sign in to save reflections.'], 401);
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $statement = $conn->prepare("SELECT id, content AS text, DATE_FORMAT(created_at, '%h:%i %p') AS time FROM reflections WHERE user_id = :user_id ORDER BY created_at DESC");
        $statement->execute([':user_id' => $userId]);
        reflection_response(['ok' => true, 'reflections' => $statement->fetchAll()]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $content = trim($_POST['content'] ?? '');
        if ($content === '' || mb_strlen($content) > 2000) {
            reflection_response(['ok' => false, 'message' => 'Write a reflection between 1 and 2,000 characters.'], 422);
        }
        $statement = $conn->prepare('INSERT INTO reflections (user_id, content) VALUES (:user_id, :content)');
        $statement->execute([':user_id' => $userId, ':content' => $content]);
        reflection_response(['ok' => true]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        parse_str(file_get_contents('php://input'), $input);
        $id = (int) ($input['id'] ?? 0);
        $statement = $conn->prepare('DELETE FROM reflections WHERE id = :id AND user_id = :user_id');
        $statement->execute([':id' => $id, ':user_id' => $userId]);
        reflection_response(['ok' => true]);
    }

    reflection_response(['ok' => false, 'message' => 'Method not allowed.'], 405);
} catch (PDOException $error) {
    reflection_response(['ok' => false, 'message' => 'Unable to save your reflection right now.'], 500);
}