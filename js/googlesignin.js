function onSignIn(googleUser) {
          var id_token = googleUser.getAuthResponse().id_token;
          var xhr = new XMLHttpRequest();
          xhr.open('POST', '<?=BASEURL;?>/logingoogle.php');
          xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
          xhr.onload = function() {
            console.log(xhr.responseText);
            document.getElementById('status-text').innerHTML=xhr.responseText;
           if(xhr.responseText == 'Signed in'){
                window.location.href = "<?=BASEURL;?>/main";
           }
          };
          xhr.send('idtoken=' + id_token);
        }