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
        
        if(isset($url[1])&& $url[1]!='create'){
            $stmt = $conn->prepare("SELECT * FROM forumthread WHERE id=".$url[1]."");
            $stmt->execute();
            if($stmt->rowCount()<1){
                header("Location: ".BASEURL."/forum");
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

$data['judul']='Forum Diskusi - mauNGajar';
require_once'template/header.php';
require_once'template/desktop-nav.php';


?>
<div class="container main-content">
    <?php if(isset($url[1]) && $url[1]=='create'){ 
            if(isset($_POST['nama-pelajaran'])) {
                if($_POST['pertanyaan']!=''){
                $stmt = $conn->prepare("INSERT INTO forumthread (mahasiswa_id, time_created, title, nama_pelajaran) VALUES (:mahasiswa_id,:time_created,:pertanyaan,:nama_pelajaran)");
                $waktu = time();
                $stmt->bindParam(':mahasiswa_id', $_SESSION['mhs_id']);
                $stmt->bindParam(':pertanyaan', $pertanyaan);
                $stmt->bindParam(':nama_pelajaran', $pelajaran);
                $stmt->bindParam(':time_created', $waktu );
                $pertanyaan = htmlspecialchars($_POST['pertanyaan']);
                $pelajaran = strip_tags($_POST['nama-pelajaran']);
                if($stmt->execute()){
                     echo'<div class="alert alert-success" role="alert">
                     Berhasil mengajukan petanyaan
                    </div>';
                }
                else{
                    echo'<div class="alert alert-danger" role="alert">
                      Gagal mengajukan pertanyaan silahkan coba lagi
                    </div>';
                }
            }
            else{
                echo'<div class="alert alert-danger" role="alert">
                      Silahkan masukkan pertanyaan, pertanyaan tidak boleh kosong
                    </div>';
            }
        }
        ?>
    <h4>Ajukan Pertanyaan Baru</h4>
    <a href='<?=BASEURL;?>/forum'>Kembali Ke Forum</a>
    <form action="" method="post">
        <div class="form-group">
            <label for="inputPelajaran" class="col-sm-2 col-form-label">Nama Pelajaran</label>
            <div class="col-sm-10">
              <select id="inputPelajaran" name="nama-pelajaran" class="form-control" required>
                <option value='' selected>Pilih Jenis Pelajaran</option>
                <option value='DDP 1'>DDP 1</option>
                <option value='MD-1'>MD-1</option>
                <option value='MatDas-1'>MatDas-1</option>
                <option value='FisDas'>FisDas</option>
              </select>
            </div>
        </div>
        <div class="form-group">
            <label for="pertanyaan"  class="col-sm-2 col-form-label">Pertanyaan</label>
            <textarea class="form-control rounded-0 forum-new-thread" rows="5" name="pertanyaan" id="pertanyaan"></textarea>
        </div>
      <button type="submit" class="btn btn-primary">Ajukan Pertanyaan</button>
    </form>
<?php 
}elseif(isset($url[1])){ 
    if(isset($_POST['body'])){
      $stmt = $conn->prepare("INSERT INTO forumpost (thread_id, mahasiswa_id, time_created, body, rating) VALUES ( :thread_id, :mahasiswa_id, :time_created, :body, :rating )");
      $stmt->bindParam(':thread_id', $url[1]);
      $stmt->bindParam(':mahasiswa_id', $_SESSION['mhs_id']);
      $stmt->bindParam(':time_created', $currentime);
      $stmt->bindParam(':body', $body);
      $stmt->bindParam(':rating', $rating);
      $body = htmlspecialchars($_POST['body']);
      $rating = 0;
      $currentime = time();
      $stmt->execute();
    }
     if(isset($_POST['love_post'])){
      $stmt = $conn->prepare("SELECT mahasiswa_id, post_id FROM forumratinglog WHERE mahasiswa_id='".$_SESSION['mhs_id']."' AND post_id=".$_POST['love_post']."");
      $stmt->execute();
      $count = $stmt->rowCount();

      if($count==0){
        $stmt = $conn->prepare("UPDATE forumpost SET rating=".($arrayPost[$_POST['love_post']]['rating']+1)." WHERE id=".$_POST['love_post']."");
        $stmt->execute();
        $stmt = $conn->prepare("INSERT INTO forumratinglog (mahasiswa_id,post_id) VALUES ('".$_SESSION['mhs_id']."','".$_POST['love_post']."')");
        $stmt->execute();
      }
    }
    if(isset($_POST['postdelete'])){
        $stmt = $conn->prepare("DELETE FROM forumpost WHERE id=:delete AND mahasiswa_id=:mahasiswa_id");
        $stmt->bindParam(':delete',$_POST['postdelete']);
        $stmt->bindParam(':mahasiswa_id',$_SESSION['mhs_id']);
        if($stmt->execute()){
            echo'<div class="alert alert-success" role="alert">
                 Berhasil menghapus jawaban
                </div>';
        }
        else{
          echo'<div class="alert alert-danger" role="alert">
                 Gagal menghapus jawaban
                </div>';  
        }
    }
    
    $stmt = $conn->prepare("SELECT * FROM forumthread WHERE id=".$url[1]."");
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
    $res=$stmt->fetchAll();
    
    $stmt = $conn->prepare("SELECT * FROM forumpost WHERE thread_id=".$url[1]."");
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
    $post=$stmt->fetchAll();

    $stmt = $conn->prepare("SELECT * FROM mahasiswa");
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
    $mahasiswa=$stmt->fetchAll();

    $stmt = $conn->prepare("SELECT mahasiswa_id, post_id FROM forumratinglog WHERE mahasiswa_id='".$_SESSION['mhs_id']."'");
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
    $ratinglog=$stmt->fetchAll();

    foreach($mahasiswa as $datamahasiswa){
      $arrayMahasiswa[$datamahasiswa['id']] = $datamahasiswa;
    }

    foreach($ratinglog as $rating){
      $arrayRating[$rating['post_id']] = $rating;
    }

    $thread = $res[0];
    $thread['datamahasiswa'] = $arrayMahasiswa[$thread['mahasiswa_id']];
    if($thread['mahasiswa_id']==$_SESSION['mhs_id']){
        $thread['ableDelete'] = True;
    }else{
        $thread['ableDelete'] = False;
    }

    $arrayPost = array();

    foreach($post as $postdata){
      $arrayPost[$postdata['id']] = $postdata;
      $arrayPost[$postdata['id']]['datamahasiswa'] = $arrayMahasiswa[$postdata['mahasiswa_id']];
      if(array_key_exists($postdata['id'],$arrayRating)){
        $arrayPost[$postdata['id']]['liked'] = True;
      }
      else{
        $arrayPost[$postdata['id']]['liked'] = False;
      }
      if($postdata['mahasiswa_id']==$_SESSION['mhs_id']){
          $arrayPost[$postdata['id']]['ableDelete'] = True;
      }
      else{
          $arrayPost[$postdata['id']]['ableDelete'] = False;
      }
    }
   
   ?>
    <div class="forum-thread-page">
    <a href='<?=BASEURL;?>/forum'>Kembali ke forum</a>
      <div class="list-group-item forum-thread-item">
        <div class="d-flex w-100 justify-content-between">
            <small><span class="forum-course-header"><?=$thread['nama_pelajaran']?></span></small>
            <?php if($thread['ableDelete']){
                echo'<form method="post" id="threadDelete" action="'.BASEURL.'/forum">
                    <input type="hidden" name="threaddelete" value="'.$url[1].'"><input type="submit" style="display:none;"></form>' ;
                echo'<a onclick="deleteThread()"><i class="far fa-trash-alt"></i></a>';
            } ?>
        </div>
        <h5 class="thread-question"><?=nl2br($thread['title'])?></h5><br>
        <h6 class="forum-question-footer"><b><?=date('d F Y H:i',$thread['time_created'])?> dari <?=$thread['datamahasiswa']['nama']?></b></h6>
      </div>
      <div class="list-group">
      <?php foreach($arrayPost as $postdata){ ?>
        <div class="list-group-item forum-jawab-item">
          <div class="d-flex w-100 justify-content-between">
            <small><b><?=$postdata['datamahasiswa']['nama']?></b></small>
            <?php if($postdata['ableDelete']){
                echo'<a onclick="deletePost('.$postdata['id'].')"><i class="far fa-trash-alt"></i></a>';
            } ?>
          </div>
          <h5 class="thread-question"><?=nl2br($postdata['body'])?></h5>
          <a onclick="submitlove(<?=$postdata['id']?>)" <?php if($postdata['liked']==True){echo'style="pointer-events:none"';}?> id="lovebutton<?=$postdata['id']?>"><span class="badge badge-pill badge-secondary forum-jawab-love"><i class="fas fa-heart"></i>&nbsp;&nbsp;TERIMA KASIH &nbsp;&nbsp;<span id='lovecount<?=$postdata['id']?>'><?=$postdata['rating']?></span></span></a>
        </div>
      <?php } ?>
      </div>
      
      <form action="" method="post">
        <input type="text" name="body" class="form-control" placeholder="Tahu Jawabannya ? Tambahkan disini">
        <input type="submit" style="position: absolute; left: -9999px">
      </form>
    
      <form action="" id="postDelete" method="post">
        <input type="hidden" name="postdelete" value="" id="postdeletevalue">
        <input type="submit" style="display:none">
      </form>
      
    </div>
    <script>
    function deletePost(postid){
        document.getElementById('postdeletevalue').value = postid;
        document.getElementById('postDelete').submit();
    }
    function deleteThread(){
        document.getElementById('threadDelete').submit();
    }
      var xhr = new XMLHttpRequest();
      function submitlove(id){
        xhr.open("POST", '<?=BASEURL;?>/forum/<?=$thread['id']?>', true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
        xhr.send('love_post='+id);
        document.getElementById('lovecount'+id).innerHTML=parseInt(document.getElementById('lovecount'+id).innerHTML)+1;
        document.getElementById('lovebutton'+id).style.pointerEvents = 'none';
      }
    </script>


<?php }elseif(!isset($url[1])){?>
    <h4>Forum Diskusi</h4>
    <a href='<?=BASEURL;?>/forum/create'>Ajukan Pertanyaan Baru</a>
    <?php 
    if(isset($_POST['threaddelete'])){
        $stmt = $conn->prepare("DELETE FROM forumthread WHERE id=:delete AND mahasiswa_id=:mahasiswa_id");
        $stmt->bindParam(':delete',$_POST['threaddelete']);
        $stmt->bindParam(':mahasiswa_id',$_SESSION['mhs_id']);
        if($stmt->execute()){
            echo'<div class="alert alert-success" role="alert">
                 Berhasil menghapus pertanyaan
                </div>';
        }
        else{
          echo'<div class="alert alert-danger" role="alert">
                 Gagal menghapus pertanyaan
                </div>';  
        }
    }
    $stmt = $conn->prepare("SELECT * FROM forumthread");
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
    $res=$stmt->fetchAll();

    $stmt = $conn->prepare("SELECT * FROM forumpost");
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
    $post=$stmt->fetchAll();

    $arrayPost = array();
    foreach($post as $postdata){
      if(!array_key_exists($postdata['thread_id'],$arrayPost)){
        $arrayPost[$postdata['thread_id']] = 1;
      }
      else{
        $arrayPost[$postdata['thread_id']]++;
      }
    }

    foreach($res as $threaddata){
      $arrayThread[$threaddata['id']] = $threaddata;
      if(array_key_exists($threaddata['id'],$arrayPost)){
        $arrayThread[$threaddata['id']]['postcount'] = $arrayPost[$threaddata['id']];
      }
      else{
        $arrayThread[$threaddata['id']]['postcount'] = 0;
      }
      
    }

    echo '<div class="list-group forum-thread-list">';
    foreach($arrayThread as $pertanyaanitem){
    ?>
      <a href="<?=BASEURL;?>/forum/<?=$pertanyaanitem['id']?>" class="list-group-item list-group-item-action forum-thread-item">
      <small><b><?=$pertanyaanitem['nama_pelajaran']?>&nbsp;&nbsp; <?=date('d F Y',$pertanyaanitem['time_created'])?></b></small>  
      <h5 class="thread-question forum-thread-question"><?=nl2br($pertanyaanitem['title'])?></h5>
      <span class="badge badge-pill badge-secondary forum-jawab-button"><?=$pertanyaanitem['postcount']?> Jawaban</span>
      <?php 
      if($pertanyaanitem['solved']==0){
        echo'<span class="badge badge-pill badge-secondary forum-jawab-button">Jawab</span>';
      } else{
        echo'<span class="badge badge-pill badge-secondary forum-solved-badges">Solved</span>';
      }
      ?>
      </a>
    <?php }?>
    </div>
<?php }?>

<div class="mobile-nav">
  <a href="<?= BASEURL;?>/main"><span style="font-size:25px"><i class="fas fa-home"></i></span></a>
  <a href="<?= BASEURL;?>/daftar" ><span style="font-size:25px"><i class="fas fa-window-restore"></i></span></a>
  <a href="<?= BASEURL;?>/forum" ><span style="font-size:25px"><i class="fab fa-discourse"></i></span></a>
</div>  

<?php
require_once'template/footer.php';
?>
