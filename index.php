<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarSale</title>
    <link rel="stylesheet" href="/style/global_css/global.css">
    <link rel="stylesheet" href="/style/css/index.css">
    <link rel="stylesheet" href="/style/css/header.css">
    <link rel="stylesheet" href="/style/css/filter.css">
    <link rel="stylesheet" href="/style/css/footer.css">
    <link rel="stylesheet" href="/style/css/catalog.css">
    <link rel="stylesheet" href="/style/css/pagination.css">
    <link rel="stylesheet" href="/style/css/fonts.css">
    <script src="/components/header.js"></script>
    <script src="/components/filter.js"></script>
    <script src="/components/footer.js"></script>
</head>
<body>
    <my-header data-logged-in="<?=isset($_SESSION['user_id']) ? 'true' : 'false'?>"></my-header>

    <div class="filter-wrap">
        <form class="filter-panel" action="/index.php" method="GET">
            <my-filter data-type="brands"    data-name="brand"      data-placeholder="Марка"        data-all="Все марки"></my-filter>
            <my-filter data-type="bodytypes" data-name="bodyType"   data-placeholder="Тип кузова"   data-all="Все кузова"></my-filter>
            <my-filter data-type="engines"   data-name="engineType" data-placeholder="Тип двигателя" data-all="Все двигатели"></my-filter>
            <my-filter data-type="gearbox"   data-name="gearBox"    data-placeholder="КПП"          data-all="Все КПП"></my-filter>
            <my-filter data-type="drive"     data-name="drive"      data-placeholder="Привод"       data-all="Все приводы"></my-filter>

            <select id="condition" name="condition" class="filter-select">
                <option value="">Любое состояние</option>
                <option value="0">Новое</option>
                <option value="1">С пробегом</option>
            </select>

            <div class="filter-FromTo">
                <input id="yearFrom" name="yearFrom" class="filter-From" placeholder="Год от" type="number"/>
                <input id="yearTo" name="yearTo" class="filter-To" placeholder="до" type="number"/>
            </div>
            <div class="filter-FromTo">
                <input id="priceFrom" name="priceFrom" class="filter-From" placeholder="Цена от" type="number"/>
                <input id="priceTo" name="priceTo" class="filter-To" placeholder="до" type="number"/>
            </div>

            <button type="submit">Показать</button>
        </form>
    </div>

    <div id="catalog">
        <?php include __DIR__ . '/layout/catalog.php'; ?>
    </div>

    <my-footer></my-footer>
</body>
</html>