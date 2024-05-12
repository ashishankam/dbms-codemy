<?php
$db_host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "cdms";

// Create Connection
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

// Check Connections
if($conn->connect_error) {
 die("connection failed");
} 
// else {
//  echo"connected";
// }
?>