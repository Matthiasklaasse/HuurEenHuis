<?php
include("ValidateAccount.php");
include("dbconfig.php");
$sql = "DELETE FROM houseads WHERE `houseads`.`UserID` = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION["UserId"]);
mysqli_stmt_execute($stmt);
$sql = "DELETE FROM revieuws WHERE `RevieuwerId` = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION["UserId"]);
mysqli_stmt_execute($stmt);
$sql = "DELETE FROM `users` WHERE `users`.`UserID` = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION["UserId"]);
mysqli_stmt_execute($stmt);
session_unset();
session_destroy();
header("Location: /Project/HompageFrame.php?notification=Account en huisjes verwijderd!");
die();
?>