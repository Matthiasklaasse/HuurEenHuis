<?php
session_start();
session_unset();
session_destroy();
header("Location: /Project/HompageFrame.php?notification=Uitloggen gelukt!");
die();
?>