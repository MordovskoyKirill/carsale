<?php
session_start();
require_once __DIR__ . '/../scripts/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /layout/login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);
$query = "SELECT * FROM Users WHERE ID = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

$query_ads = "SELECT 
                Advertisements.ID,
                Brands.Name AS Brand_Name,
                Advertisements.Model,
                Advertisements.Year,
                Advertisements.Price,
                Advertisements.Region,
                Advertisements.Mileage,
                Advertisements.Photo,
                BodyTypes.Name AS BodyType,
                Engines.Type AS EngineType,
                Drive.Type AS DriveType,
                GearBox.Type AS GearType,
                Colors.Name AS Color
              FROM Advertisements
              JOIN Brands ON Advertisements.Brand_ID = Brands.ID
              JOIN BodyTypes ON Advertisements.BodyType_ID = BodyTypes.ID
              JOIN Engines ON Advertisements.Engine_ID = Engines.ID
              JOIN Drive ON Advertisements.Drive_ID = Drive.ID
              JOIN GearBox ON Advertisements.GearBox_ID = GearBox.ID
              JOIN Colors ON Advertisements.Color_ID = Colors.ID
              WHERE Advertisements.User_ID = $user_id
              ORDER BY Advertisements.Created_date DESC";
$result_ads = mysqli_query($conn, $query_ads);
$ads = mysqli_fetch_all($result_ads, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarSale - Профиль</title>
    <link rel="stylesheet" href="/style/global_css/global.css">
    <link rel="stylesheet" href="/style/css/fonts.css">
    <link rel="stylesheet" href="/style/css/header.css">
    <link rel="stylesheet" href="/style/css/footer.css">
    <link rel="stylesheet" href="/style/css/common.css">
    <link rel="stylesheet" href="/style/css/catalog.css">
    <link rel="stylesheet" href="/style/css/profile.css">
    <script src="/components/header.js"></script>
    <script src="/components/footer.js"></script>
</head>
<body>
    <my-header data-logged-in="true"></my-header>

    <div class="profile-container">
        <div class="profile-card">
            <a href="/index.php" class="profile-back">&lt;</a>
            <div class="profile-info">
                <div class="profile-header">
                    <div class="profile-title">Профиль</div>
                </div>

                <div class="profile-field">
                    <strong>Email:</strong> <?=sanitize($user['email'])?>
                </div>
                <div class="profile-field">
                    <strong>Имя:</strong> <?=sanitize($user['First_name'])?>
                </div>
                <div class="profile-field">
                    <strong>Фамилия:</strong> <?=sanitize($user['Last_name'])?>
                </div>
                <div class="profile-field">
                    <strong>Телефон:</strong> <?=sanitize($user['Phone'])?>
                </div>
                <div class="profile-field">
                    <strong>Дата регистрации:</strong> <?=sanitize($user['Reg_date'])?>
                </div>
            </div>
        </div>
    </div>

    <div class="profile-ads">
        <h2 class="profile-ads-title">Мои объявления</h2>

        <?php if (empty($ads)): ?>
            <p class="empty">У вас пока нет объявлений</p>
        <?php else: ?>
            <?php foreach ($ads as $advertisement): ?>
                <?php
                $photos = explode(',', $advertisement['Photo']);
                $firstPhoto = trim($photos[0]);
                $imagePath = "/src/photos/" . $advertisement['ID'] . "/" . sanitize($firstPhoto);
                ?>
                <div class="profile-ad">
                    <a href="/layout/advetisement.php?id=<?=$advertisement['ID']?>">
                        <div class="catalog-box">
                            <img src="<?=$imagePath?>" alt="<?=sanitize($advertisement["Brand_Name"])?> <?=sanitize($advertisement["Model"])?>">
                            <div class="catalog-text">
                                <div class="model">
                                    <p><?=sanitize($advertisement["Brand_Name"])?> <?=sanitize($advertisement["Model"])?></p>
                                </div>
                                <div class="year">
                                    <p><?=$advertisement["Year"]?></p>
                                </div>
                                <div class="price">
                                    <p><?=number_format($advertisement["Price"], 0, '', ' ')?> ₽</p>
                                </div>
                                <div class="description">
                                    <p><?=sanitize($advertisement["Color"])?></p>
                                    <p style="grid-row: 2;"><?=sanitize($advertisement["DriveType"])?></p>
                                    <p><?=sanitize($advertisement["BodyType"])?></p>
                                    <p><?=sanitize($advertisement["GearType"])?></p>
                                </div>
                                <div class="mileage">
                                    <p><?=number_format($advertisement["Mileage"], 0, '', ' ')?> км</p>
                                </div>
                                <div class="user-text">
                                    <p><?=sanitize($advertisement["Region"])?></p>
                                </div>
                            </div>
                        </div>
                        <div class="profile-ad-actions">
                            <a href="/layout/edit_listing.php?id=<?=$advertisement['ID']?>" class="btn-edit"><img src="\src\icons\edit.png" alt=""></a>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <my-footer></my-footer>
</body>
</html>