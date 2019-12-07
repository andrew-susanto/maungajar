<?php
$conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER, DB_PASS);
// set the PDO error mode to exception
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $conn->prepare("SELECT * FROM daftarkelas WHERE seat_available>0 AND status_kelas='onprogress' ORDER BY tanggal ASC");
$stmt->execute();
$result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
$daftarkelastersediadate=$stmt->fetchAll();

$stmt = $conn->prepare("SELECT * FROM daftarkelas WHERE seat_available>0 AND status_kelas='onprogress' ORDER BY nama_kelas ASC");
$stmt->execute();
$result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
$daftarkelastersediacourse=$stmt->fetchAll();

$stmt = $conn->prepare("SELECT * FROM pendaftaran_mhs WHERE id_mhs='".$_SESSION['mhs_id']."' AND status_tutor='onprogress'");
$stmt->execute();
$result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
$terdaftar=$stmt->fetchAll();

$kelas_terdaftar=array();
foreach($terdaftar as $dataterdaftar){
  array_push($kelas_terdaftar,$dataterdaftar['id_kelas']);
}

if(strtotime('now')<strtotime('+ 9 hours',strtotime('sunday this week'))){
  $closetime = strtotime('+ 24 hours', strtotime( 'sunday this week'));
}
else{
  $closetime = strtotime('+ 24 hours',strtotime( 'next sunday' ));
}
?>
 <!-- Horizontal Steppers -->
 <div class="row">
  <div class="col-md-12">
    <!-- Stepers Wrapper -->
    <ul class="stepper stepper-horizontal">
      <!-- First Step -->
      <li class="active">
        <a>
          <span class="circle">1</span>
          <span class="label">Pilih Tutor</span>
        </a>
      </li>
      <!-- Second Step -->
      <li>
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
<div class="container">
    <h4>Pilih Tutor yang Diinginkan</h4>
    <small>Pendaftaran untuk tutor minggu depan dapat mulai dilakukan pada hari minggu jam 00:00 WIB</small>
    <br>
    <form class="form-inline" style="margin-top:20px;">
      Pilih Berdasarkan : 
      <select style="margin-left:10px;" class="form-control" id="sortby">
        <option value="date">Tanggal Tutor</option>
        <option value="course">Mata Kuliah</option>
      </select>
    </form>
    <div id="sortbydate" class="sortcourse">
      <div><div>
        <?php 
        $date = '';
        foreach($daftarkelastersediadate as $kelas){ 
            if(!in_array($kelas['id'],$kelas_terdaftar)){
              if(strtotime($kelas['tanggal'].' '.$kelas['waktu'])>strtotime('now') && strtotime($kelas['tanggal'].' '.$kelas['waktu'])<=$closetime){
                if($kelas['tanggal']!=$date){ ?>
        </div></div>
        <div class="item-course-list">
        <h5><?=date('l, j F Y',strtotime($kelas['tanggal']));?></h5>
        <hr>
        <div class="row">
        <?php $date = $kelas['tanggal'];
        } ?>
        <div class="col-sm-6">
            <div class="card">
              <div class="card-body">
                  <h5 class="card-title"><?= $kelas['nama_kelas']?></h5>
                  <p class="card-text">
                      <?= $kelas['deskripsi_kelas']?><br><br>
                      Tanggal : <?= $kelas['tanggal']?><br>
                      Waktu : <?= $kelas['waktu']?><br>
                      Tempat : <?= $kelas['lokasi_kelas']?><br>
                      Pengajar : <?= $kelas['pengajar']?><br>
                      Kursi Tersedia : <?=$kelas['seat_available']?>
                  </p>
                  <a href="<?=BASEURL;?>/daftar/<?=$kelas['id']?>/" class="btn btn-primary daftar-button">Pilih Tutor</a>
              </div>
            </div>
        </div>
        <?php }}}?>  
      </div>
    </div>
    </div>

    <div id="sortbycourse" style="display:none" class="sortcourse">
        <div><div>
        <?php 
        $namakelas = '';
        foreach($daftarkelastersediacourse as $kelas){ 
            if(!in_array($kelas['id'],$kelas_terdaftar)){
              if(strtotime($kelas['tanggal'].' '.$kelas['waktu'])>strtotime('now') && strtotime($kelas['tanggal'].' '.$kelas['waktu'])<=$closetime){
                if($kelas['nama_kelas']!=$namakelas){ ?>
        </div></div>
        <div class="item-course-list">
        <h5><?=$kelas['nama_kelas'];?></h5>
        <hr>
        <div class="row">
        <?php $namakelas = $kelas['nama_kelas'];
        } ?>
        <div class="col-sm-6">
            <div class="card">
              <div class="card-body">
                  <h5 class="card-title"><?= $kelas['nama_kelas']?></h5>
                  <p class="card-text">
                      <?= $kelas['deskripsi_kelas']?><br><br>
                      Tanggal : <?= $kelas['tanggal']?><br>
                      Waktu : <?= $kelas['waktu']?><br>
                      Tempat : <?= $kelas['lokasi_kelas']?><br>
                      Pengajar : <?= $kelas['pengajar']?><br>
                      Kursi Tersedia : <?=$kelas['seat_available']?>
                  </p>
                  <a href="<?=BASEURL;?>/daftar/<?=$kelas['id']?>/" class="btn btn-primary daftar-button">Pilih Tutor</a>
              </div>
            </div>
        </div>
        <?php }}}?>  
      </div>
    </div>
    </div>
</div>
<script>
var inputselect = document.getElementById('sortby');
var coursedate = document.getElementById('sortbydate');
var coursetype = document.getElementById('sortbycourse');
inputselect.addEventListener('change',function(){
  if(inputselect.value=='date'){
    coursedate.style.display = 'block';
    coursetype.style.display = 'none';
  }
  else if(inputselect.value=='course'){
    coursedate.style.display = 'none';
    coursetype.style.display = 'block';
  }
  }
);
</script>