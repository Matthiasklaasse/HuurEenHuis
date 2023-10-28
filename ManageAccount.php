<?php
include("dbconfig.php");
include("ValidateAccount.php");

$userID = $_SESSION["UserId"];
$username = $email = $firstName = $lastName = $notification = "";

if (isset($_SESSION["UserId"])) {    
    $sql = "SELECT * FROM Users WHERE UserID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userID);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $username = $row["Username"];
    $email = $row["Email"];
    $firstName = $row["FirstName"];
    $lastName = $row["LastName"];
    mysqli_stmt_close($stmt);
    
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $newUsername = $_POST["newUsername"];
        $newEmail = $_POST["newEmail"];
        $newFirstName = $_POST["newFirstName"];
        $newLastName = $_POST["newLastName"];
        
        $checkSql = "SELECT UserID FROM Users WHERE Username = ?";
        $checkStmt = mysqli_prepare($conn, $checkSql);
        mysqli_stmt_bind_param($checkStmt, "s", $newUsername);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);
        $existingUser = mysqli_fetch_assoc($checkResult);
        mysqli_stmt_close($checkStmt);

        if ($existingUser && $existingUser["UserID"] !== $userID) {
            $notification = "Deze naam is al in gebruik";
        } else {
            $updateSql = "UPDATE Users SET Username = ?, Email = ?, FirstName = ?, LastName = ? WHERE UserID = ?";
            $updateStmt = mysqli_prepare($conn, $updateSql);
            mysqli_stmt_bind_param($updateStmt, "ssssi", $newUsername, $newEmail, $newFirstName, $newLastName, $userID);
            
            if (mysqli_stmt_execute($updateStmt)) {
                $notification = "Account info is geüpdatet";
                $username = $newUsername;
                $email = $newEmail;
                $firstName = $newFirstName;
                $lastName = $newLastName;
                $_SESSION["UserName"] = $username;
                $_SESSION["FirstName"] = $firstName;
                $_SESSION["LastName"] = $lastName;
            } else {
                $notification = "Account updaten is niet gelukt";
            }
            mysqli_stmt_close($updateStmt);
        }
        header("Location: /Project/ManageAccount.php?id=".$userID."&notification=".$notification);
        die();
    } else {
        $notification = null;
    }
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
        body {
            font-family: Arial, sans-serif;
            background-color: rgb(39, 39, 39);
            color: #fff;
        }
        .Centered {
            display: flex;
            align-items: center;
        }
        .Logo {
            width: 60px;
            height: 60px;
        }
        .Slogan {
            margin-left: 10px;
            font-size: 24px;
            text-decoration: none;
            color: #fff;
        }
        .mid {
            width: 70vw;
            margin: 0 auto;
            padding: 10vw;
            border: 1px solid #ccc;
            border-radius: 0.2vw;
            color: #555;
            background-color: #f9f9f9;
        }
        table {
            width: 100%;
        }
        table td {
            padding: 10px 0;
        }
        select, input[type="text"], input[type="email"], input[type="password"] {
            width: 50vw;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        select {
            height: 36px;
        }
        input[type="submit"] {
            background-color: #555;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px;
            width: 50vw;
            cursor: pointer;
        }
        button{
            background-color: #555;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px;
            font-size: 0.6vw;
            width: 10vw;
            cursor: pointer;  
        }
        button:hover, input[type="submit"]:hover {
            background-color: #3a3a3a;
        }
        @media screen and (min-width: 700px) {
            .mid {
                padding: 1vw;
            }
        }
    </style>
</head>
<body>
    <div id="NotificationsBar" class="NotificationsBar"></div>
    <div class="Topbar">
        <div class="Centered">
            <a href="/project/HompageFrame.php"><img src="Images/Logo.ico" class="Logo" alt="HuurEenHuis Logo"></a>
            <a class="Slogan" onclick="location = '/project/HompageFrame.php'">HuurEenHuis: ieder huis een thuis</a>
        </div>
    </div>
    <br>
    <form method="post" action="/project/ManageAccount.php?id=".<?php echo $userID; ?> id="account-settings-form">
        <div class="mid">
            <table>
                <tr>
                    <td>gebruikersnaam:</td>
                    <td><input type="text" name="newUsername" required value="<?php echo $username; ?>"></td>
                </tr>
                <tr>
                    <td>e-mailadres:</td>
                    <td><input type="email" name="newEmail" required value="<?php echo $email; ?>"></td>
                </tr>
                <tr>
                    <td>voornaam:</td>
                    <td><input type="text" name="newFirstName" required value="<?php echo $firstName; ?>"></td>
                </tr>
                <tr>
                    <td>achternaam:</td>
                    <td><input type="text" name="newLastName" required value="<?php echo $lastName; ?>"></td>
                </tr>
            </table>
            <a href="/project/DeleteAccount.php"><button type="button">Verwijder Account en huisjes</button></a>
            <input type="submit" name="submit" value="Opslaan">
        </div>
    </form>
    <script src="Notifications.js"></script>
</body>
</html>
