<?php
$conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER, DB_PASS);
// set the PDO error mode to exception
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$opendate = date( 'Y-m-d', strtotime('+ 7 days',strtotime( 'monday this week' )));
$closedate = date( 'Y-m-d', strtotime('+ 7 days',strtotime( 'sunday this week' )));

if(isset($_POST['nama-kelas'])){
  var_dump(strtotime($_POST['tanggal-kelas']));
  if(strtotime($_POST['tanggal-kelas']) <= strtotime($closedate) && strtotime($_POST['tanggal-kelas']) >= strtotime($opendate) && ($_POST['nama-kelas']=='DDP 1' || $_POST['nama-kelas']=='MD-1' || $_POST['nama-kelas']=='MatDas-1' || $_POST['nama-kelas']=='FisDas' || $_POST['nama-kelas']=='PSD (KI)') ){
    $stmt = $conn->prepare("INSERT INTO daftarkelas (token, nama_kelas, deskripsi_kelas, lokasi_kelas, tanggal, waktu, pengajar, id_pengajar,  seat_available, status_kelas, last_updated) VALUES ('".substr(md5(uniqid()),0,10)."', :nama_kelas, :deskripsi_kelas, :lokasi_kelas, :tanggal, :waktu, :pengajar, :id_pengajar,  :seat_available, 'onhold',0)");
    $stmt->bindParam(':nama_kelas', $namakelas);
    $stmt->bindParam(':deskripsi_kelas', $deskripsi_kelas);
    $stmt->bindParam(':lokasi_kelas', $lokasi_kelas);
    $stmt->bindParam(':tanggal', $tanggal_kelas);
    $stmt->bindParam(':waktu', $waktu_kelas);
    $stmt->bindParam(':pengajar', $data_mhs['nama']);
    $stmt->bindParam(':id_pengajar', $_SESSION['mhs_id']);
    $stmt->bindParam(':seat_available', $kapasitas_kelas); 
    $namakelas=strip_tags('Tutor '.$_POST['nama-kelas']);
    $deskripsi_kelas = strip_tags($_POST['deskripsi-kelas']);
    $lokasi_kelas = strip_tags($_POST['lokasi-kelas']);
    $tanggal_kelas = strip_tags($_POST['tanggal-kelas']);
    $waktu_kelas = strip_tags($_POST['waktu-kelas']);
    $kapasitas_kelas = strip_tags($_POST['kapasitas-kelas']);
    if($stmt->execute()){
      echo'<div class="alert alert-success" role="alert">
      Kelas berhasil dibuat, data Anda akan divalidasi oleh tim akademis, dan akan otomatis tampil apabila data sudah divalidasi.
    </div>';
    }
  }
  else{
    echo'
      <div class="alert alert-danger" role="alert">
        Anda hanya bisa menjadi pengajar tutor untuk tutor satu minggu kedepan.
      </div>
    ';
  }
}

if(False){
?>
<div class="alert alert-warning" role="alert">
  Maaf, masa pendaftaran menjadi pengajar dibuka mulai hari senin jam 00:00 WIB sampai sabtu jam 23:59 WIB.
</div>
<?php
}
else{
?>
<form action="registerkelas" method="post">
  <div class="form-group row">
    <label for="staticEmail" class="col-sm-2 col-form-label">Nama Pengajar Tutor</label>
    <div class="col-sm-10">
      <input type="text" readonly class="form-control-plaintext" id="staticEmail" value="<?=$data_mhs['nama']?>" disabled>
    </div>
  </div>
  <div class="form-group row">
    <label for="inputPelajaran" class="col-sm-2 col-form-label">Nama Pelajaran</label>
    <div class="col-sm-10">
      <select id="inputPelajaran" name="nama-kelas" class="form-control" required>
        <option selected value="">Pilih Jenis Pelajaran</option>
        <option value="DDP 1">DDP 1</option>
        <option value="MD-1">MD-1</option>
        <option value="MatDas-1">MatDas-1</option>
        <option value="FisDas">FisDas</option>
        <option value="PSD (KI)">PSD (KI)</option>
      </select>
    </div>
  </div>
  <div class="form-group row">
    <label for="inputPassword" class="col-sm-2 col-form-label">Deskripsi Tutor</label>
    <div class="col-sm-10">
    <textarea class="form-control" id="inputPassword" name="deskripsi-kelas" placeholder="Deskripsi Tutor" required></textarea>
    </div>
  </div>
  <div class="form-group row">
    <label for="inputlokasi" class="col-sm-2 col-form-label">Lokasi Tutor</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="inputlokasi" name="lokasi-kelas" placeholder="Lokasi Tutor (ex. Perpucil Atas)" required>
    </div>
  </div>
  <div class="form-group row">
    <label for="input tanggal" class="col-sm-2 col-form-label">Tanggal Tutor</label>
    <div class="col-sm-10">
    <input type="date" class="form-control" id="inputtanggal" name="tanggal-kelas" min="<?=$opendate?>" max="<?=$closedate?>" required>
    <small>Tanggal tutor hanya diperbolehkan untuk satu minggu kedepan</small>
    </div>
  </div>
  <div class="form-group row">
    <label for="inputwaktu" class="col-sm-2 col-form-label">Waktu Tutor</label>
    <div class="col-sm-3">
      <input type="time" class="form-control" id="inputwaktu" name="waktu-kelas" required>
    </div>
    <div class="col-sm-7">
    <p>*Apabila terdapat 3 kolom, gunakan AM / PM pada kolom terakhir</p>
    </div>
  </div>
  <div class="form-group row">
    <label for="inputseat" class="col-sm-2 col-form-label">Jumlah Kapasitas</label>
    <div class="col-sm-3">
      <input type="number" class="form-control" id="inputseat" name="kapasitas-kelas" placeholder="Masukan hanya angka.." required>
    </div>
  </div>
  <div class="form-group row">
        <div class="col-sm-10">
        <button type="submit" class="btn btn-primary btn-md form-button">Buat Kelas</button>
        </div>
    </div>
</form>
<script>
  $.('.calendario').flatpickr();
</script>

<?php
}
?>


