<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Не авторизован']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Неверный метод']);
    exit;
}

if (!check_csrf_token($_POST['csrf_token'] ?? '')) {
    echo json_encode(['error' => 'Ошибка безопасности']);
    exit;
}

$sender_id   = intval($_SESSION['user_id']);
$receiver_id = intval($_POST['receiver_id'] ?? 0);
$ad_id       = intval($_POST['advertisement_id'] ?? 0);

if (empty($receiver_id) || empty($text)) {
    echo json_encode(['error' => 'Пустое сообщение']);
    exit;
}

if ($receiver_id === $sender_id) {
    echo json_encode(['error' => 'Нельзя писать себе']);
    exit;
}

$check = mysqli_query($conn, "SELECT ID FROM Users WHERE ID = $receiver_id");
if (mysqli_num_rows($check) === 0) {
    echo json_encode(['error' => 'Получатель не найден']);
    exit;
}

$text_esc  = mysqli_real_escape_string($conn, $_POST['text'] ?? '');
$ad_value  = $ad_id > 0 ? $ad_id : 'NULL';
$created   = date('Y-m-d H:i:s');

$query = "INSERT INTO Messages (Sender_ID, Receiver_ID, Advertisement_ID, Text, Created_date, IsRead)
          VALUES ($sender_id, $receiver_id, $ad_value, '$text_esc', '$created', 0)";

if (mysqli_query($conn, $query)) {
    echo json_encode(['ok' => true]);
} else {
    echo json_encode(['error' => mysqli_error($conn)]);
}
?>