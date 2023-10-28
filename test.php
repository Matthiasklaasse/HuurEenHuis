<?php
session_start();
include("ValidateAccount.php");
echo $_SESSION["UserName"]."<br>";
print_r($_SESSION);
if (isset($_SESSION["AccountType"]) && ($_SESSION["AccountType"] === "Admin" || $_SESSION["AccountType"] === "Seller")){
    echo "cool";
}
?>
