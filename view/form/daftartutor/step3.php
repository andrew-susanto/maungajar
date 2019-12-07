<?php
$conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER, DB_PASS);
// set the PDO error mode to exception
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $conn->prepare("SELECT * FROM pendaftaran_mhs WHERE id_mhs='".$_SESSION['mhs_id']."' AND id_kelas=".$url[1]);
$stmt->execute();
$result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
$count=$stmt->rowCount();
$pendaftaran = $stmt->fetchAll()[0];

$stmt = $conn->prepare("SELECT * FROM daftarkelas WHERE id='".$url[1]."'");
$stmt->execute();
$result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
$res=$stmt->fetchAll();
$kelas=$res[0];

$success=False;
if(isset($_POST['token_kelas'])){
  if($_POST['token_kelas']==$kelas['token'] && $kelas['seat_available']>0 && $count==0 && $kelas['id_pengajar']!=$_SESSION['mhs_id']){
  $stmt = $conn->prepare("UPDATE daftarkelas SET seat_available=".($kelas['seat_available']-1)." WHERE id=".$url[1]);
  $stmt->execute();
  $stmtinsert = $conn->prepare("INSERT INTO pendaftaran_mhs (id_mhs,id_kelas,tanggal_pendaftaran,status_tutor) VALUES ('".$_SESSION['mhs_id']."', '".$url[1]."', '".date("j F Y H:i:s")."', 'onprogress')");
  $stmtinsert->execute();
  $success=True;
}}
?>
 <!-- Horizontal Steppers -->
 <div class="row">
  <div class="col-md-12">
    <!-- Stepers Wrapper -->
    <ul class="stepper stepper-horizontal">
      <!-- First Step -->
      <li class="completed">
        <a href="<?=BASEURL;?>/daftar/">
          <span class="circle">1</span>
          <span class="label">Pilih Tutor</span>
        </a>
      </li>
      <!-- Second Step -->
      <li class="completed">
        <a href="<?=BASEURL;?>/daftar/<?=$url[1];?>/">
          <span class="circle">2</span>
          <span class="label">Konfirmasi</span>
        </a>
      </li>
      <!-- Third Step -->
      <li class="active">
        <a>
          <span class="circle">3</span>
          <span class="label">Selesai</span>
        </a>
      </li>
    </ul>
    <!-- /.Stepers Wrapper -->
  </div>
</div>
<!-- /.Horizontal Steppers -->
<div class="container">
<?php if ($success){?>
  <h4>Pendaftaran Tutor Anda Berhasil ! </h4>
  <a href="<?= BASEURL;?>/detailkelas/<?=$url[1]?>" class="btn btn-primary">Buka Detail Kelas</a>
  <form>
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
        <?= $kelas['pengajar']?>
        </div>
        </div>
    </div>
<?php } elseif ($count>0 && $pendaftaran['status_tutor']=='onprogress'){ ?>
  <h4>Anda sudah terdaftar di kelas ini ! </h4>
<?php } elseif ($count>0 && $pendaftaran['status_tutor']=='user_cancel'){ ?>
  <h4>Anda tidak dapat mendaftar ke tutor yang sudah pernah dibatalkan. Silahkan pilih kelas lain </h4>
<?php } elseif ($kelas['seat_available']==0){ ?>
  <div class="alert alert-danger" role="alert">
  Kelas Penuh ! Silahkan pilih kelas yang masih tersedia
</div>
<?php }elseif ($kelas['id_pengajar']==$_SESSION['mhs_id']){ ?>
  <div class="alert alert-danger" role="alert">
  Anda tidak dapat mendaftar di kelas Anda sendiri
</div>
<?php }else{
  header("Location:".BASEURL."/daftar");
  }?>
</div>