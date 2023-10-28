<?php
ini_set('session.gc_maxlifetime', 604800);

$dbHost = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "huureenhuis.nltest";
$conn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbName);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST)) {
        die("No post data found");
    }

    print_r($_POST);

    $userName = mysqli_real_escape_string($conn, $_POST["UserName"]);
    $email = mysqli_real_escape_string($conn, $_POST["Email"]);
    $firstName = mysqli_real_escape_string($conn, $_POST["FirstName"]);
    $lastName = mysqli_real_escape_string($conn, $_POST["LastName"]);

    $accountTypeText = $_POST["AccountType"];
    
    if ($accountTypeText !== "Seller" && $accountTypeText !== "Buyer") {
        die("Invalid account type");
    }
    
    $passwordHash = hash('sha256', $_POST["Password"]);

    $sqlCheckUsername = 'SELECT * FROM `users` WHERE UserName = "' . $userName . '"';
    $result = mysqli_query($conn, $sqlCheckUsername);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            header("Location: /project/SignUp.html?notification=Sorry, deze naam bestaat al");
            die();
        }
    } else {
        echo "Error checking username: " . mysqli_error($conn);
    }

    $sql = "INSERT INTO Users (Username, Email, FirstName, LastName, AccountType, IsBanned, PasswordHash)
            VALUES (?, ?, ?, ?, ?, 0, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssss", $userName, $email, $firstName, $lastName, $accountTypeText, $passwordHash);
        if (mysqli_stmt_execute($stmt)) {
            echo "User data inserted successfully.";
            session_start();
            $_SESSION["SessionId"] = session_create_id("SESSION-");
            $_SESSION["UserId"] = mysqli_insert_id($conn);
            $_SESSION["UserName"] = $userName;
            $_SESSION["FirstName"] = $firstName;
            $_SESSION["LastName"] = $lastName;
            $_SESSION["Email"] = $email;
            $_SESSION["AccountType"] = $accountTypeText;
        } else {
            echo "Error inserting user data: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing insert statement: " . mysqli_error($conn);
    }
} else {
    echo "This page should be accessed via an HTTP POST request.";
}
header("Location: /Project/HompageFrame.php?notification=Account aangemaakt!");
die();
?>
