<?php

require_once __DIR__ . '/config.php';

$userId = $_SESSION['user_id'] ?? 1;
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->prepare("SELECT * FROM `notifications` WHERE `user_id` = ? ORDER BY `id` DESC LIMIT 15");
    $stmt->execute([$userId]);
    $items = $stmt->fetchAll();

    $unreadStmt = $pdo->prepare("SELECT COUNT(*) FROM `notifications` WHERE `user_id` = ? AND `is_read` = 0");
    $unreadStmt->execute([$userId]);
    $unreadCount = (int)$unreadStmt->fetchColumn();

    jsonResponse([
        'status' => 'success',
        'data' => [
            'unread_count' => $unreadCount,
            'items' => $items
        ]
    ]);
} elseif ($method === 'POST') {
    $input = getRequestBody();
    $action = $input['action'] ?? 'mark_read';

    if ($action === 'mark_read' || $action === 'clear') {
        $pdo->prepare("UPDATE `notifications` SET `is_read` = 1 WHERE `user_id` = ?")
            ->execute([$userId]);

        jsonResponse([
            'status' => 'success',
            'message' => 'All notifications marked as read',
            'data' => ['unread_count' => 0]
        ]);
    } else {
        jsonError("Unknown action: $action");
    }
} else {
    jsonError('Method not allowed', 405);
}
