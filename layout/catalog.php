<div class="catalog">
    <?php
    require_once __DIR__ . '/../scripts/db.php';

    $brand      = isset($_GET['brand']) ? intval($_GET['brand']) : 0;
    $bodyType   = isset($_GET['bodyType']) ? intval($_GET['bodyType']) : 0;
    $engineType = isset($_GET['engineType']) ? intval($_GET['engineType']) : 0;
    $gearBox    = isset($_GET['gearBox']) ? intval($_GET['gearBox']) : 0;
    $drive      = isset($_GET['drive']) ? intval($_GET['drive']) : 0;
    $condition  = isset($_GET['condition']) && $_GET['condition'] !== '' ? intval($_GET['condition']) : -1;
    $yearFrom   = isset($_GET['yearFrom']) ? intval($_GET['yearFrom']) : 0;
    $yearTo     = isset($_GET['yearTo']) ? intval($_GET['yearTo']) : 0;
    $priceFrom  = isset($_GET['priceFrom']) ? intval($_GET['priceFrom']) : 0;
    $priceTo    = isset($_GET['priceTo']) ? intval($_GET['priceTo']) : 0;
    $search     = isset($_GET['search']) ? trim($_GET['search']) : '';
    $searchEsc  = mysqli_real_escape_string($conn, $search);

    $where = [];
    if ($brand > 0)      $where[] = "Advertisements.Brand_ID = $brand";
    if ($bodyType > 0)   $where[] = "Advertisements.BodyType_ID = $bodyType";
    if ($engineType > 0) $where[] = "Advertisements.Engine_ID = $engineType";
    if ($gearBox > 0)    $where[] = "Advertisements.GearBox_ID = $gearBox";
    if ($drive > 0)      $where[] = "Advertisements.Drive_ID = $drive";
    if ($condition >= 0) $where[] = "Advertisements.Cond = $condition";
    if ($yearFrom > 0)   $where[] = "Advertisements.Year >= $yearFrom";
    if ($yearTo > 0)     $where[] = "Advertisements.Year <= $yearTo";
    if ($priceFrom > 0)  $where[] = "Advertisements.Price >= $priceFrom";
    if ($priceTo > 0)    $where[] = "Advertisements.Price <= $priceTo";
    if ($search !== '')  $where[] = "(Brands.Name LIKE '%$searchEsc%' OR Advertisements.Model LIKE '%$searchEsc%')";

    $whereClause = empty($where) ? '' : ' WHERE ' . implode(' AND ', $where);

    $perPage = 5;
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $offset = ($page - 1) * $perPage;

    $countQuery = "SELECT COUNT(*) AS total
                   FROM Advertisements
                   JOIN Brands ON Advertisements.Brand_ID = Brands.ID
                   $whereClause";
    $countRes = mysqli_query($conn, $countQuery);
    $countRow = mysqli_fetch_assoc($countRes);
    $total = intval($countRow['total']);
    $totalPages = max(1, ceil($total / $perPage));

    $query = "SELECT 
                Advertisements.ID,
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
                Colors.Name as Color
            FROM Advertisements
            JOIN Users ON Advertisements.User_ID = Users.ID
            JOIN Brands ON Advertisements.Brand_ID = Brands.ID
            JOIN BodyTypes ON Advertisements.BodyType_ID = BodyTypes.ID
            JOIN Engines ON Advertisements.Engine_ID = Engines.ID
            JOIN Drive ON Advertisements.Drive_ID = Drive.ID
            JOIN GearBox ON Advertisements.GearBox_ID = GearBox.ID
            JOIN Colors ON Advertisements.Color_ID = Colors.ID
            $whereClause
            ORDER BY Advertisements.Created_date DESC
            LIMIT $perPage OFFSET $offset";

    $result = mysqli_query($conn, $query);
    $advertisements = mysqli_fetch_all($result, MYSQLI_ASSOC);

    if (empty($advertisements)) {
        echo "<p class='empty'>Объявления не найдены</p>";
    } else {
        foreach ($advertisements as $advertisement) {
            $photos = explode(',', $advertisement['Photo']);
            $firstPhoto = trim($photos[0]);
            $imagePath = "/src/photos/" . $advertisement['ID'] . "/" . sanitize($firstPhoto);
            ?>
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
                            <p><?=sanitize($advertisement["Lname"])?> <?=sanitize($advertisement["Fname"])?></p>
                            <p class="region"><?=sanitize($advertisement["Region"])?></p>
                        </div>
                    </div>
                </div>
            </a>
            <?php
        }
    }
    ?>

    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php
            $params = $_GET;

            $params['page'] = max(1, $page - 1);
            $prevClass = ($page <= 1) ? 'page-btn disabled' : 'page-btn';
            echo '<a href="?' . http_build_query($params) . '#catalog" class="' . $prevClass . '">&lt;</a>';

            for ($i = 1; $i <= $totalPages; $i++) {
                $params['page'] = $i;
                $active = ($i == $page) ? ' active' : '';
                echo '<a href="?' . http_build_query($params) . '#catalog" class="page-btn' . $active . '">' . $i . '</a>';
            }

            $params['page'] = min($totalPages, $page + 1);
            $nextClass = ($page >= $totalPages) ? 'page-btn disabled' : 'page-btn';
            echo '<a href="?' . http_build_query($params) . '#catalog" class="' . $nextClass . '">&gt;</a>';
            ?>
        </div>
    <?php endif; ?>
</div>