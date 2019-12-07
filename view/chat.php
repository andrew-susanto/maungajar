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

        $token_kelas=$url[1];

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT * FROM chat WHERE kelas_id='".$token_kelas."'");
        $stmt->execute();
        $chat_history=$stmt->fetchAll();
    }
    else{
        header("Location: ".BASEURL);
    }
}
else{
    header("Location: ".BASEURL);
}
?>

<link rel="stylesheet" href="<?= BASEURL;?>/css/bootstrap.css">
<link rel="stylesheet" href="<?= BASEURL;?>/css/style.css"> 
<script src="<?= BASEURL;?>/js/jquery-3.4.1.min.js"></script>
<style>
html{
    padding:20px;
    overflow-x:hidden;
}

</style>

<div class="chat-history" id="chat-history">
<?php foreach($chat_history as $chat_item){if ($chat_item['mhs_id']==$_SESSION['mhs_id']){ ?>
<div class="row justify-content-end" id="<?=$chat_item['id']?>">
<div class="card chat-bubble-right">
  <div class="card-body chat-body">
    <span style="font-size:12px; color:grey" ><?=$chat_item['mhs_name']?></span><br>
    <?=$chat_item['chat']?><br>
    <span style="font-size:12px; color:grey" ><?=date("H:i",$chat_item['unix_time'])?></span>
  </div>
</div>
</div>
<?php } else{?>
    <div class="row justify-content-start" id="<?=$chat_item['id']?>">
    <div class="card chat-bubble-left">
  <div class="card-body chat-body">
    <span style="font-size:12px; color:grey" ><?=$chat_item['mhs_name']?></span><br>
    <?=$chat_item['chat']?><br>
    <span style="font-size:12px; color:grey" ><?=date("H:i",$chat_item['unix_time'])?></span>
  </div>
  </div>
</div>
<?php }}?>

</div>


<div class="input-group mb-3 fixed-bottom">
  <input type="text" class="form-control" id="chat" placeholder="<?= $data_mhs['nama']?>..." aria-label="<?= $data_mhs['nama']?>" aria-describedby="button-send" >
  <div class="input-group-append">
    <button class="btn btn-outline-secondary" type="button" id="button-send">Send</button>
  </div>
</div>

<script>
    window.scrollTo(0,document.body.scrollHeight);

    // Get the input field
    var input = document.getElementById("chat");

    input.addEventListener("keyup", function(event) {    
    if (event.keyCode === 13) {
        event.preventDefault();
        document.getElementById("button-send").click();
    }
    });

    function checkChat(){
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            chatData = JSON.parse(this.responseText);
            chatData.forEach(checkChatDisplay);
          }
        };
        xmlhttp.open("POST", "<?=BASEURL;?>/getchat.php", true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send("token=<?=$token_kelas?>");
        
        function checkChatDisplay(item,index){
             var chatbubble = document.getElementById(item["id"]);
             if(!chatbubble){
                if(item["mhs_id"]=='<?=$_SESSION['mhs_id']?>'){
                    $("#chat-history").append("<div class='row justify-content-end' id='".concat(item['id'],"'> <div class='card chat-bubble-right'><div class='card-body chat-body'><span style='font-size:12px; color:grey' >",item['mhs_name'],"</span><br>",item['chat'],"<br><span style='font-size:12px; color:grey' >",item['time'],"</span></div></div></div>"));
                }
                else{
                    $("#chat-history").append("<div class='row justify-content-start' id='".concat(item['id'],"'> <div class='card chat-bubble-left'><div class='card-body chat-body'><span style='font-size:12px; color:grey' >",item['mhs_name'],"</span><br>",item['chat'],"<br><span style='font-size:12px; color:grey' >",item['time'],"</span></div></div></div>"));
                }
                window.scrollTo(0,document.body.scrollHeight);
                
            }
        }
    } 

    $(document).ready(function(){
        $("#button-send").click(function(){
            var chat_content=document.getElementById("chat").value; 
            document.getElementById("chat").value="";
            if(chat_content!=""){
              $.post("<?=BASEURL;?>/addchat.php",
              {
              nama_mahasiswa : "<?= $data_mhs['nama']?>",
              id : "<?= $_SESSION['mhs_id']?>",
              chat: chat_content,
              token: "<?=$token_kelas?>"
              },
              function(data,status){
              });
              checkChat();
            }
        });
        });

    $(document).ready(function() {
      setInterval(function() {
        checkChat();
      }, 2000);
    });

  function addDarkCSS() {
    var x = document.createElement("LINK");
    x.setAttribute("id","dark-mode-css")
    x.setAttribute("rel", "stylesheet");
    x.setAttribute("type", "text/css");
    x.setAttribute("href", "<?=BASEURL;?>/css/darkmode.css");
    document.head.appendChild(x);
  }
</script>


<script src="<?=BASEURL;?>/js/custom.js"></script>