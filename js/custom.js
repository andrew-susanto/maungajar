$( ".inner-switch" ).on("click", function() {
    if( $( "body" ).hasClass( "dark" )) {
      $( "body" ).removeClass( "dark" );
      $( ".inner-switch" ).text( "OFF" );
      document.getElementById("dark-mode-css").remove();
      document.cookie = "dark-mode=off"; 
    } else {
      $( "body" ).addClass( "dark" );
      $( ".inner-switch" ).text( "ON" );
      addDarkCSS();
      document.cookie = "dark-mode=on"; 
    }
});

var darkmodestate = getCookie('dark-mode');
if (darkmodestate != ""){
    if(darkmodestate=='on'){
        $( "body" ).addClass( "dark" );
        $( ".inner-switch" ).text( "ON" );
        addDarkCSS();
    }
    else{
        $( "body" ).removeClass( "dark" );
         $( ".inner-switch" ).text( "OFF" );
         document.getElementById("dark-mode-css").remove();
    }
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
  }
  function activateDarkMode(){
    $( "body" ).addClass( "dark" );
    $( ".inner-switch" ).text( "ON" );
    addDarkCSS();
  }
  function activateLightMode() {
    $( "body" ).removeClass( "dark" );
    $( ".inner-switch" ).text( "OFF" );
    document.getElementById("dark-mode-css").remove();
  }

  function setColorScheme() {
    const isDarkMode = window.matchMedia("(prefers-color-scheme: dark)").matches
    const isLightMode = window.matchMedia("(prefers-color-scheme: light)").matches
    window.matchMedia("(prefers-color-scheme: dark)").addListener(e => e.matches && activateDarkMode())
    window.matchMedia("(prefers-color-scheme: light)").addListener(e => e.matches && activateLightMode())
  }

  setColorScheme();
function signOut() {
    var auth2 = gapi.auth2.getAuthInstance();
    auth2.signOut().then(function () {
      console.log('User signed out.');
      window.location.href = "https://akademis.maung.id/logoutgoogle.php";
    });
  }
