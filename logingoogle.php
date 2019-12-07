<?php
require_once 'app/config.php';
session_start();
    $google_auth_url="https://oauth2.googleapis.com/tokeninfo?id_token=".$_POST['idtoken'];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL,$google_auth_url);
    $result_auth=curl_exec($ch);
    curl_close($ch);
	$google = json_decode($result_auth);
	if ($google->aud =='945454987119-iov8btgckp4tectboeph2fut2n90ghq1.apps.googleusercontent.com'){
		$conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER, DB_PASS);
		// set the PDO error mode to exception
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt = $conn->prepare("SELECT * FROM mahasiswa_google WHERE email='".$google->email."'");
		$stmt->execute();
		if($stmt->rowCount()==1){
			$stmt->setFetchMode(PDO::FETCH_ASSOC); 
			$res=$stmt->fetchAll();
			$stmtacc = $conn->prepare("SELECT * FROM mahasiswa WHERE npm='".$res[0]['npm']."'");
			$stmtacc->execute();
			if($stmtacc->rowCount()==1){
				// set the resulting array to associative
				$stmtacc->setFetchMode(PDO::FETCH_ASSOC); 
				$resacc=$stmtacc->fetchAll();
				$_SESSION["mhs_id"] = $resacc[0]['id'];
			}
			if($stmtacc->rowCount()==0){
				$id=substr(md5(uniqid()),0,10);
				$stmtinsert = $conn->prepare("INSERT INTO mahasiswa (id, nama, npm, jurusan, ed_program, line_id, batal_kelas, banned, role) VALUES ('".$id."', '".$res[0]['nama']."', '".$res[0]['npm']."','".$res[0]['jurusan']."','','not_registered',0,0,'mahasiswa')");
				$stmtinsert->execute();
				$_SESSION["mhs_id"] = $id;
			}
			echo'Signed in';
		}
		else{
			echo "Can't Login, Email Not Registered";
		}
	}

?>