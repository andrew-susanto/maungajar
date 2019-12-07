<script>

function addDarkCSS() {
    var x = document.createElement("LINK");
    x.setAttribute("id","dark-mode-css")
    x.setAttribute("rel", "stylesheet");
    x.setAttribute("type", "text/css");
    x.setAttribute("href", "<?=BASEURL;?>/css/darkmode.css");
    document.head.appendChild(x);
  }

function removenotif(){
  var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            var element = document.getElementsByClassName('notif-card');
            while(element[0]) {
              element[0].parentNode.removeChild(element[0]);
            }
            document.getElementById("bell-notif").style.content = "url('<?=BASEURL;?>/img/bell.png')"; 
          }
        };
        xmlhttp.open("POST", "<?=BASEURL;?>/removenotif.php", true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send("id=<?=$_SESSION['mhs_id']?>");
}

</script>
<script src="<?=BASEURL;?>/js/custom.js"></script>

</body>
</html>


