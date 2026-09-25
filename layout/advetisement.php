<?php
session_start();
require_once __DIR__ . '/../scripts/db.php';

$advertisement_id = intval($_GET["id"] ?? 0);

$query = "SELECT 
            Advertisements.ID,
            Advertisements.User_ID,
            Users.Last_name AS Lname,
            Users.First_name AS Fname,
            Brands.Name AS Brand_Name,
            Advertisements.Model,
            Advertisements.Year,
            Advertisements.Price,
            Advertisements.Region,
            Advertisements.Mileage,
            BodyTypes.Name AS BodyType,
            Engines.Type as EngineType,
            Drive.Type as DriveType,
            GearBox.Type as GearType,
            Advertisements.Photo,
            Colors.Name as Color,
            Advertisements.Comment
        FROM Advertisements
        JOIN Users ON Advertisements.User_ID = Users.ID
        JOIN Brands ON Advertisements.Brand_ID = Brands.ID
        JOIN BodyTypes ON Advertisements.BodyType_ID = BodyTypes.ID
        JOIN Engines ON Advertisements.Engine_ID = Engines.ID
        JOIN Drive ON Advertisements.Drive_ID = Drive.ID
        JOIN GearBox ON Advertisements.GearBox_ID = GearBox.ID
        JOIN Colors ON Advertisements.Color_ID = Colors.ID
        WHERE Advertisements.ID = $advertisement_id";
$result = mysqli_query($conn, $query);
$advertisements = mysqli_fetch_all($result, MYSQLI_ASSOC);
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
    <link rel="stylesheet" href="/style/css/advertisement.css">
    <script src="/components/header.js"></script>
    <script src="/components/footer.js"></script>
</head>
<body>
    <my-header data-logged-in="<?=isset($_SESSION['user_id']) ? 'true' : 'false'?>"></my-header>

    <?php foreach ($advertisements as $advertisement): ?>
        <?php
        $photos = explode(',', $advertisement['Photo']);
        $firstPhoto = trim($photos[0]);
        $imagePath = "/src/photos/" . $advertisement['ID'] . "/" . sanitize($firstPhoto);
        ?>
        <div class="advertisement">
            <a href="/index.php" class="advertisement-back">&lt;</a>
            <div class="ad-name">
                <p><?=sanitize($advertisement["Brand_Name"])?> <?=sanitize($advertisement["Model"])?>, <?=$advertisement["Year"]?> г.</p>
                <img src="<?=$imagePath?>" alt="<?=sanitize($advertisement["Brand_Name"])?> <?=sanitize($advertisement["Model"])?>">
                <?php if (count($photos) > 1): ?>
                    <div class="gallery">
                        <?php foreach ($photos as $p): ?>
                            <?php $p = trim($p); if ($p === '') continue; ?>
                            <img src="/src/photos/<?=$advertisement['ID']?>/<?=sanitize($p)?>" alt="Фото">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="description">
                <p class="price"><?=number_format($advertisement["Price"], 0, '', ' ')?> ₽</p>
                <div class="grid-descript">
                    <p>Двигатель</p>
                    <p><?=sanitize($advertisement["EngineType"])?></p>
                    <p>КПП</p>
                    <p><?=sanitize($advertisement["GearType"])?></p>
                    <p>Привод</p>
                    <p><?=sanitize($advertisement["DriveType"])?></p>
                    <p>Цвет</p>
                    <p><?=sanitize($advertisement["Color"])?></p>
                    <p>Пробег</p>
                    <p><?=number_format($advertisement["Mileage"], 0, '', ' ')?> км</p>
                    <p>Тип кузова</p>
                    <p><?=sanitize($advertisement["BodyType"])?></p>
                </div>
                <div class="comment">
                    <p>Комментарий продавца:</p>
                    <p><?=sanitize($advertisement["Comment"])?></p>
                </div>
                <p>Город: <?=sanitize($advertisement["Region"])?></p>

                <?php if (isset($_SESSION['user_id']) && intval($_SESSION['user_id']) !== intval($advertisement['User_ID'])): ?>
                    <a href="/layout/chat.php?to=<?=$advertisement['User_ID']?>&ad=<?=$advertisement['ID']?>">
                        <button class="btn">Связаться</button>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <my-footer></my-footer>
</body>
</html>