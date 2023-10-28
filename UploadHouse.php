<?php
include("ValidateAccount.php");
if (isset($_SESSION["AccountType"]) && ($_SESSION["AccountType"] === "Admin" || $_SESSION["AccountType"] === "Seller")) {
    $uploadDirectory = "HousePhotos/";

    function createDirectory($directoryPath) {
        if (!is_dir($directoryPath)) {
            if (!mkdir($directoryPath, 0777, true)) {
                header("Location: /project/Upload.html?notification=Fout bij het aanmaken van de map");
                exit();
            }
        }
    }

    if (isset($_POST["houseName"])) {
        include('dbconfig.php');


        $houseName = $_POST["houseName"];
        $houseDescription = $_POST["houseDescription"];
        $bedrooms = (int)$_POST["bedrooms"];
        $bathrooms = (int)$_POST["bathrooms"];
        $squareFootage = (float)$_POST["squareFootage"];
        $price = (float)$_POST["price"];
        $zipCode = $_POST["zipCode"];
        $userId = $_SESSION["UserId"];
        $photoCount = 1;
        $notification = null;

        if (isset($_POST["photoCount"])) {
            $photoCount = (int)$_POST["photoCount"];
        }

        if ($squareFootage < 20) {
            $notification .=  "Vierkante meters zijn ongeloofwaardig laag<br>";
        }
        
        if ($bedrooms <= 0) {
            $notification .= "Aantal slaapkamers is te laag.<br>";
        }
        
        if ($bedrooms > 20) {
            $notification .= "Aantal slaapkamers is te hoog. <br>";
        }
        
        if ($bathrooms <= 0) {
            $notification .= "Aantal badkamers is te laag. <br>";
        }
        
        if ($bathrooms > 10) {
            $notification .= "Aantal badkamers is te hoog. <br>";
        }
        
        if ($price < 75) {
            $notification .= "Prijs is te laag. <br>";
        }

        if (!empty($notification)) {
            header("Location: /project/Upload.html?notification=" . $notification);
            exit();
        }
        
        $sql = "INSERT INTO HouseAds (UserId, HouseName, Description, PhotoCount, Bedrooms, Bathrooms, SquareFootage, Price, ZipCode)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";


        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssssdddds", $userId, $houseName, $houseDescription, $photoCount, $bedrooms, $bathrooms, $squareFootage, $price, $zipCode);


            if (mysqli_stmt_execute($stmt)) {
                $adId = mysqli_insert_id($conn);
                $uploadDirectory .= $adId . "/";
                createDirectory($uploadDirectory);
                $uploadFile = $uploadDirectory . "1.jpg";


                if (move_uploaded_file($_FILES["housePhoto"]["tmp_name"], $uploadFile)) {
                    header("Location: /project/ad.php?AdId=" . $adId . "&notification=Advertentie is succesvol geüpload");
                    exit();
                } else {
                    header("Location: /project/Upload.html?notification=Fout bij het uploaden van de foto");
                    exit();
                }
            } else {
                header("Location: /project/Upload.html?notification=Fout bij het toevoegen van het huis");
                exit();
            }
        } else {
            header("Location: /project/Upload.html?notification=Fout bij het voorbereiden van de query");
            exit();
        }
    }else{
        header("Location: /project/Upload.html?notification=Geen geldig informatie");
        exit(); 
    }
} else {
    header("Location: /project/Upload.html?notification=Je hebt geen toestemming om huizen te uploaden. Log in met een verkopers of beheerdersaccount");
    exit();
}
?>
