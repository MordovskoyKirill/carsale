<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Не авторизован']);
    exit;
}

$user_id = intval($_SESSION['user_id']);

$query = "SELECT 
            IF(M.Sender_ID = $user_id, M.Receiver_ID, M.Sender_ID) AS other_id,
            M.Advertisement_ID,
            M.Text AS last_text,
            M.Created_date AS last_date,
            U.First_name,
            U.Last_name,
            (SELECT COUNT(*) FROM Messages 
             WHERE Receiver_ID = $user_id 
               AND Sender_ID = IF(M.Sender_ID = $user_id, M.Receiver_ID, M.Sender_ID)
               AND IsRead = 0) AS unread
          FROM Messages M
          JOIN Users U ON U.ID = IF(M.Sender_ID = $user_id, M.Receiver_ID, M.Sender_ID)
          WHERE M.ID IN (
              SELECT MAX(ID) FROM Messages
              WHERE Sender_ID = $user_id OR Receiver_ID = $user_id
              GROUP BY IF(Sender_ID = $user_id, Receiver_ID, Sender_ID), Advertisement_ID
          )
          ORDER BY M.Created_date DESC";

$result = mysqli_query($conn, $query);

$dialogs = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $dialogs[] = [
            'other_id'   => intval($row['other_id']),
            'ad_id'      => intval($row['Advertisement_ID']),
            'name'       => sanitize($row['First_name'] . ' ' . $row['Last_name']),
            'last_text'  => sanitize(mb_substr($row['last_text'], 0, 40)),
            'last_date'  => date('H:i d.m', strtotime($row['last_date'])),
            'unread'     => intval($row['unread'])
        ];
    }
}

echo json_encode(['dialogs' => $dialogs]);
?>