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

        $stmt = $conn->prepare("SELECT * FROM daftarkelas WHERE id_pengajar='".$_SESSION['mhs_id']."' ORDER BY id DESC");
        $stmt->execute();
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
        $daftarkelas=$stmt->fetchAll();

    }
    else{
        header("Location: ".BASEURL);
    }
}
else{
    header("Location: ".BASEURL);
}

$data['judul']='Histori Kelas Saya - mauNGajar';
require_once'template/header.php';
require_once'template/desktop-nav.php';
?>
<div class="container main-content">
    <h2>Histori Kelas Saya</h2>
    <div class="row" style="margin-top:10px">
        <?php foreach($daftarkelas as $kelas){?>
        <div class="col-sm-6 mt-4">
            <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?= $kelas['nama_kelas']?></h5>
                <p class="card-text">
                <?= $kelas['deskripsi_kelas']?><br><br>
                    Tanggal : <?= $kelas['tanggal']?><br>
                    Waktu : <?= $kelas['waktu']?><br>
                    Tempat : <?= $kelas['lokasi_kelas']?><br>
                    Pengajar : <?= $kelas['pengajar']?><br>
                    Status Kelas : <?php if($kelas['status_kelas']=='onprogress'){echo'<span class="status-normal">Akan Berlangsung</span>';} elseif($kelas['status_kelas']=='done'){echo'<span class="status-done">Telah Selesai</span>';}elseif($kelas['status_kelas']=='cancelled'){echo'<span class="status-cancelled">Tutor Dibatalkan</span>';}elseif($kelas['status_kelas']=='rejected'){echo'<span class="status-cancelled">Tutor Ditolak</span>';}elseif($kelas['status_kelas']=='onhold'){echo'<span class="status-done">Menunggu validasi tim akademis</span>';}?>
                </p>
        <?php if($kelas['status_kelas']!='rejected' && $kelas['status_kelas']!='onhold'){?><a href="<?= BASEURL;?>/detailkelas/<?=$kelas['id']?>" class="btn btn-primary">Detail Kelas</a><?php }?>
            </div>
            </div>
        </div>
        <?php } ?>
    </div>

</div>

<div class="mobile-nav">
  <a href="<?= BASEURL;?>/main"><span style="font-size:25px"><i class="fas fa-home"></i></span></a>
  <a href="<?= BASEURL;?>/daftar" ><span style="font-size:25px"><i class="fas fa-window-restore"></i></span></a>
  <a href="<?= BASEURL;?>/forum" ><span style="font-size:25px"><i class="fab fa-discourse"></i></span></a>
</div> 

<?php
require_once'template/footer.php';
?>
