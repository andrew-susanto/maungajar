<?php
require_once 'app/config.php';
header("Content-Type: application/json; charset=UTF-8");

$room_token=$_POST['token'];

$conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER, DB_PASS);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $conn->prepare("SELECT * FROM chat WHERE kelas_id=:token AND unix_time>=".(time()-180));
$stmt->bindParam(':token', $room_token);
$stmt->execute();
$result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
$res=$stmt->fetchAll();

echo json_encode($res);