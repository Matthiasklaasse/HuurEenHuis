<?php
ini_set('session.gc_maxlifetime', 604801);

$dbHost = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "huureenhuis.nltest";
$conn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbName);
$response = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST)) {
        die("Geen post-gegevens gevonden. Blijkbaar ben je wat vergeten in te vullen.");
    }

    $userName = $_POST["UserName"];
    $password = $_POST["Password"];
    $passwordHash = hash('sha256', $password);

    $sql = "SELECT * FROM Users WHERE Username = '".$userName."' AND PasswordHash = '".$passwordHash."'";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if ($row["IsBanned"] == 0){
                echo "Inloggen geslaagd. Welkom terug!";

                if (session_status() !== PHP_SESSION_ACTIVE) {
                    session_start();
                }

                session_regenerate_id(true);

                $_SESSION["SessionId"] = session_create_id("SESSION-");
                $_SESSION["UserId"] = $row["UserID"];
                $_SESSION["Email"] = $row["Email"];
                $_SESSION["UserName"] = $row["Username"];
                $_SESSION["FirstName"] = $row["FirstName"];
                $_SESSION["LastName"] = $row["LastName"];
                $_SESSION["AccountType"] = $row["AccountType"];
                $response = "Inloggen is gelukt!";
            }else{
                $response = "Oeps! Dit account is geblokkeerd.";
            }
        } else {
            $response = "Sorry, inloggen mislukt, controleer je gegevens.";
        }

        mysqli_stmt_close($stmt);
    } else {
        $response = "Fout: " . mysqli_error($conn);
    }
} else {
    $response = "Use a HTTP post request";
}

header("Location: /Project/HompageFrame.php?notification=".$response);
die();
?>
