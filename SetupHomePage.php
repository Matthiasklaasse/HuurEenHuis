<?php
include("ValidateAccount.php");
$dbHost = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "huureenhuis.nltest";
$conn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbName);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$Houses = array();

$sortOrder = "default";
if (isset($_POST['sort'])) {
    $sortOrder = $_POST['sort'];
}

if (!empty($_POST['Name'])) {
    $searchTerm = mysqli_real_escape_string($conn, $_POST['Name']);
    $sql = "SELECT * FROM HouseAds WHERE (Description LIKE '%$searchTerm%' OR HouseName LIKE '%$searchTerm%')";

    if (!empty($_POST['MinPrice'])) {
        $minPrice = (float)$_POST['MinPrice'];
        $sql .= " AND Price >= " . mysqli_real_escape_string($conn, $minPrice);
    }

    if (!empty($_POST['MaxPrice'])) {
        $maxPrice = (float)$_POST['MaxPrice'];
        $sql .= " AND Price <= " . mysqli_real_escape_string($conn, $maxPrice);
    }
} else {
    $sql = "SELECT * FROM HouseAds";

    if (!empty($_POST['MinPrice'])) {
        $minPrice = (float)$_POST['MinPrice'];
        $sql .= " WHERE Price >= " . mysqli_real_escape_string($conn, $minPrice);
    }

    if (!empty($_POST['MaxPrice'])) {
        $maxPrice = (float)$_POST['MaxPrice'];
        if (strpos($sql, "WHERE") === false) {
            $sql .= " WHERE Price <= " . mysqli_real_escape_string($conn, $maxPrice);
        } else {
            $sql .= " AND Price <= " . mysqli_real_escape_string($conn, $maxPrice);
        }
    }
}

if ($sortOrder === "price_low_to_high") {
    $sql .= " ORDER BY Price ASC";
} elseif ($sortOrder === "price_high_to_low") {
    $sql .= " ORDER BY Price DESC";
} elseif ($sortOrder === "newest_first") {
    $sql .= " ORDER BY DateAdded DESC";
} elseif ($sortOrder === "oldest_first") {
    $sql .= " ORDER BY DateAdded ASC";
}

$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $ownerId = $row['UserId'];
        $ownerQuery = "SELECT * FROM users WHERE userID = " . mysqli_real_escape_string($conn, $ownerId);
        $ownerResult = mysqli_query($conn, $ownerQuery);

        if ($ownerResult) {
            $ownerData = mysqli_fetch_assoc($ownerResult);
            if ($ownerData['IsBanned'] == 0) {
                $Houses[] = $row;
            }
        }
    }
} else {
    die("Error fetching data: " . mysqli_error($conn));
}

mysqli_close($conn);
?>
