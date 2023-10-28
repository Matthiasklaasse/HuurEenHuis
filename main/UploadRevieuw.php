<?php
include("dbconfig.php");
include("ValidateAccount.php");

$AdId = $_GET["AdId"];
$Content = $_POST["Content"];
$Stars = $_POST["Stars"];

if ($_SESSION["AccountType"] != "Seller"){
    if($_POST["Stars"] < 6){
        $sql = "INSERT INTO revieuws (RevieuwerId, AdId, Stars, Content, Time) VALUES (? , ?, ?, ?, NOW())";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iiss", $_SESSION["UserId"], $AdId, $Stars, $Content);
    }else{
        header("Location: /Project/Ad.php?AdId=" . $_GET["AdId"] . "&notification=Ongeldig waarden ingevoerd");
        exit();
    }
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: /Project/Ad.php?AdId=" . $_GET["AdId"] . "&notification=Revieuw sucsessvol geupload");
        exit();
    } else {
        header("Location: /Project/Ad.php?AdId=" . $_GET["AdId"] . "&notification=Revieuw niet sucsessvol geupload");
        exit();
    }
} else{
    header("Location: /Project/Ad.php?AdId=" . $_GET["AdId"] . "&notification=Revieuw niet sucsessvol geupload");
    exit(); 
}
?>