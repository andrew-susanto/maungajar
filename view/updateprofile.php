<?php

session_start();
if(isset($_SESSION['mhs_id'])){
    $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER, DB_PASS);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if(isset($_POST['line_id'])){
        $stmt = $conn->prepare("UPDATE mahasiswa SET line_id=:line_id WHERE id='".$_SESSION['mhs_id']."'");
        $stmt->bindParam(':line_id', $line_id);
        $line_id = strip_tags($_POST['line_id']);
        $stmt->execute();
    }
    $stmt = $conn->prepare("SELECT * FROM mahasiswa WHERE id='".$_SESSION['mhs_id']."'");
    $stmt->execute();
    if($stmt->rowCount()==1){
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
        $res=$stmt->fetchAll();
        $data_mhs=$res[0];
        if($data_mhs['line_id']=="not_registered"){
            $data_mhs['line_id']="";
        }
    }

    else{
        header("Location: ".BASEURL);
    }
}
else{
    header("Location: ".BASEURL);
}

$data['judul']='Update Profile - mauNGajar';
$data['page']='updateprofile';
require_once'template/header.php';
require_once'template/desktop-nav.php';

?>
<div class="container main-content">
    <h2>Update Profile</h2>
    <form action="updateprofile" method="post">
    <div class="form-group row mt-4">
        <label for="inputname" class="col-sm-2 col-form-label">Nama</label>
        <div class="col-sm-10">
        <div class="md-form mt-2">
            <?= $data_mhs['nama']?>
        </div>
        </div>
    </div>
    <div class="form-group row">
        <label for="inputnpm" class="col-sm-2 col-form-label">NPM</label>
        <div class="col-sm-10">
        <div class="md-form mt-2">
            <?= $data_mhs['npm']?>
        </div>
        </div>
    </div>

    <div class="form-group row">
        <label for="inputjurusan" class="col-sm-2 col-form-label">Jurusan</label>
        <div class="col-sm-10">
        <div class="md-form mt-2">
            <?= $data_mhs['jurusan']?>
        </div>
        </div>
    </div>

    <div class="form-group row">
    <label for="inputlineid" class="col-sm-2 col-form-label">LINE ID</label>
    <div class="col-sm-10">
      <div class="md-form mt-0">
        <input type="text" name="line_id" class="form-control" id="inputlineid" placeholder="LINE ID" value="<?= $data_mhs['line_id']?>">
      </div>
    </div>
  </div>

    <div class="form-group row">
        <div class="col-sm-10">
        <button type="submit" class="btn btn-primary btn-md form-button">Update</button>
        </div>
    </div>
    </form>


</div>
<div class="mobile-nav">
  <a href="<?= BASEURL;?>/main"><span style="font-size:25px"><i class="fas fa-home"></i></span></a>
  <a href="<?= BASEURL;?>/daftar" ><span style="font-size:25px"><i class="fas fa-window-restore"></i></span></a>
  <a href="<?= BASEURL;?>/forum" ><span style="font-size:25px"><i class="fab fa-discourse"></i></span></a>
</div> 
<?php
require_once'template/footer.php';
?>
