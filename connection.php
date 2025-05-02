<?php
$servername = "localhost";
$username = "u357529928_donationsys";
$password = "Oct.272001";
$dbname = "u357529928_donationsys";

try {
    
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    //hello
    echo "hello";
}
?>