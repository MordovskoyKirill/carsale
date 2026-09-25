<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Не авторизован']);
    exit;
}

$user_id     = intval($_SESSION['user_id']);
$chat_with   = intval($_GET['chat_with'] ?? 0);
$ad_id       = intval($_GET['advertisement_id'] ?? 0);

if (empty($chat_with)) {
    echo json_encode(['error' => 'Не указан собеседник']);
    exit;
}

mysqli_query($conn, "UPDATE Messages SET IsRead = 1 
                     WHERE Receiver_ID = $user_id AND Sender_ID = $chat_with AND IsRead = 0");

$ad_cond = $ad_id > 0 ? " AND Advertisement_ID = $ad_id" : "";

$query = "SELECT ID, Sender_ID, Text, Created_date 
          FROM Messages
          WHERE ((Sender_ID = $user_id AND Receiver_ID = $chat_with)
              OR (Sender_ID = $chat_with AND Receiver_ID = $user_id))
          $ad_cond
          ORDER BY Created_date ASC";

$result = mysqli_query($conn, $query);

$messages = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $messages[] = [
            'id'      => $row['ID'],
            'sender'  => intval($row['Sender_ID']),
            'text'    => sanitize($row['Text']),
            'date'    => date('H:i d.m', strtotime($row['Created_date'])),
            'is_mine' => intval($row['Sender_ID']) === $user_id
        ];
    }
}

echo json_encode(['messages' => $messages, 'me' => $user_id]);
?>