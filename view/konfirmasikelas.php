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

        if($data_mhs['role']!='admin' && $data_mhs['role']!='tutor'){
            header("Location: ".BASEURL."/main");
        }

        if(isset($_POST['token_kelas'])){
            if($_POST['action']=='terima'){
                $stmt = $conn->prepare("UPDATE daftarkelas SET status_kelas='onprogress' WHERE token='".$_POST['token_kelas']."'");
                $stmt->execute();
            }
            elseif($_POST['action']=='tolak'){
                $stmt = $conn->prepare("UPDATE daftarkelas SET status_kelas='rejected' WHERE token='".$_POST['token_kelas']."'");
                $stmt->execute();
            }
            
        }

        $stmt = $conn->prepare("SELECT * FROM daftarkelas WHERE status_kelas='onhold' ");
        $stmt->execute();
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
        $daftarkelas=$stmt->fetchAll();

        $stmt = $conn->prepare("SELECT * FROM mahasiswa");
        $stmt->execute();
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
        $mahasiswa=$stmt->fetchAll();

        foreach($mahasiswa as $datamahasiswa){
            $arrayMahasiswa[$datamahasiswa['id']] = $datamahasiswa;
        }

    }
    else{
        header("Location: ".BASEURL);
    }
}
else{
    header("Location: ".BASEURL);

}

$data['judul']='Konfirmasi Kelas Baru - mauNGajar';
require_once'template/header.php';
require_once'template/desktop-nav.php';
?>

<div class="container main-content">
    <h2>Konfirmasi Kelas Baru</h2>
    <ul class="list-group">
        <?php foreach($daftarkelas as $kelas){ ?>
        <li class="list-group-item d-flex align-items-center konfirmasilist">
            <div class="konfirmasicontent"><span style="font-weight:600"><?= $kelas['nama_kelas'] ?></span><br>
            Deskripsi : <?=$kelas['deskripsi_kelas']?><br><br>
            Pengajar : <?=$kelas['pengajar']?>   <a href="https://line.me/R/ti/p/~<?=$arrayMahasiswa[$kelas['id_pengajar']]['line_id']?>">Chat Via LINE </a><br>
            Tempat : <?= $kelas['lokasi_kelas'] ?><br>
            Waktu : <?=$kelas['tanggal'];?> <?=$kelas['waktu']?>
            </div>
            <form action="<?=BASEURL;?>/konfirmasikelas" method="post">
                <input type='hidden' name='id' value='<?=$kelas['id']?>'>
                <input type='hidden' name='token_kelas' value='<?=$kelas['token']?>'>
                <input type="hidden" name='action' value='tolak'>
                <button type="submit" class="btn btn-danger" style="width:200px">Tolak Tutor</button>
            </form>
            <form action="<?=BASEURL;?>/konfirmasikelas" method="post">
                <input type='hidden' name='id' value='<?=$kelas['id']?>'>
                <input type='hidden' name='token_kelas' value='<?=$kelas['token']?>'>
                <input type="hidden" name='action' value='terima'>
                <button type="submit" class="btn btn-success" style="width:200px">Terima Tutor</button>
            </form>
        </li>
        <?php }?>
    </ul>
</div>
<div class="mobile-nav">
  <a href="<?= BASEURL;?>/main"><span style="font-size:25px"><i class="fas fa-home"></i></span></a>
  <a href="<?= BASEURL;?>/daftar" ><span style="font-size:25px"><i class="fas fa-window-restore"></i></span></a>
  <a href="<?= BASEURL;?>/forum" ><span style="font-size:25px"><i class="fab fa-discourse"></i></span></a>
</div> 
<?php
require_once'template/footer.php';
?>
