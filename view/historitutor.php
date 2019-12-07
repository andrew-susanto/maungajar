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

        if(isset($_POST['action'])){
            if($_POST['action']=='cancel'){
                $stmt = $conn->prepare("UPDATE pendaftaran_mhs SET status_tutor='user_cancel' WHERE id_mhs='".$_SESSION['mhs_id']."' AND id_kelas='".$_POST['kelas']."'");
                $stmt->execute();

                $batal_kelas_count = $data_mhs['batal_kelas']+1;

                $stmt = $conn->prepare("UPDATE mahasiswa SET batal_kelas='".$batal_kelas_count."' WHERE id='".$_SESSION['mhs_id']."'");
                $stmt->execute();
            }
        }

        $stmt = $conn->prepare("SELECT * FROM daftarkelas");
        $stmt->execute();
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
        $daftarkelas=$stmt->fetchAll();

        $stmt = $conn->prepare("SELECT * FROM pendaftaran_mhs WHERE id_mhs='".$_SESSION['mhs_id']."' ORDER BY id DESC");
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
    }
    else{
        header("Location: ".BASEURL);
    }
}
else{
    header("Location: ".BASEURL);
}

$data['judul']='Histori Tutor - mauNGajar';
$data['page']='dashboard';
require_once'template/header.php';
require_once'template/desktop-nav.php';
?>

<div class="container main-content">
    <h2>Histori Tutor</h2>
    <div class="row" style="margin-top:10px">
    <?php foreach($arrayKelas as $kelas){?>
        <div class="col-sm-6 mt-4">
            <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?= $kelas['datakelas']['nama_kelas']?></h5>
                <p class="card-text">
                <?= $kelas['datakelas']['deskripsi_kelas']?><br><br>
                    Tanggal : <?= $kelas['datakelas']['tanggal']?><br>
                    Waktu : <?= $kelas['datakelas']['waktu']?><br>
                    Tempat : <?= $kelas['datakelas']['lokasi_kelas']?><br>
                    Pengajar : <?= $kelas['datakelas']['pengajar']?><br>
                    Status Tutor :  
                    <?php 
                    if($kelas['status_tutor']=='user_cancel'){
                        echo'<span class="status-cancelled">Dibatalkan Pendaftar</span>';
                    } 
                    elseif($kelas['status_tutor']=='onprogress'){
                        echo'<span class="status-normal">Akan Berlangsung</span>';
                    }
                    elseif($kelas['status_tutor']=='done'){
                        echo'<span class="status-done">Telah Selesai</span>';
                    }
                    elseif($kelas['status_tutor']=='cancelled'){
                        echo'<span class="status-cancelled">Tutor Dibatalkan</span>';
                    }
                    ?>
                    <?php if($daftarkelas[$kelas['id_kelas']-1]['last_updated']>0){
                        echo '<br><br>
                        <small class="text-muted">Last Updated : '.date("d F Y H:i:s",$daftarkelas[$kelas['id_kelas']-1]['last_updated']).'</small>
                        ';
                    }?>
                </p>
                <?php if($kelas['status_tutor']!='user_cancel'){?>
                    <a href="<?= BASEURL;?>/detailkelas/<?=$kelas['id_kelas']?>" class="btn btn-primary">Detail Kelas</a>
                <?php }?>
                
            </div>
            </div>
        </div>
        <?php } ?>


<div class="mobile-nav">
  <a href="<?= BASEURL;?>/main"><span style="font-size:25px"><i class="fas fa-home"></i></span></a>
  <a href="<?= BASEURL;?>/daftar" ><span style="font-size:25px"><i class="fas fa-window-restore"></i></span></a>
  <a href="<?= BASEURL;?>/forum" ><span style="font-size:25px"><i class="fab fa-discourse"></i></span></a>
</div> 
<?php
require_once'template/footer.php';
?>