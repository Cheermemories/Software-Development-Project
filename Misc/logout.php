<?php
session_start();
session_destroy();
header("Location: Login/Boundary/Login.php");
exit();
?>