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
        
        if(isset($_POST['npm'])){
            $stmt = $conn->prepare("UPDATE mahasiswa_google SET email='".$_POST['email']."' WHERE npm='".$_POST['npm']."'");
            $stmt->execute();
        }
        $stmt = $conn->prepare("SELECT * FROM mahasiswa_google");
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

$data['judul']='Ubah Email - mauNGajar';
require_once'template/header.php';
require_once'template/desktop-nav.php';
?>

<div class="container main-content">
    <h2>Email Google Mahasiswa</h2>
    <ul class="list-group">
        <?php foreach($res as $mahasiswa){ ?>
        <form action="<?=BASEURL;?>/changeemail" method="post">
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <div style="width:200px"><?= $mahasiswa['nama'] ?> <br><?=$mahasiswa['npm']?></div>
                <input type='hidden' name='npm' value='<?=$mahasiswa['npm']?>'>
                <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email" name="email" style="width:300px" value="<?=$mahasiswa['email']?>" >
                <button type="submit" class="btn btn-primary" style="width:100px">Edit</button>
        </li>
        </form>
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
