<?php
$conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER, DB_PASS);
// set the PDO error mode to exception
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $conn->prepare("SELECT * FROM notification WHERE mhs_id='".$_SESSION['mhs_id']."' ORDER BY id DESC");
$stmt->execute();
$result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
$notification=$stmt->fetchAll();
?>

<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container mt-2">
        <a class="navbar-brand default-font" href="<?=BASEURL?>/main"><img src="<?=BASEURL;?>/img/logo.png" class="main-logo"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-item nav-link <?php if($data['page']=='dashboard'){echo 'active';}?>" href="<?=BASEURL;?>/main">Dashboard <span class="sr-only">(current)</span></a>
                <a class="nav-item nav-link <?php if($data['page']=='daftar'){echo 'active';}?>" href="<?=BASEURL;?>/daftar">Daftar Tutor</a>
            </div>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-danger dropdown-toggle notif-icon" style="padding-left:0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?php if($stmt->rowCount()>=1){?><div class="red-notif bell-ring-notif" id="bell-notif"><?php } else{
            ?><div class="bell-ring-notif" id="bell-notif"><?php }?>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                <a onclick="removenotif()" class="clear-notif">Clear Notification</a>
                <?php foreach($notification as $notificationitem){?>
                <div class="card notif-card" style="width: 18rem;" id="notif-card">
                <div class="card-body">
                    <p class="card-text"><?=$notificationitem['content']?></p>
                    <p class="card-text"><small class="text-muted"><?=date("d F Y H:i:s",$notificationitem['time'])?></small></p>
                </div>
                </div>
                <?php }?>
            </div>
        <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle rounded-circle name-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?=substr($data_mhs['nama'],0,1)?>
        </button>
        <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-left">
            <a class="dropdown-item" href="<?=BASEURL;?>/updateprofile"><div class="row" style="margin-left:0"><div class="update-profile-icon"></div>&nbsp;Update Profile</div></a>
            <a class="dropdown-item" href="<?=BASEURL;?>/registerkelas">Daftar Pengajar</a>
            <a class="dropdown-item" href="<?=BASEURL;?>/daftarkelas">Histori Pengajar</a>
            <a class="dropdown-item" href="<?=BASEURL;?>/historitutor">Histori Tutor</a>
            <a class="dropdown-item disabled" href="<?=BASEURL;?>/catatan">Latihan Soal & Catatan</a>
            <a class="dropdown-item" href="<?=BASEURL;?>/forum">Forum Tanya Jawab</a>
            <?php if($data_mhs['role']=='tutor' || $data_mhs['role']=='admin'){?><a class="dropdown-item" href="<?=BASEURL;?>/konfirmasikelas">Konfirmasi Kelas</a><?php } ?>
            <?php if($data_mhs['role']=='admin'){?><a class="dropdown-item" href="<?=BASEURL;?>/changerole">Ubah Role</a><?php } ?>
            <?php if($data_mhs['role']=='admin'){?><a class="dropdown-item" href="<?=BASEURL;?>/changeemail">Ubah Google Email</a><?php } ?>
            <div class="dropdown-item switch" style="padding:10px;">Dark mode:              
                    <span class="inner-switch">OFF</span>
            </div>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item logout-button" href="<?=BASEURL;?>/logout.php" ><div class="row" style="margin-left:0"><div class="logout-icon"></div>&nbsp;Logout</div></a>
            <div class="g-signin2" data-onsuccess="onSignIn" style="display:none;"></div>

        </div>
        </div>
    </div>
</nav>