<?php
// logout.php - Destroy Session
session_start();
session_destroy();
header('Location: index.php');
exit();
?>