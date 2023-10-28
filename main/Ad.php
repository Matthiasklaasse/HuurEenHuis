<?php
include("SetupHomePage.php");
include 'dbconfig.php';

$AdId = $_GET['AdId'] ?? null;
$IsOwner = null;
$Revieuws = [];

if ($AdId) {
    $sql = "SELECT * FROM HouseAds WHERE AdId = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $AdId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        $House = mysqli_fetch_assoc($result);

        $sql = "SELECT * FROM Users WHERE UserID = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $House['UserId']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $Owner = mysqli_fetch_assoc($result);

        if (isset($_SESSION["UserId"])) {
            $IsOwner = $House["UserId"] == $_SESSION["UserId"] || $_SESSION["AccountType"] == "Admin";
        }
    } else {
        echo "Error fetching house data.";
    }
} else {
    echo "AdId is not valid.";
}

if (isset($_POST["Submit"])) {
    $notification = "";

    $updatedZipCode = $_POST["zipCode"];
    $updatedDescription = $_POST["houseDescription"];
    $updatedHouseName = $_POST["houseName"];
    $updatedPrice = $_POST["price"];
    $updatedBedrooms = $_POST["bedrooms"];
    $updatedBathrooms = $_POST["bathrooms"];
    $updatedSquareFootage = $_POST["squareFootage"];

    if ($updatedSquareFootage < 20) {
        $notification .= "Vierkante meters zijn ongeloofwaardig laag. ";
    }

    if ($updatedBedrooms <= 0) {
        $notification .= "Aantal slaapkamers is te laag. ";
    }

    if ($updatedBedrooms > 20) {
        $notification .= "Aantal slaapkamers is te hoog. ";
    }

    if ($updatedBathrooms <= 0) {
        $notification .= "Aantal badkamers is te laag. ";
    }

    if ($updatedBathrooms > 10) {
        $notification .= "Aantal badkamers is te hoog. ";
    }

    if ($updatedPrice < 75) {
        $notification .= "Prijs is te laag. ";
    }

    if (!empty($notification)) {
        header("Location: /Project/Ad.php?AdId=" . $AdId . "&notification=" . $notification);
        die();
    }

    if ($IsOwner) {
        $updateSql = "UPDATE HouseAds SET HouseName = ?, Description = ?, Bedrooms = ?, Bathrooms = ?, ZipCode = ?, SquareFootage = ?, Price = ? WHERE AdId = ?";
        $updateStmt = mysqli_prepare($conn, $updateSql);
        mysqli_stmt_bind_param($updateStmt, "ssiisiii", $updatedHouseName, $updatedDescription, $updatedBedrooms, $updatedBathrooms, $updatedZipCode, $updatedSquareFootage, $updatedPrice, $AdId);
        
        if (mysqli_stmt_execute($updateStmt)) {
            $notificationMessage = "Het huis is succesvol bijgewerkt";
            header("Location: /Project/Ad.php?AdId=" . $AdId . "&notification=" . $notificationMessage);
            die();
        } else {
            $errorNotificationMessage = "Er is een fout opgetreden bij het bijwerken van het huis.";
            header("Location: /Project/Ad.php?AdId=" . $AdId . "&notification=" . $errorNotificationMessage);
            die();
        }
    } else {
        header("Location: /Project/HompageFrame.php?notification=Geen toestemming om dit huis te bewerken");
        die();
    }
}
$sql = "SELECT * FROM `revieuws` WHERE `AdId` = ?;";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $AdId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $Revieuws[] = $row;
    }
}
$AverageScore = null;
$TotalScore = 0;
foreach ($Revieuws as $Revieuw) {
    $TotalScore = $Revieuw["Stars"];
}
if ($TotalScore > 0){
    $AverageScore = $TotalScore/count($Revieuws);
}

$ownerId = $House["UserId"];
$ownerQuery = "SELECT * FROM users WHERE userID = " . mysqli_real_escape_string($conn, $ownerId);
$ownerResult = mysqli_query($conn, $ownerQuery);
if ($ownerResult) {
    $ownerData = mysqli_fetch_assoc($ownerResult);
    if ($ownerData['IsBanned'] == 1) {
        header("Location: /Project/HompageFrame.php?notification=De eigenaar van dat huis is geblokeerd dus kan je het huis niet zien");
        die();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="GeneralStyle.css">
    <link rel="stylesheet" href="Ad.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HuurEenHuis.nl (ieder huis een thuis)</title>
    <link rel="icon" type="image/x-icon" href="Images/Logo.ico">
</head>
<body>
    <div id="NotificationsBar" class="NotificationsBar"></div>
    <div class="Topbar">
        <div class="Centered">
            <a href="/project/HompageFrame.php"><img src="Images/Logo.ico" class="Logo" alt="HuurEenHuis Logo"></a>
            <a class="Slogan" onclick="location = '/project/HompageFrame.php'"> HuurEenHuis: ieder huis een thuis</a>
        </div>
    </div>
    
    <div class="HouseInfoContainer">
        <div class="HouseInfoLeft">
            <img src="<?php echo 'HousePhotos/' . $House['AdId'] . '/1.jpg'; ?>" class="HousePhoto" id="Photo">
        </div>
        <form class="HouseInfoRight" method="post">
            <h1 class="HouseName">
            <?php if($IsOwner) { ?>
                <input type="text" value="<?php echo $House['HouseName']; ?>" name="houseName">
            <?php } else { ?>
                <?php echo $House['HouseName']; ?>
            <?php } ?>
            </h1>
            <div class="HouseDescription">
                <?php if($IsOwner) { ?>
                <textarea name="houseDescription"><?php echo $House['Description']; ?></textarea>
                <?php } else { ?>
                <?php echo $House['Description']; ?>
                <?php } ?>
            </div>
            <br>
            <table>
            <tr>
                <td>Prijs: </td>
                <td>
                    <?php if($IsOwner) { ?>
                        €<input type="text" value="<?php echo $House['Price']; ?>" name="price"> per maand
                    <?php } else { ?>
                        €<?php echo $House['Price']; ?>.- per maand
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td>Slaapkamers: </td>
                <td>
                    <?php if($IsOwner) { ?>
                        <input type="text" value="<?php echo $House['Bedrooms']; ?>" name="bedrooms">
                    <?php } else { ?>
                        <?php echo $House['Bedrooms']; ?>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td>Badkamers: </td>
                <td>
                    <?php if($IsOwner) { ?>
                        <input type="text" value="<?php echo $House['Bathrooms']; ?>" name="bathrooms">
                    <?php } else { ?>
                        <?php echo $House['Bathrooms']; ?>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td>Vierkante meter: </td>
                <td>
                    <?php if($IsOwner) { ?>
                        <input type="text" value="<?php echo $House['SquareFootage']; ?>" name="squareFootage">
                    <?php } else { ?>
                        <?php echo $House['SquareFootage']; ?>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td>Postcode: </td>
                <td>
                    <?php if($IsOwner) { ?>
                        <input type="text" pattern="^\d{4}\s?[A-Z]{2}$" value="<?php echo $House['ZipCode']; ?>" name="zipCode">
                    <?php } else { ?>
                        <?php echo $House['ZipCode']; ?>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td>Verkoper: </td>
                <td><?php echo $Owner['FirstName']." ".$Owner['LastName'].' ';
                if ($Owner['AccountType'] == "Admin"){
                    echo "<img class='Check' src='/project/images/Check.png'>";
                }
                ?></td>
            </tr>
            <tr>
                <td>Contact: </td>
                <td><?php echo $Owner['Email']; ?></td>
            </tr>
            <?php
            if (isset($AverageScore)) {
                echo '<tr>
                        <td>Gemiddelde score: </td>
                        <td>
                            ' . $AverageScore . ' van de 5
                        </td>
                    </tr>';
            }
            ?>
        </table>
        <?php
        if($IsOwner){
            echo '<input type="submit" name="Submit" value="Updaten">';
            echo '<a href="/project/DeleteAd.php?AdId='.$_GET["AdId"].'"><button type="button">Verwijder huis</button></a>';
        }
        ?>
    </form>
</div>
<script src="Notifications.js"></script>
<div class="Line"></div>
<a>Revieuws:</a>
<?php
$ReviewsPlaced = 0;
for ($i = 0; $i < count($Revieuws); $i++){
    $Revieuw = $Revieuws[$i];
    $ReviewerId = $Revieuw["RevieuwerId"];
    $sql = "SELECT * FROM Users WHERE UserID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $ReviewerId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $Revieuwer = mysqli_fetch_assoc($result);
    $HasEditPermissions = false;

    if (isset($_SESSION["UserId"])) {
        if ($_SESSION["UserId"] == $ReviewerId || $_SESSION["AccountType"] == "Admin") {
            $HasEditPermissions = true;
        }
    }
    

    if ($Revieuwer["IsBanned"] == 0){
        $ReviewsPlaced += 1;
        echo "<div class=Revieuw>";
        if ($Revieuwer["AccountType"] == "Admin") {
            echo "<a class='RevText'>".$Revieuwer["FirstName"]." ".$Revieuwer["LastName"]."</a><img class='Check' src='/project/images/Check.png'></img><a class='RevText'> (".$Revieuwer["Email"].")"." </a>";
        }else{
            echo "<a class='RevText'>".$Revieuwer["FirstName"]." ".$Revieuwer["LastName"]." (".$Revieuwer["Email"].")"." </a>";
        }
        echo "<br>";
        for ($s=0; $s < (int)$Revieuw["Stars"]; $s++){
            echo "<img class='Star' src='/project/images/star.png'></img>";
        }
        for ($s=(int)$Revieuw["Stars"]; $s < 5; $s++){
            echo "<img class='Star' src='/project/images/emptystar.png'></img>";
        }
        echo "<br><br>";
        echo "<a class='RevText'>".$Revieuw["Content"]."</a><br>";
        if ($HasEditPermissions) {
            echo "<br><a href='/project/RemoveRevieuw.php?AdId=".$_GET["AdId"]."&RevieuwId=" . $Revieuw["Id"] . "'>";
            echo "<button style='margin: 1vw;' class='button-style'>Verwijder</button></a>";
        }                   
        echo "<br></div>";
    }
}
if($ReviewsPlaced == 0){
    echo "<br><br>Nog geen reviews<br><br>";
}
if (isset($_SESSION["AccountType"])){
    if ($_SESSION["AccountType"] != "Seller"){
        echo "Schrijf revieuw:";
        echo "<form class=Revieuw method='post' action='/project/UploadRevieuw.php?AdId=".$_GET["AdId"]."'>";
        if ($_SESSION["AccountType"] == "Admin") {
            echo "<a class='RevText'>".$_SESSION["FirstName"]." ".$_SESSION["LastName"]."</a><img class='Check' src='/project/images/Check.png'></img><a class='RevText'> (".$_SESSION["Email"].")".": </a>";
        }else{
            echo "<a class='RevText'>".$_SESSION["FirstName"]." ".$_SESSION["LastName"]." (".$_SESSION["Email"].")".": </a>";
        }
        echo "<input type='number' name='Stars'  max='5' placeholder='sterren' style='width: 3.2vw;'>";
        echo "<br>";
        echo '<textarea style="margin-left: 1vw; width: 30vw;" name="Content" rows="4" placeholder="Schrijf review hier"></textarea>';
        echo "<br><input style='margin-left: 1vw;' type='submit'value='Upload'>";
        echo "</form>";
    }
}
?>
<script src="Ad.js"></script>
</body>
</html>
