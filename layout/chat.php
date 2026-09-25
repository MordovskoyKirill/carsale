<?php
session_start();
require_once __DIR__ . '/../scripts/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /layout/login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);
$to      = intval($_GET['to'] ?? 0);
$ad_id   = intval($_GET['ad'] ?? 0);

$receiver = null;
if ($to > 0 && $to !== $user_id) {
    $rq = mysqli_query($conn, "SELECT ID, First_name, Last_name FROM Users WHERE ID = $to");
    if (mysqli_num_rows($rq) === 1) {
        $receiver = mysqli_fetch_assoc($rq);
    }
}

$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarSale</title>
    <link rel="stylesheet" href="/style/global_css/global.css">
    <link rel="stylesheet" href="/style/css/fonts.css">
    <link rel="stylesheet" href="/style/css/header.css">
    <link rel="stylesheet" href="/style/css/footer.css">
    <link rel="stylesheet" href="/style/css/chat.css">
    <script src="/components/header.js"></script>
    <script src="/components/footer.js"></script>
</head>
<body>
    <my-header data-logged-in="true"></my-header>

    <div class="chat-wrap">
        <aside class="chat-dialogs">
            <div class="chat-dialogs-title">Диалоги</div>
            <div id="dialogs-list" class="chat-dialogs-list"></div>
        </aside>

        <section class="chat-main">
            <?php if ($receiver): ?>
                <div class="chat-header">
                    <div class="chat-header-name">
                        <?=sanitize($receiver['First_name'] . ' ' . $receiver['Last_name'])?>
                    </div>
                    <?php if ($ad_id > 0): ?>
                        <a class="chat-header-ad" href="/layout/advetisement.php?id=<?=$ad_id?>">
                            К объявлению #<?=$ad_id?>
                        </a>
                    <?php endif; ?>
                </div>

                <div id="messages" class="chat-messages"></div>

                <form id="msg-form" class="chat-form" onsubmit="return false;">
                    <input type="hidden" id="receiver_id" value="<?=$receiver['ID']?>">
                    <input type="hidden" id="advertisement_id" value="<?=$ad_id?>">
                    <input type="hidden" id="csrf_token" value="<?=$csrf_token?>">
                    <input type="text" id="msg-input" class="chat-input" placeholder="Введите сообщение..." autocomplete="off">
                    <button type="submit" id="msg-send" class="chat-send">Отправить</button>
                </form>
            <?php else: ?>
                <div class="chat-empty">Выберите диалог слева или откройте объявление и нажмите «Связаться»</div>
            <?php endif; ?>
        </section>
    </div>

    <my-footer></my-footer>

    <script src="/scripts/chat.js"></script>
</body>
</html>