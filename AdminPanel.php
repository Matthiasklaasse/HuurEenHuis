<?php
include("dbconfig.php");
include("ValidateAccount.php");

if (isset($_SESSION["AccountType"])) {
    if ($_SESSION["AccountType"] !== "Admin") {
        header("Location: /Project/HompageFrame.php?notification=Je hebt geen rechten om dat te zien");
        die();
    }
} else {
    header("Location: /Project/HompageFrame.php?notification=Je moet ingelogd zijn als admin");
    die();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['Email'];
    $username = $_POST['Username'];
    $firstName = $_POST['FirstName'];
    $lastName = $_POST['LastName'];
    $accountType = $_POST['AccountType'];
    $isBanned = isset($_POST['IsBanned']) ? 1 : 0;
    $userID = $_POST['ID'];

    $sql = "UPDATE `users` SET `Email` = ?, `Username` = ?, `FirstName` = ?, `LastName` = ?, `AccountType` = ?, `IsBanned` = ? WHERE `UserID` = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'sssssii', $email, $username, $firstName, $lastName, $accountType, $isBanned, $userID);
    $result = mysqli_stmt_execute($stmt);

    if (!$result) {
        echo "Error updating user data for UserID: $userID";
    } else{
        header("Location: /Project/AdminPanel.php?notification=Data sucessvol aangepast");
        die();
    }
}

$sql = "SELECT * FROM `users`";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$Users = array();

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $Users[] = $row;
    }
} else {
    echo "Error executing the SQL query.";
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="GeneralStyle.css">
    <link rel="icon" type="image/x-icon" href="Images/Logo.ico">
    <style>
        table {
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 1px;
            text-align: left;
            font-size: 1vw;
        }

        input {
            width: 5vw;
        }

        input[type="email"] {
            width: 15vw;
        }

        input[type="password"] {
            width: 75%;
            font-family: monospace;
        }

        select {
            width: 10vw;
        }
    </style>
    <title>HuurEenHuis.nl (ieder huis een thuis)</title>
</head>
<body>
    <div id="NotificationsBar" class="NotificationsBar"></div>
    <div class="Topbar">
        <div class="Centered">
            <a href="/project/HompageFrame.php"><img src="Images/Logo.ico" class="Logo" alt="HuurEenHuis Logo"></a>
            <a class="Slogan" onclick="location = '/project/HompageFrame.php'">HuurEenHuis: ieder huis een thuis</a>
        </div>
    </div>

    <?php
    if (isset($Users) && is_array($Users)) {
        echo '<table>';
        echo '<th>User ID</th><th>Gebruikersnaam</th><th>Email</th><th>Voornaam</th><th>Achternaam</th><th>Account Type</th><th>Is Geblokkeerd</th><th>Account gemaakt op</th><th>Update</th>';
        foreach ($Users as $user) {
            echo '<form method="post">';
            echo '<tr>';
            echo '</tr>';
            echo '<tr>';
            echo '<td>' . $user["UserID"] . '</td>';
            echo '<td><input type="text" name="Username" value="' . $user["Username"] . '"></td>';
            echo '<td><input type="email" name="Email" value="' . $user["Email"] . '"></td>';
            echo '<td><input type="text" name="FirstName" value="' . $user["FirstName"] . '"></td>';
            echo '<td><input type="text" name="LastName" value="' . $user["LastName"] . '"></td>';
            echo '<td><select name="AccountType">';
            echo '<option value="Buyer" ' . ($user["AccountType"] === 'Buyer' ? 'selected' : '') . '>Koper</option>';
            echo '<option value="Seller" ' . ($user["AccountType"] === 'Seller' ? 'selected' : '') . '>Verkoper</option>';
            echo '<option value="Admin" ' . ($user["AccountType"] === 'Admin' ? 'selected' : '') . '>Beheerder</option>';
            echo '</select></td>';
            echo '<td><input type="checkbox" name="IsBanned" ' . ($user["IsBanned"] ? 'checked' : '') . '></td>';
            echo '<td>' . $user["CreatedAt"] . '</td>';
            echo '<td><button type="submit" name="ID" value="' . $user["UserID"] . '">Update</button></td>';
            echo '</tr>';
            echo '</form>';
        }
        echo '</table>';
    }
    ?>

    <script src="Notifications.js"></script>
</body>
</html>