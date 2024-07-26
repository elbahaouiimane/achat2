<?php
session_start();
require_once('config.php');

session_unset();
session_destroy();
header("Location: login_page.php");
exit();
?>
