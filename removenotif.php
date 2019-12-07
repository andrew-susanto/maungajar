<?php
require_once 'app/config.php';

$conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER, DB_PASS);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $conn->prepare("DELETE FROM notification WHERE mhs_id=:mhs_id");
$stmt->bindParam(':mhs_id', $_POST['id']);
$stmt->execute();