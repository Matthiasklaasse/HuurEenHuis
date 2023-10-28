<?php
session_start();
#echo $_SESSION["AccountType"]."<br>";
if(isset($_SESSION["UserId"])){
    $userId = $_SESSION["UserId"];
    $sql = "SELECT IsBanned, Email, Username, FirstName, LastName, AccountType FROM Users WHERE UserID = ?";

    include("dbconfig.php");

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            if ($row['IsBanned'] == 1) {
                session_unset();
                session_destroy();
                header("Location: /Project/HompageFrame.php?notification=Je bent uitgelogd omdat je account geblokeerd is!");
                exit();
            } else {
                $_SESSION["UserId"] = $userId;
                $_SESSION["Email"] = $row["Email"];
                $_SESSION["UserName"] = $row["Username"];
                $_SESSION["FirstName"] = $row["FirstName"];
                $_SESSION["LastName"] = $row["LastName"];
                $_SESSION["AccountType"] = $row["AccountType"];
                #echo $_SESSION["AccountType"]."<br>";
            }
        } else {
            die("Error cannot verify user is not banned user: " . mysqli_error($conn));
        }
    } else {
        die("Error preparing statement: " . mysqli_error($conn));
    }
}
?>