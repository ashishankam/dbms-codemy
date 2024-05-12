<?php
    include("./mainInclude/header.php");
    session_destroy();
    header("Location:index.php");
    include("./mainInclude/header.php");
?>
