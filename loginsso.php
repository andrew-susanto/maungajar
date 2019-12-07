<?php

// Include the dependencies
require "login/SSO/SSO.php";
require_once 'app/config.php';

$cas_path = "login/vendor/CAS.php";
SSO\SSO::setCASPath($cas_path);

if(SSO\SSO::check()){
	$user = SSO\SSO::getUser();
	if(substr($user->org_code,6)=="12.01" && substr($user->npm,0,2)=="19"){
		try {
			$conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER, DB_PASS);
			// set the PDO error mode to exception
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$stmt = $conn->prepare("SELECT * FROM mahasiswa WHERE npm='".$user->npm."'");
			$stmt->execute();
			session_start();
			if($stmt->rowCount()==1){
				// set the resulting array to associative
				$result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
				$res=$stmt->fetchAll();
				if(res[0]['username']!=$user->username){
					$stmtupdate = $conn->prepare("UPDATE mahasiswa SET username=:username, nama=:nama, npm=:npm, jurusan=:jurusan, ed_program=:edprogram WHERE npm=:npm");
					$stmtupdate->bindParam(':username',$user->username);
					$stmtupdate->bindParam(':nama',$user->name);
					$stmtupdate->bindParam(':npm',$user->npm);
					$stmtupdate->bindParam(':jurusan',$user->study_program);
					$stmtupdate->bindParam(':edprogram',$user->educational_program);
					$stmtupdate->bindParam(':npm',$user->npm);
					$stmtupdate->execute();
				}
				$_SESSION["mhs_id"] = $res[0]['id'];
			}
			if($stmt->rowCount()==0){
				$id=substr(md5(uniqid()),0,10);
				$stmtinsert = $conn->prepare("INSERT INTO mahasiswa (id, username, nama, npm, jurusan, ed_program, line_id, batal_kelas, banned, role) VALUES (:id,:username, :nama, :npm,:jurusan, :edprogram,'not_registered',0,0,'mahasiswa')");
				$stmtinsert->bindParam(':id',$id);
				$stmtinsert->bindParam(':username',$user->username);
				$stmtinsert->bindParam(':nama',$user->name);
				$stmtinsert->bindParam(':npm',$user->npm);
				$stmtinsert->bindParam(':jurusan',$user->study_program);
				$stmtinsert->bindParam(':edprogram',$user->educational_program);
				$stmtinsert->bindParam(':npm',$user->npm);
				$stmtinsert->execute();
				$_SESSION["mhs_id"] = $id;
			}
			header("Location: ".BASEURL."/main");
			}
		catch(PDOException $e)
			{
			echo "Connection failed: " . $e->getMessage();
			}
	}
	else{
		header("Location: ".BASEURL);
	}
	
}
// Authenticate the user
SSO\SSO::authenticate();