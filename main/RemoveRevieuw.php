<?php
include("dbconfig.php");
include("ValidateAccount.php");

$RevieuwId = $_GET["RevieuwId"];

if (isset($_SESSION["UserId"])) {
    $sql = "SELECT * FROM Revieuws WHERE Id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $RevieuwId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && $Revieuw = mysqli_fetch_assoc($result)) {
        $ReviewerId = $Revieuw["RevieuwerId"];
        
        if ($_SESSION["UserId"] == $ReviewerId || $_SESSION["AccountType"] == "Admin") {
            $sql = "DELETE FROM Revieuws WHERE `Id` = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $RevieuwId);
            
            if (mysqli_stmt_execute($stmt)) {
                header("Location: /Project/Ad.php?AdId=" . $_GET["AdId"] . "&notification=Revieuw%20succesvol%20verwijderd");
                exit();
            } else {
                header("Location: /Project/Ad.php?AdId=" . $_GET["AdId"] . "&notification=Verwijdering%20mislukt");
                exit();
            }
        } else {
            header("Location: /Project/Ad.php?AdId=" . $_GET["AdId"] . "&notification=Geen%20toestemming");
            exit();
        }
    } else {
        header("Location: /Project/Ad.php?AdId=" . $_GET["AdId"] . "&notification=Revieuw%20niet%20gevonden");
        exit();
    }
} else {
    header("Location: /Project/Ad.php?AdId=" . $_GET["AdId"] . "&notification=Niet%20geauthenticeerd");
    exit();
}
?>
