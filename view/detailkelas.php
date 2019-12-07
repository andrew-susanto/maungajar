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

        $stmt = $conn->prepare("SELECT * FROM pendaftaran_mhs WHERE id_mhs='".$_SESSION['mhs_id']."' AND id_kelas=".$url[1]);
        $stmt->execute();
        $count=$stmt->rowCount();
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
        $pendaftaran=$stmt->fetchAll();

        $stmt = $conn->prepare("SELECT * FROM daftarkelas WHERE id=".$url[1]);
        $stmt->execute();
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
        $daftarkelas=$stmt->fetchAll();
        
        $stmt = $conn->prepare("SELECT * FROM mahasiswa WHERE id='".$daftarkelas[0]['id_pengajar']."'");
        $stmt->execute();
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
        $pengajar=$stmt->fetchAll();
        $idlinepengajar=$pengajar[0]['line_id'];

        if($daftarkelas[0]['id_pengajar']==$_SESSION['mhs_id'] && $daftarkelas[0]['status_kelas']!='rejected' && $daftarkelas[0]['status_kelas']!='onhold'){
            $kelas=$daftarkelas[0];
        }
        elseif($count>0 && $pendaftaran[0]['status_tutor']!='user_cancel'){
            $kelas=$daftarkelas[0];
        }
        else{
            header("Location: ".BASEURL."/main");
        }
    }
    else{
        header("Location: ".BASEURL);
    }
}
else{
    header("Location: ".BASEURL);
}

$data['judul']='Detail Kelas '.$kelas["nama_kelas"].' - mauNGajar';
require_once'template/header.php';
require_once'template/desktop-nav.php';
?>

<div class="container main-content">
    <h2>Detail Kelas</h2>
    <div class="form-group row mt-4">
        <label class="col-sm-2 col-form-label">Nama Tutor</label>
        <div class="col-sm-10">
        <div class="md-form mt-2">
        <?= $kelas['nama_kelas']?>
        </div>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Deskripsi Tutor</label>
        <div class="col-sm-10">
        <div class="md-form mt-2">
        <?= $kelas['deskripsi_kelas']?>
        </div>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Lokasi Tutor</label>
        <div class="col-sm-10">
        <div class="md-form mt-2">
        <?= $kelas['lokasi_kelas']?>
        </div>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Tanggal Tutor</label>
        <div class="col-sm-10">
        <div class="md-form mt-2">
        <?= $kelas['tanggal']?>
        </div>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Waktu Tutor</label>
        <div class="col-sm-10">
        <div class="md-form mt-2">
        <?= $kelas['waktu']?>
        </div>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Pengajar Tutor</label>
        <div class="col-sm-10">
        <div class="md-form mt-2">
        <?= $kelas['pengajar']?><br> <a href="https://line.me/R/ti/p/~<?= $idlinepengajar?>">Chat Pengajar</a>
        </div>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Status Kelas</label>
        <div class="col-sm-10">
        <div class="md-form mt-2">

        <?php if($kelas['status_kelas']=='onprogress'){echo'<p class="status-normal">Akan Berlangsung</p>';} elseif($kelas['status_kelas']=='done'){echo'<p class="status-done">Telah Selesai</p>';}elseif($kelas['status_kelas']=='cancelled'){echo'<p class="status-cancelled">Tutor Dibatalkan</p>';}elseif($kelas['status_kelas']=='rejected'){echo'<p class="status-cancelled">Tutor Ditolak</p>';}elseif($kelas['status_kelas']=='onhold'){echo'<p class="status-done">Menunggu persetujuan tim akademis</p>';}?>
        </div>
        </div>
    </div>
    
    <h2>Tutor Group Chat</h2>
    <iframe class="iframe-chat" src="<?=BASEURL;?>/chat/<?=$kelas['token']?>"></iframe>

<?php 
if($daftarkelas[0]['id_pengajar']==$_SESSION['mhs_id']){
    $stmt = $conn->prepare("SELECT * FROM pendaftaran_mhs WHERE id_kelas=".$url[1]);
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
    $anggota=$stmt->fetchAll();

    if(isset($url[2]) && !isset($url[3])){
        if($url[2]=='selesai'){
            $stmt = $conn->prepare("UPDATE daftarkelas SET status_kelas='done', last_updated='".time()."' WHERE id='".$url[1]."'");
            $stmt->execute();
            $stmt = $conn->prepare("UPDATE pendaftaran_mhs SET status_tutor='done',  last_updated='".time()."' WHERE id_kelas='".$url[1]."'");
            $stmt->execute();
        }
        if($url[2]=='batalkan'){
            $stmt = $conn->prepare("UPDATE daftarkelas SET status_kelas='cancelled', last_updated='".time()."' WHERE id='".$url[1]."'");
            $stmt->execute();
            $stmt = $conn->prepare("UPDATE pendaftaran_mhs SET status_tutor='cancelled',  last_updated='".time()."' WHERE id_kelas='".$url[1]."'");
            $stmt->execute();
            foreach($anggota as $anggotakelas){
                $stmt = $conn->prepare("INSERT INTO notification (time,mhs_id,content) VALUES ('".time()."','".$anggotakelas['id_mhs']."','Kelas Tutor ".$kelas['nama_kelas']." Dibatalkan')");
                $stmt->execute();
            }
        }
        if($url[2]=='berlangsung'){
            $stmt = $conn->prepare("UPDATE daftarkelas SET status_kelas='onprogress', last_updated='".time()."' WHERE id='".$url[1]."'");
            $stmt->execute();
            $stmt = $conn->prepare("UPDATE pendaftaran_mhs SET status_tutor='onprogress',  last_updated='".time()."' WHERE id_kelas='".$url[1]."'");
            $stmt->execute();
            foreach($anggota as $anggotakelas){
                $stmt = $conn->prepare("INSERT INTO notification (time,mhs_id,content) VALUES ('".time()."','".$anggotakelas['id_mhs']."','Kelas Tutor ".$kelas['nama_kelas']." Diadakan Kembali')");
                $stmt->execute();
            }
        }
    }
    ?>

    <br><br>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Kursi Tersedia</label>
        <div class="col-sm-10">
        <div class="md-form mt-2">
        <p><?=$kelas['seat_available']?></p>
        </div>
        </div>
    </div>

<?php
    $id_peserta=array();

    foreach($anggota as $dataanggota){
        array_push($id_peserta,$dataanggota['id_mhs']);
    }

    $orstmt= implode("' OR id='",$id_peserta);
    $stmt = $conn->prepare("SELECT * FROM mahasiswa WHERE (id='".$orstmt."')");
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
    $mahasiswa_peserta=$stmt->fetchAll();
?>

    <h2>Peserta Tutor</h2>
    <ul class="list-group">
        <?php foreach($mahasiswa_peserta as $datamahasiswa){ ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <div class="col-6">
                <?= $datamahasiswa['nama'] ?>
            </div>
            <div class="col-2">
                <a href="https://line.me/R/ti/p/~<?=$datamahasiswa['line_id']?>">Chat Via LINE </a>
            </div>
        </li>
        <?php }?>
    </ul>

        <br><br>
        <button type="button" class="btn btn-danger btn-md form-button" onclick="window.location.href='<?=BASEURL;?>/detailkelas/<?=$url[1]?>/batalkan'">Batalkan Kelas</button>
        <button type="button" class="btn btn-success btn-md form-button" onclick="window.location.href='<?=BASEURL;?>/detailkelas/<?=$url[1]?>/berlangsung'">Kelas Akan Berlangsung</button>
        <button type="button" class="btn btn-warning btn-md form-button" onclick="window.location.href='<?=BASEURL;?>/detailkelas/<?=$url[1]?>/selesai'">Kelas Selesai</button>
   <?php }?>

<?php if($daftarkelas[0]['id_pengajar']!=$_SESSION['mhs_id']){  ?>
    <form action="<?=BASEURL;?>/historitutor" method='post'>
        <input type="hidden" name="kelas" value="<?=$url[1]?>">
        <input type="hidden" name="action" value="cancel">
    <button type="submit" class="btn btn-danger btn-md form-button">Batal Ikut Kelas</button>
    </form>
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
