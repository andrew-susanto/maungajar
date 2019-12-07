<?php
$conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER, DB_PASS);
// set the PDO error mode to exception
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $conn->prepare("SELECT * FROM daftarkelas WHERE id='".$url[1]."'");
$stmt->execute();
$result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
$res=$stmt->fetchAll();
$kelas=$res[0];

?>
 <!-- Horizontal Steppers -->
 <div class="row">
  <div class="col-md-12">
    <!-- Stepers Wrapper -->
    <ul class="stepper stepper-horizontal">
      <!-- First Step -->
      <li class="completed">
        <a href="<?=BASEURL;?>/daftar">
          <span class="circle">1</span>
          <span class="label">Pilih Tutor</span>
        </a>
      </li>
      <!-- Second Step -->
      <li class="active">
        <a>
          <span class="circle">2</span>
          <span class="label">Konfirmasi</span>
        </a>
      </li>
      <!-- Third Step -->
      <li >
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
<?php if ($kelas['seat_available']>0 && $kelas['status_kelas']=='onprogress'){?>
<form action="registered" method="post">
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
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Konfirmasi</label>
        <div class="col-sm-10">
        <div class="custom-control custom-checkbox mb-3">
          <input type="checkbox" class="custom-control-input" id="customControlValidation1" required>
          <label class="custom-control-label" for="customControlValidation1" style="font-weight:500;">Saya akan hadir saat tutor berlangsung.</label>
          <div class="invalid-feedback">Kolom ini harus dicentang</div>
        </div>
        </div>
    </div>
    <input type="hidden" name="token_kelas" value="<?=$kelas['token']?>" hidden >
    <div class="form-group row">
        <div class="col-sm-10">
        <button type="submit" class="btn btn-primary btn-md form-button daftar-button">Daftar</button>
        </div>
    </div>
    </form>
<?php } elseif($kelas['status_kelas']=='done'){?>
  <div class="alert alert-danger" role="alert">
 Kelas Sudah Berakhir, Silahkan pilih kelas yang masih tersedia
</div>
<?php } else{?>
  <div class="alert alert-danger" role="alert">
 Kelas penuh, Silahkan pilih kelas yang masih tersedia
</div>
<?php }?>