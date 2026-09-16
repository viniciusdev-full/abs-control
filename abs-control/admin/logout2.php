<?php

session_start();

$_SESSION = [];

session_destroy();

header("Location:logout2.php");

exit();

?>