<?php
$host = 'localhost';
$dbname = 'dbdgoybkbcahsz';
$username = 'uxgukysg8xcbd';
$password = '6imcip8yfmic';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>
