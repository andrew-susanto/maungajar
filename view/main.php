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

        $stmt = $conn->prepare("SELECT * FROM daftarkelas");
        $stmt->execute();
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
        $daftarkelas=$stmt->fetchAll();

        $stmt = $conn->prepare("SELECT * FROM daftarkelas WHERE status_kelas='onprogress' ORDER BY tanggal ASC");
        $stmt->execute();
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
        $daftarkelastersediadate=$stmt->fetchAll();

        $stmt = $conn->prepare("SELECT * FROM daftarkelas WHERE status_kelas='onprogress' ORDER BY nama_kelas ASC");
        $stmt->execute();
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
        $daftarkelastersediacourse=$stmt->fetchAll();

        $stmt = $conn->prepare("SELECT * FROM pendaftaran_mhs WHERE id_mhs='".$_SESSION['mhs_id']."' AND status_tutor='onprogress' AND ((status_tutor='cancelled' AND last_updated>".(time()-259200)." ) OR (status_tutor='user_cancel' AND last_updated>".(time()-259200)." )) ORDER BY id DESC");
        $stmt->execute();
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
        $res=$stmt->fetchAll();

        foreach($res as $kelas){
            $arrayKelas[$kelas['id_kelas']] = $kelas;
        }

        foreach($daftarkelas as $kelasdata){
            if(array_key_exists($kelasdata['id'],$arrayKelas)){
                $arrayKelas[$kelasdata['id']]['datakelas'] = $kelasdata;
            }
        }

        if(strtotime('now')<strtotime('+ 9 hours',strtotime('sunday this week'))){
            $closetime = strtotime('+ 24 hours', strtotime( 'sunday this week'));
        }
        else{
            $closetime = strtotime('+ 24 hours',strtotime( 'next sunday' ));
        }
    }
    else{
        header("Location: ".BASEURL);
    }
}
else{
    header("Location: ".BASEURL);
}

$data['judul']='Dashboard - mauNGajar';
$data['page']='dashboard';
require_once'template/header.php';
require_once'template/desktop-nav.php';
?>


<div class="container main-content">
    
    <?php 
    $time = date('G');
    if ($time >=0 && $time < 11){
    echo'<span style="font-size:35px; font-weight:700;">Selamat Pagi, </span>';
    }
    elseif($time>=11 && $time<17){
    echo'<span style="font-size:35px; font-weight:700;">Selamat Siang, </span>';
    }
    elseif($time>=17 && $time<24){
    echo'<span style="font-size:35px; font-weight:700;">Selamat Malam,  </span>';
    }
    echo '<span style="font-size:35px; font-weight:400">'.$data_mhs['nama']."!</span>";?>
    </h2><br><hr>
    <h2>Tutor Terdaftar</h2>
    <div class="row" style="margin-top:10px">
        <?php foreach($arrayKelas as $kelas){?>
        <div class="col-sm-6 mt-4">
            <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?= $kelas['datakelas']['nama_kelas']?></h5>
                <p class="card-text">
                    <?= $kelas['datakelas']['deskripsi_kelas']?>    
                <br><br>
                    Tanggal : <?= date('d F Y',strtotime($kelas['datakelas']['tanggal']))?><br>
                    Waktu : <?= $kelas['datakelas']['waktu']?><br>
                    Tempat : <?= $kelas['datakelas']['lokasi_kelas']?><br>
                    Pengajar : <?= $kelas['datakelas']['pengajar']?><br>
                    Status Tutor :  
                    <?php 
                    if($kelas['status_tutor']=='onprogress'){
                        echo'<span class="status-normal">Akan Berlangsung</span>';
                    }
                    elseif($kelas['status_tutor']=='done'){
                        echo'<span class="status-done">Telah Selesai</span>';
                    }
                    elseif($kelas['status_tutor']=='cancelled'){
                        echo'<span class="status-cancelled">Tutor Dibatalkan</span>';
                    }
                    elseif($kelas['status_tutor']=='user_cancel'){
                        echo'<span class="status-cancelled">Dibatalkan Pendaftar</span>';
                    }?>
                </p>
                <?php 
                if($kelas['status_tutor']!='user_cancel'){ ?>
                <a href="<?= BASEURL;?>/detailkelas/<?=$kelas['id_kelas']?>" class="btn btn-primary">Detail Kelas</a>
                <?php } ?>
            </div>
            </div>
        </div>
        <?php } ?>
    </div>
    <br><br>
    <h2>Tutor Tersedia</h2>
    <form class="form-inline" style="margin-top:20px;">
        Pilih Berdasarkan : 
        <select style="margin-left:10px; border-radius:30px;" class="form-control" id="sortby">
            <option value="date">Tanggal Tutor</option>
            <option value="course">Mata Kuliah</option>
        </select>
    </form>
    <div id="sortbydate" class="sortcourse" >
        <div><div>
            <?php 
            $date = '';
            foreach($daftarkelastersediadate as $kelas){ 
                if(strtotime($kelas['tanggal'].' '.$kelas['waktu'])>strtotime('now') && strtotime($kelas['tanggal'].' '.$kelas['waktu'])<=$closetime){
                    if($date != $kelas['tanggal']){ ?>
                    </div></div>
                    <div class="item-course-list">
                    <h5><?=date('l, j F Y',strtotime($kelas['tanggal']))?></h5>
                    <hr>
                    <div class="scrolling-wrapper">
                    <?php 
                    $date = $kelas['tanggal']; 
                    } ?>
                    <div class="card h-scroll">
                    <div class="card-body">
                        <h5 class="card-title"><?=$kelas['nama_kelas']?></h5>
                        <p class="card-text">
                            <?=$kelas['deskripsi_kelas']?>
                            <br><br>
                            Tanggal : <?=date('d F Y',strtotime($kelas['tanggal']))?><br>
                            Waktu : <?=$kelas['waktu']?><br>
                            Tempat : <?=$kelas['lokasi_kelas']?><br>
                            Pengajar : <?=$kelas['pengajar']?><br>
                            <b>
                                <?php if($kelas['seat_available']<1){
                                    echo '<span style="color:red">Kursi Tersedia : '.$kelas["seat_available"].'</span>';
                                 }else{
                                     echo '<span style="color:green">Kursi Tersedia : '.$kelas["seat_available"].'</span>';
                                 }
                                ?>
                            </b>
                        </p>
                        <a href="<?=BASEURL?>/daftar/<?=$kelas['id']?>/" class="btn btn-primary daftar-button">Daftar Kelas</a>
                    </div>
                    </div>
            <?php }} ?>
            </div>
        </div>     
    </div>
    <div id="sortbycourse" class="sortcourse" style="display:none">
        <div><div>
            <?php 
            $namakelas = '';
            foreach($daftarkelastersediacourse as $kelas){ 
                if(strtotime($kelas['tanggal'].' '.$kelas['waktu'])>strtotime('now') && strtotime($kelas['tanggal'].' '.$kelas['waktu'])<=$closetime){
                if($kelas['nama_kelas']!=$namakelas){ ?>
                </div></div>
                <div class="item-course-list">
                <h5><?=$kelas['nama_kelas']?></h5>
                <hr>
                <div class="scrolling-wrapper">      
                <?php 
                $namakelas = $kelas['nama_kelas'];
                } ?>
                <div class="card h-scroll">
                <div class="card-body">
                    <h5 class="card-title"><?=$kelas['nama_kelas']?></h5>
                    <p class="card-text">
                    <?=$kelas['deskripsi_kelas']?><br><br>
                        Tanggal : <?=$kelas['tanggal']?><br>
                        Waktu : <?=$kelas['waktu']?><br>
                        Tempat : <?=$kelas['lokasi_kelas']?><br>
                        Pengajar : <?=$kelas['pengajar']?><br>
                        <b>
                            <?php if($kelas['seat_available']<1){
                                echo '<span style="color:red">Kursi Tersedia : '.$kelas["seat_available"].'</span>';
                             }else{
                                 echo '<span style="color:green">Kursi Tersedia : '.$kelas["seat_available"].'</span>';
                             }
                            ?>
                        </b>
                    </p>
                    <a href="<?=BASEURL?>/daftar/<?=$kelas['id']?>/" class="btn btn-primary daftar-button">Daftar Kelas</a>
                </div>
                </div>
            <?php }} ?>
            </div>
        </div>     
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