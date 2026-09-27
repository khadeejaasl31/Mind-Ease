<?php
session_start();
require_once 'db.php';
header('Content-Type: application/json; charset=utf-8');

function journal_response($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

$userId = (int) ($_SESSION['user_id'] ?? 0);
if (!$userId) {
    journal_response(['ok' => false, 'message' => 'Please sign in to save your journal.'], 401);
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $statement = $conn->prepare('SELECT content, updated_at FROM journal_entries WHERE user_id = :user_id LIMIT 1');
        $statement->execute([':user_id' => $userId]);
        $entry = $statement->fetch();
        journal_response(['ok' => true, 'content' => $entry['content'] ?? '', 'updated_at' => $entry['updated_at'] ?? null]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $content = trim($_POST['content'] ?? '');
        if (mb_strlen($content) > 10000) {
            journal_response(['ok' => false, 'message' => 'Please keep your journal entry under 10,000 characters.'], 422);
        }
        $statement = $conn->prepare('INSERT INTO journal_entries (user_id, content) VALUES (:user_id, :content) ON DUPLICATE KEY UPDATE content = VALUES(content)');
        $statement->execute([':user_id' => $userId, ':content' => $content]);
        journal_response(['ok' => true, 'message' => 'Journal saved.']);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $statement = $conn->prepare('DELETE FROM journal_entries WHERE user_id = :user_id');
        $statement->execute([':user_id' => $userId]);
        journal_response(['ok' => true]);
    }

    journal_response(['ok' => false, 'message' => 'Method not allowed.'], 405);
} catch (PDOException $error) {
    journal_response(['ok' => false, 'message' => 'Unable to save your journal right now.'], 500);
}