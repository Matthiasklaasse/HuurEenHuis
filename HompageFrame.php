<?php
include("SetupHomePage.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HuurEenHuis.nl (ieder huis een thuis)</title>
    <link rel="icon" type="image/x-icon" href="Images/Logo.ico">
    <link rel="stylesheet" href="GeneralStyle.css">
    <link rel="stylesheet" href="HomePage.css">
</head>
<body>
    <div id="NotificationsBar" class="NotificationsBar"></div>
    <div class="Topbar">
        <div class="Centered">
            <a href="/project/HompageFrame.php"><img src="Images/Logo.ico" class="Logo" alt="HuurEenHuis Logo"></a>
            <a class="Slogan" onclick="location = '/project/HompageFrame.php'">HuurEenHuis: ieder huis een thuis</a>
        </div>
    </div>

    <form class="Tabbar" method="post" id="SearchBar">
        <img src="Images/glass.png" class="TabMobile" onclick="document.getElementById('SearchBar').submit()">
        
        <?php
        if (!empty($_POST['Name'])) {
            echo '<input type="text" class="Search" id="Search" name="Name" value="'.$_POST['Name'].'">';
        } else {
            echo '<input type="text" class="Search" id="Search" placeholder="Zoeken" name="Name">';
        }
        ?>
        
        <div class='PriceFilterDiv'>
            <?php
            if (!empty($_POST['MinPrice'])) {
                echo '<input type="number" class="PriceFilter" name="MinPrice" value="'.$_POST['MinPrice'].'">';
            } else {
                echo '<input type="number" class="PriceFilter" name="MinPrice" placeholder="Min prijs">';
            }
            
            if (!empty($_POST['MaxPrice'])) {
                echo '<input type="number" class="PriceFilter" name="MaxPrice" value="'.$_POST['MaxPrice'].'">';
            } else {
                echo '<input type="number" class="PriceFilter" name="MaxPrice" placeholder="Max prijs">';
            }
            ?>
        </div>
        <div class='PriceFilterDiv'>
            Sorteren:
            <select id="sort" name="sort" class="PriceFilter">
                <option value="price_low_to_high" <?php echo (!isset($_POST['sort']) || $_POST['sort'] === 'price_low_to_high') ? 'selected' : ''; ?>>Prijs: Laag naar Hoog</option>
                <option value="price_high_to_low" <?php echo (!isset($_POST['sort']) || $_POST['sort'] === 'price_high_to_low') ? 'selected' : ''; ?>>Prijs: Hoog naar Laag</option>
                <option value="newest_first" <?php echo (!isset($_POST['sort']) || $_POST['sort'] === 'newest_first') ? 'selected' : ''; ?>>Nieuwste eerst</option>
                <option value="oldest_first" <?php echo (!isset($_POST['sort']) || $_POST['sort'] === 'oldest_first') ? 'selected' : ''; ?>>Oudste eerst</option>
                <option value="default" <?php echo (!isset($_POST['sort']) || $_POST['sort'] === 'default') ? 'selected' : ''; ?>>Standaard sorteren</option>
            </select>
        </div>
        <input type="submit" value="Search" hidden class="SearchButton">
        <div class="SearchBarImages">
            <img class="Tab" src="Images/Account.png" id="account">
        </div>
    </form>

    <div class="LoginBar" id="ManageAccount">
        <div class="Login">
            <?php
            if (!isset($_SESSION["SessionId"])) {
                echo '
                <form method="post" action="/project/Login.php">
                    <a class="LoginItems">Gebruikersnaam: </a>
                    <br> 
                    <input type="text" name="UserName" class="LoginItems">
                    <br>
                    <br>
                    <a class="LoginItems">Wachtwoord: </a> 
                    <br> 
                    <input type="password" name="Password" class="LoginItems">
                    <br>
                    <br>
                    <input type="submit" value="Login" class="LoginItems">
                    <br>
                </form>
                <br>
                <a class="LoginItems">Of: </a><button onclick="location=\'/project/SignUp.html\'">Maak Account</button>
                ';
            } else {
                echo '<div class="Option">
                <div class="Option"><a class="OptionText">'.$_SESSION["UserName"]."(".$_SESSION["FirstName"].")".'</a></div>
                </div>';
                echo '<div>
                <div class="Option" onclick="location=\'/project/ManageAccount.php\'"><a onclick="location=\'/project/ManageAccount.php\'" class="OptionText">Beheer account</a></div>
                </div>';
                if ($_SESSION["AccountType"] == "Admin" || $_SESSION["AccountType"] == "Seller"){
                    echo '<div>
                    <div class="Option" onclick="location=\'/project/Upload.html\'"><a class="OptionText" onclick="location=\'/project/Upload.php\'">Upload huis</a></div>
                    </div>';
                }
                if ($_SESSION["AccountType"] == "Admin"){
                    echo '<div>
                    <div class="Option" onclick="location=\'/project/AdminPanel.php\'"><a class="OptionText" onclick="location=\'/project/AdminPanel.php\'">Beheer website</a></div>
                    </div>';
                }
                echo '<div>
                <div class="Option" onclick="location=\'/project/Logout.php\'"><a class="OptionText" onclick="location=\'/project/Logout.php\'">Log uit</a></div>
                </div>';
            }
            ?>
        </div>

    </div>

    <div id="search-results">
        <?php
        if (empty($Houses)) {
            echo "Sorry, voor deze zoektermen zijn geen resultaten gevonden";
        } else {
            foreach ($Houses as $House) {
                echo '
                <div class="HouseAd" data-adid="' . $House['AdId'] . '">
                    <div class="HouseAdNameTag">' . $House['HouseName'] . '</div>
                    <img src="HousePhotos/' . $House['AdId'] . '/1.jpg" class="Adimg">
                    <div class="HouseAdPriceTag">€' . round($House['Price']) . ' per maand</div>
                </div>
                ';
            }
        }
        ?>
    </div>

    <script src="Homepage.js"></script>
    <script src="Notifications.js"></script>
</body>
</html>
