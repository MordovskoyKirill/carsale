<?php
session_start();
require_once __DIR__ . '/../scripts/db.php';

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!check_csrf_token($_POST['csrf_token'] ?? '')) {
        $error_message = 'Ошибка безопасности';
    } else {
        $email = sanitize($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($email)) {
            $error_message = 'Введите Email';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = 'Некорректный Email';
        } elseif (empty($password)) {
            $error_message = 'Введите пароль';
        } else {
            $email_escaped = mysqli_real_escape_string($conn, $email);
            $query = "SELECT ID, email, Password, First_name, Last_name, Role FROM Users WHERE email = '$email_escaped'";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) == 1) {
                $user = mysqli_fetch_assoc($result);
                if (password_verify($password, $user['Password'])) {
                    $_SESSION['user_id'] = $user['ID'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_first_name'] = $user['First_name'];
                    $_SESSION['user_last_name'] = $user['Last_name'];
                    $_SESSION['user_role'] = $user['Role'];
                    header("Location: /index.php");
                    exit();
                } else {
                    $error_message = 'Неверный пароль';
                }
            } else {
                $error_message = 'Пользователь с таким Email не найден';
            }
        }
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
    <link rel="stylesheet" href="/style/css/common.css">
    <link rel="stylesheet" href="/style/css/sign.css">
    <script src="/components/header.js"></script>
    <script src="/components/footer.js"></script>
</head>
<body>
    <my-header data-logged-in="<?=isset($_SESSION['user_id']) ? 'true' : 'false'?>"></my-header>

    <div class="login-container">
        <div class="auth-card">
            <div class="auth-inner">
                <div class="auth-title">Вход</div>

                <?php if ($error_message): ?>
                    <div style="color: red; background: #ffe6e6; padding: 10px; border-radius: 10px; margin-bottom: 15px; font-family: Roboto; font-size: 14px; text-align: center;">
                        <?=sanitize($error_message)?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?=$csrf_token?>">
                    <div class="input-group">
                        <input type="email" class="input-field" name="email" placeholder="Email" value="<?=isset($email) ? sanitize($email) : ''?>" required>
                    </div>
                    <div class="input-group">
                        <input type="password" class="input-field" name="password" placeholder="Пароль" required>
                    </div>
                    <div class="forgot-link">
                        <a href="#">Забыли пароль?</a>
                    </div>
                    <button type="submit" class="action-btn btn-primary">Войти</button>
                </form>

                <a href="/layout/signup.php">
                    <button class="action-btn btn-secondary">Создать аккаунт</button>
                </a>
            </div>
        </div>
    </div>

    <my-footer></my-footer>
</body>
</html>