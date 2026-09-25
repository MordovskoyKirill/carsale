<?php
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

$type = $_GET['type'] ?? '';

$data = [];

switch ($type) {
    case 'brands':
        $query = "SELECT ID, Name FROM Brands ORDER BY Name";
        break;
    case 'bodytypes':
        $query = "SELECT ID, Name FROM BodyTypes ORDER BY Name";
        break;
    case 'engines':
        $query = "SELECT ID, Type FROM Engines ORDER BY Type";
        break;
    case 'gearbox':
        $query = "SELECT ID, Type FROM GearBox ORDER BY Type";
        break;
    case 'drive':
        $query = "SELECT ID, Type FROM Drive ORDER BY Type";
        break;
    default:
        echo json_encode([]);
        exit;
}

$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}

echo json_encode($data);
mysqli_close($conn);
?>