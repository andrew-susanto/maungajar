<?php

session_start();
if(isset($_SESSION['mhs_id'])){
    $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER, DB_PASS);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT * FROM mahasiswa WHERE id='".$_SESSION['mhs_id']."'");
    $stmt->execute();
    if($stmt->rowCount()==1){
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
        $res=$stmt->fetchAll();
        $data_mhs=$res[0];
        if($data_mhs['line_id']=="not_registered"){
            header("Location: ".BASEURL."/updateprofile");
        }        
        if($data_mhs['role']!='admin'){
            header("Location: ".BASEURL."/main");
        }
        
        if(isset($url[1])){
            $stmt = $conn->prepare("UPDATE mahasiswa SET role='".$url[2]."' WHERE id='".$url[1]."'");
            $stmt->execute();
            $stmt = $conn->prepare("SELECT * FROM mahasiswa WHERE id='".$_SESSION['mhs_id']."'");
            $stmt->execute();
            $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
            $res=$stmt->fetchAll();
        }
        $stmt = $conn->prepare("SELECT * FROM mahasiswa");
        $stmt->execute();
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
        $res=$stmt->fetchAll();
    }
    else{
        header("Location: ".BASEURL);
    }
}
else{
    header("Location: ".BASEURL);
}

$data['judul']='Ubah Role - mauNGajar';
require_once'template/header.php';
require_once'template/desktop-nav.php';
?>


<div class="container main-content">
    <h2>Role Mahasiswa</h2>
    <ul class="list-group">
        <?php foreach($res as $mahasiswa){ ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <?= $mahasiswa['nama'] ?> (<?=$mahasiswa['npm']?>) <br>
            Role : 
                <?php if($mahasiswa['role']=='tutor'){
                    echo'koordinator';
                }else{
                    echo $mahasiswa['role'];
                }
            ?>
            <div>
            <button type="button" class="btn btn-warning btn-md form-button" onclick="window.location.href='<?=BASEURL;?>/changerole/<?=$mahasiswa['id']?>/mahasiswa'">Mahasiswa</button>
            <button type="button" class="btn btn-primary btn-md form-button" onclick="window.location.href='<?=BASEURL;?>/changerole/<?=$mahasiswa['id']?>/tutor'">Koordinator</button>
        </div>
        </li>
    </ul>
        <?php }?>

        

</div>

<div class="mobile-nav">
  <a href="<?= BASEURL;?>/main"><span style="font-size:25px"><i class="fas fa-home"></i></span></a>
  <a href="<?= BASEURL;?>/daftar" ><span style="font-size:25px"><i class="fas fa-window-restore"></i></span></a>
  <a href="<?= BASEURL;?>/forum" ><span style="font-size:25px"><i class="fab fa-discourse"></i></span></a>
</div> 
<?php
require_once'template/footer.php';
?>
