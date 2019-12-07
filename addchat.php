<?php 

require_once 'app/config.php';

$mhs_id=$_POST['id'];
$chat=htmlspecialchars($_POST['chat']);
$room_token=$_POST['token'];
$mhs_name=$_POST['nama_mahasiswa'];

// Setup Databse Connection
$conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER, DB_PASS);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check Name Validity with mhs_id
$stmt = $conn->prepare("SELECT * FROM mahasiswa WHERE id='".$mhs_id."'");
$stmt->execute();
$result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
$res=$stmt->fetchAll();
$data_mhs=$res[0];

if($mhs_name==$data_mhs['nama']){
	$stmt = $conn->prepare("INSERT INTO chat (kelas_id,mhs_id,mhs_name,chat,unix_time,time) VALUES ('".$room_token."','".$mhs_id."','".$mhs_name."',:chat,'".time()."','".date('H:i')."')");
	 $stmt->bindParam(':chat', $chat);
	$stmt->execute();
 }
?>