<?php
session_start();
require_once __DIR__ . '/../scripts/db.php';

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!check_csrf_token($_POST['csrf_token'] ?? '')) {
        $error_message = 'Ошибка безопасности';
    } else {
        $email = sanitize($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');
        $last_name = sanitize($_POST['last_name'] ?? '');
        $first_name = sanitize($_POST['first_name'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');

        if (empty($email)) {
            $error_message = 'Введите Email';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = 'Некорректный Email';
        } elseif (empty($password)) {
            $error_message = 'Введите пароль';
        } elseif (strlen($password) < 6) {
            $error_message = 'Пароль должен содержать минимум 6 символов';
        } elseif ($password !== $confirm_password) {
            $error_message = 'Пароли не совпадают';
        } elseif (empty($last_name)) {
            $error_message = 'Введите фамилию';
        } elseif (empty($first_name)) {
            $error_message = 'Введите имя';
        } elseif (empty($phone)) {
            $error_message = 'Введите телефон';
        } elseif (!preg_match('/^[0-9]{10,12}$/', $phone)) {
            $error_message = 'Телефон должен содержать 10-12 цифр';
        } else {
            $email_escaped = mysqli_real_escape_string($conn, $email);
            $last_name_escaped = mysqli_real_escape_string($conn, $last_name);
            $first_name_escaped = mysqli_real_escape_string($conn, $first_name);
            $phone_escaped = mysqli_real_escape_string($conn, $phone);

            $check_query = "SELECT ID FROM Users WHERE email = '$email_escaped'";
            $check_result = mysqli_query($conn, $check_query);

            if (mysqli_num_rows($check_result) > 0) {
                $error_message = 'Пользователь с таким Email уже существует';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $reg_date = date('Y-m-d H:i:s');
                $insert_query = "INSERT INTO Users (email, Password, First_name, Last_name, Phone, IsConfirmed, Role, Reg_date) 
                                VALUES ('$email_escaped', '$hashed_password', '$first_name_escaped', '$last_name_escaped', '$phone_escaped', 0, 0, '$reg_date')";

                if (mysqli_query($conn, $insert_query)) {
                    $success_message = 'Регистрация успешна!';
                    echo '<meta http-equiv="refresh" content="2;url=/layout/login.php">';
                } else {
                    $error_message = 'Ошибка регистрации: ' . mysqli_error($conn);
                }
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
                <div class="auth-title">Регистрация</div>

                <?php if ($error_message): ?>
                    <div style="color: red; background: #ffe6e6; padding: 10px; border-radius: 10px; margin-bottom: 15px; font-family: Roboto; font-size: 14px; text-align: center;">
                        <?=sanitize($error_message)?>
                    </div>
                <?php endif; ?>

                <?php if ($success_message): ?>
                    <div style="color: green; background: #e6ffe6; padding: 10px; border-radius: 10px; margin-bottom: 15px; font-family: Roboto; font-size: 14px; text-align: center;">
                        <?=sanitize($success_message)?> Перенаправление...
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
                    <div class="input-group">
                        <input type="password" class="input-field" name="confirm_password" placeholder="Подтвердить пароль" required>
                    </div>
                    <div class="input-group">
                        <input type="text" class="input-field" name="last_name" placeholder="Фамилия" value="<?=isset($last_name) ? sanitize($last_name) : ''?>" required>
                    </div>
                    <div class="input-group">
                        <input type="text" class="input-field" name="first_name" placeholder="Имя" value="<?=isset($first_name) ? sanitize($first_name) : ''?>" required>
                    </div>
                    <div class="input-group">
                        <input type="tel" class="input-field" name="phone" placeholder="Телефон (10-12 цифр)" value="<?=isset($phone) ? sanitize($phone) : ''?>" required>
                    </div>
                    <button type="submit" class="action-btn btn-primary">Зарегистрироваться</button>
                </form>
            </div>
        </div>
    </div>

    <my-footer></my-footer>
</body>
</html>