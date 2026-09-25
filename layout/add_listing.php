<?php
session_start();
require_once __DIR__ . '/../scripts/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /layout/login.php");
    exit();
}

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!check_csrf_token($_POST['csrf_token'] ?? '')) {
        $error_message = 'Ошибка безопасности';
    } else {
        $brand      = intval($_POST['brand'] ?? 0);
        $model      = sanitize($_POST['model'] ?? '');
        $year       = intval($_POST['year'] ?? 0);
        $bodyType   = intval($_POST['bodyType'] ?? 0);
        $transmission = intval($_POST['transmission'] ?? 0);
        $engine     = intval($_POST['engine'] ?? 0);
        $drive      = intval($_POST['drive'] ?? 0);
        $color      = intval($_POST['color'] ?? 0);
        $condition  = intval($_POST['condition'] ?? 0);
        $region     = sanitize($_POST['region'] ?? '');
        $mileage    = intval($_POST['mileage'] ?? 0);
        $comment    = sanitize($_POST['comment'] ?? '');
        $govNumber  = sanitize($_POST['govNumber'] ?? '');
        $vinNumber  = sanitize($_POST['vinNumber'] ?? '');
        $price      = intval($_POST['price'] ?? 0);

        if (empty($brand) || empty($model) || empty($year) || empty($price) || empty($region)) {
    $error_message = 'Заполните обязательные поля';
        } else {
            $user_id  = intval($_SESSION['user_id']);
            $created  = date('Y-m-d H:i:s');

            $insert = "INSERT INTO Advertisements 
                (User_ID, Brand_ID, Model, Year, Price, Region, Mileage, Cond, BodyType_ID, Engine_ID, Drive_ID, GearBox_ID, Color_ID, Number, Vin, Comment, Created_date, Photo) 
                VALUES 
                ($user_id, $brand, '$model', $year, $price, '$region', $mileage, $condition, $bodyType, $engine, $drive, $transmission, $color, '$govNumber', '$vinNumber', '$comment', '$created', 'default.png')";

            if (!mysqli_query($conn, $insert)) {
                $error_message = 'Ошибка: ' . mysqli_error($conn);
            } else {
                $new_id = mysqli_insert_id($conn);
                $photoNames = [];

                if (!empty($_FILES['photos']['name'][0])) {
                    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                    $uploadDir = __DIR__ . '/../src/photos/' . $new_id . '/';

                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    for ($i = 0; $i < count($_FILES['photos']['name']); $i++) {
                        if ($_FILES['photos']['error'][$i] !== 0) continue;

                        $ext = strtolower(pathinfo($_FILES['photos']['name'][$i], PATHINFO_EXTENSION));

                        if (!in_array($ext, $allowed)) {
                            $error_message = 'Недопустимый формат файла';
                            break;
                        }
                        if ($_FILES['photos']['size'][$i] > 5 * 1024 * 1024) {
                            $error_message = 'Файл слишком большой (макс. 5 МБ)';
                            break;
                        }

                        $newName = time() . '_' . $i . '_' . basename($_FILES['photos']['name'][$i]);
                        if (move_uploaded_file($_FILES['photos']['tmp_name'][$i], $uploadDir . $newName)) {
                            $photoNames[] = $newName;
                        }
                    }
                }

                if (empty($error_message) && !empty($photoNames)) {
                    $photoStr = mysqli_real_escape_string($conn, implode(',', $photoNames));
                    mysqli_query($conn, "UPDATE Advertisements SET Photo = '$photoStr' WHERE ID = $new_id");
                }

                if (empty($error_message)) {
                    $success_message = 'Объявление добавлено!';
                }
            }
        }
    }
}

$csrf_token = generate_csrf_token();

$brands     = mysqli_fetch_all(mysqli_query($conn, "SELECT ID, Name FROM Brands ORDER BY Name"), MYSQLI_ASSOC);
$bodyTypes  = mysqli_fetch_all(mysqli_query($conn, "SELECT ID, Name FROM BodyTypes ORDER BY Name"), MYSQLI_ASSOC);
$engines    = mysqli_fetch_all(mysqli_query($conn, "SELECT ID, Type FROM Engines ORDER BY Type"), MYSQLI_ASSOC);
$gearBoxes  = mysqli_fetch_all(mysqli_query($conn, "SELECT ID, Type FROM GearBox ORDER BY Type"), MYSQLI_ASSOC);
$drives     = mysqli_fetch_all(mysqli_query($conn, "SELECT ID, Type FROM Drive ORDER BY Type"), MYSQLI_ASSOC);
$colors     = mysqli_fetch_all(mysqli_query($conn, "SELECT ID, Name FROM Colors ORDER BY Name"), MYSQLI_ASSOC);
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
    <link rel="stylesheet" href="/style/css/add_listing.css">
    <script src="/components/header.js"></script>
    <script src="/components/footer.js"></script>
</head>
<body>
    <my-header data-logged-in="true"></my-header>

    <div class="listing-container">
        <div class="listing-card">
            <div class="listing-inner">
                <div class="listing-title">Размещение объявления</div>

                <?php if ($error_message): ?>
                    <div class="msg msg-error"><?=sanitize($error_message)?></div>
                <?php endif; ?>

                <?php if ($success_message): ?>
                    <div class="msg msg-success"><?=sanitize($success_message)?></div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?=$csrf_token?>">

                    <div class="input-group">
                        <select class="input-field" name="brand" required>
                            <option value="" disabled selected hidden>Марка</option>
                            <?php foreach ($brands as $b): ?>
                                <option value="<?=$b['ID']?>"><?=sanitize($b['Name'])?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="input-group">
                        <input type="text" class="input-field" name="model" placeholder="Модель" required>
                    </div>

                    <div class="input-group">
                        <input type="number" class="input-field" name="year" placeholder="Год выпуска" required>
                    </div>

                    <div class="input-group">
                        <select class="input-field" name="bodyType" required>
                            <option value="" disabled selected hidden>Тип кузова</option>
                            <?php foreach ($bodyTypes as $bt): ?>
                                <option value="<?=$bt['ID']?>"><?=sanitize($bt['Name'])?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="input-group">
                        <select class="input-field" name="transmission" required>
                            <option value="" disabled selected hidden>КПП</option>
                            <?php foreach ($gearBoxes as $gb): ?>
                                <option value="<?=$gb['ID']?>"><?=sanitize($gb['Type'])?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="input-group">
                        <select class="input-field" name="engine" required>
                            <option value="" disabled selected hidden>Двигатель</option>
                            <?php foreach ($engines as $e): ?>
                                <option value="<?=$e['ID']?>"><?=sanitize($e['Type'])?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="input-group">
                        <select class="input-field" name="drive" required>
                            <option value="" disabled selected hidden>Привод</option>
                            <?php foreach ($drives as $d): ?>
                                <option value="<?=$d['ID']?>"><?=sanitize($d['Type'])?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="input-group">
                        <select class="input-field" name="color" required>
                            <option value="" disabled selected hidden>Цвет</option>
                            <?php foreach ($colors as $c): ?>
                                <option value="<?=$c['ID']?>"><?=sanitize($c['Name'])?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="input-group">
                        <select class="input-field" name="condition" required>
                            <option value="" disabled selected hidden>Состояние</option>
                            <option value="0">Новое</option>
                            <option value="1">С пробегом</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <input type="text" class="input-field" name="region" placeholder="Регион" required>
                    </div>

                    <div class="input-group">
                        <input type="number" class="input-field" name="mileage" placeholder="Пробег" required>
                    </div>

                    <div class="input-group photo-group">
                        <label for="photos" class="photo-upload">
                            <img src="/src/icons/camera.svg" alt="Фото" class="photo-icon-img">
                            <div class="photo-text">Выберите фото (можно несколько)</div>
                            <input type="file" id="photos" name="photos[]" accept="image/*" multiple style="display: none;">
                        </label>
                        <div id="preview" class="photo-preview"></div>
                    </div>

                    <div class="input-group comment-group">
                        <textarea class="input-field textarea-field" name="comment" placeholder="Комментарий"></textarea>
                    </div>

                    <div class="input-group">
                        <input type="text" class="input-field" name="govNumber" placeholder="Гос. номер">
                    </div>

                    <div class="input-group">
                        <input type="text" class="input-field" name="vinNumber" placeholder="VIN номер">
                    </div>

                    <div class="input-group">
                        <input type="number" class="input-field" name="price" placeholder="Цена" required>
                    </div>

                    <button type="submit" class="action-btn btn-primary">Разместить</button>
                </form>
            </div>
        </div>
    </div>

    <my-footer></my-footer>

    <script>
        document.getElementById('photos').addEventListener('change', function () {
            const preview = document.getElementById('preview');
            preview.innerHTML = '';
            for (let i = 0; i < this.files.length; i++) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '80px';
                    img.style.height = '60px';
                    img.style.objectFit = 'cover';
                    img.style.borderRadius = '8px';
                    img.style.marginRight = '6px';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(this.files[i]);
            }
        });
    </script>
</body>
</html>