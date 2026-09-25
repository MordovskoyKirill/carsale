<?php
session_start();
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /layout/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /layout/profile.php");
    exit();
}

if (!check_csrf_token($_POST['csrf_token'] ?? '')) {
    header("Location: /layout/profile.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);
$ad_id   = intval($_POST['id'] ?? 0);

$check = mysqli_query($conn, "SELECT ID FROM Advertisements WHERE ID = $ad_id AND User_ID = $user_id");

if (mysqli_num_rows($check) > 0) {
    mysqli_query($conn, "DELETE FROM Messages WHERE Advertisement_ID = $ad_id");

    $dir = __DIR__ . '/../src/photos/' . $ad_id . '/';

    if (is_dir($dir)) {
        $files = glob($dir . '*');
        foreach ($files as $file) {
            if (is_file($file)) unlink($file);
        }
        rmdir($dir);
    }

    mysqli_query($conn, "DELETE FROM Advertisements WHERE ID = $ad_id AND User_ID = $user_id");
}

header("Location: /layout/profile.php");
exit();
?>