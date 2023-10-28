<?php
include("ValidateAccount.php");
include 'dbconfig.php';
$AdId = $_GET["AdId"]??null;

if ($AdId) {
    $sql = "SELECT * FROM HouseAds WHERE AdId = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $AdId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $House = mysqli_fetch_assoc($result);

    if (isset($_SESSION["UserId"])) {
        $IsOwner = $House["UserId"] == $_SESSION["UserId"] || $_SESSION["AccountType"] == "Admin";
        if ($IsOwner){
            $sql = "DELETE FROM revieuws WHERE `AdId` = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $AdId);
            mysqli_stmt_execute($stmt);

            $sql = "DELETE FROM houseads WHERE `houseads`.`AdId` = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $AdId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            header("Location: /Project/HompageFrame.php?notification=Huis is vervijderd");
            die();
        }
    }else{
        header("Location: /Project/HompageFrame.php?notification=Je bent geen eigenaar");
        die(); 
    }
} else {
    echo "AdId is not valid.";
}
?>