<?php
$conn = mysqli_connect('localhost', 'root', '', 'books');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Database Connected Successfully!";
?>
