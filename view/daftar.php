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

        if(isset($url[1])){
            $stmt = $conn->prepare("SELECT * FROM daftarkelas WHERE id='".$url[1]."'");
            $stmt->execute();
            $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
            $res=$stmt->fetchAll();
            $kelas=$res[0];

            if(strtotime('now')<strtotime('sunday this week')){
            $closetime = strtotime('+ 24 hours', strtotime( 'sunday this week'));
            }
            else{
            $closetime = strtotime('+ 24 hours',strtotime( 'next sunday' ));
            }

            if(strtotime($kelas['tanggal'].' '.$kelas['waktu'])<strtotime('now') || strtotime($kelas['tanggal'].' '.$kelas['waktu'])>=$closetime){
            header("Location:".BASEURL."/daftar");
            }
        }
    }
    else{
        header("Location: ".BASEURL);
    }
}
else{
    header("Location: ".BASEURL);

}

$data['judul']='Daftar Tutor - mauNGajar';
$data['page']='daftar';
require_once'template/header.php';
require_once'template/desktop-nav.php';
?>

<div class="container main-content">
    <h2>Daftar Tutor</h2>
    <?php 
    if($data_mhs['banned']<(time()-172800)){
        if(isset($url[1])){
            if(isset($url[2])){
                require_once 'form/daftartutor/step3.php';
            }
            else{
                require_once 'form/daftartutor/step2.php';
            }
        }
        else{
            require_once 'form/daftartutor/step1.php';
        }
        
    }
    else{
        $timeleft=$data_mhs['banned']+172800-time();
        echo'<div class="alert alert-danger" role="alert">
        Akun Anda Diblokir ( Harap menunggu '.floor($timeleft/86400)." Hari ".floor($timeleft/3600)%24 ." Jam ".floor($timeleft/60)%60 ." Menit ".$timeleft%60 .' Detik )
        <br>Hal ini dimungkinkan karena Anda tidak mengikuti salah satu tutor yang telah Anda daftar
        </div>';
    }
        ?>
</div>

<div class="mobile-nav">
  <a href="<?= BASEURL;?>/main"><span style="font-size:25px"><i class="fas fa-home"></i></span></a>
  <a href="<?= BASEURL;?>/daftar" ><span style="font-size:25px"><i class="fas fa-window-restore"></i></span></a>
  <a href="<?= BASEURL;?>/forum" ><span style="font-size:25px"><i class="fab fa-discourse"></i></span></a>
</div> 
<?php
require_once'template/footer.php';
?>
