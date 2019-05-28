<html lang="en">
  <head>
    <meta name="google-signin-scope" content="profile email">
    <meta name="google-signin-client_id" content="62773334792-14230oqk4utak526f195jg9ue07ugn3l.apps.googleusercontent.com">
    <script src="https://apis.google.com/js/platform.js" async defer></script>
  </head>
  <body>
    <div>Name : <span id="name"></span></div>
    <div>Email : <span id="email"></span></div>
    <img src="" id="image">
    <input type="text" id="token1" value="">
    <div class="g-signin2" data-onsuccess="onSignIn" data-theme="dark"></div>
    <a href="#" onclick="signOut();">Sign out</a>

    <script>

      function onSignIn(googleUser) {
        // Useful data for your client-side scripts:
        var profile = googleUser.getBasicProfile();
        console.log("ID: " + profile.getId()); // Don't send this directly to your server!
        console.log('Full Name: ' + profile.getName());
        console.log('Given Name: ' + profile.getGivenName());
        console.log('Family Name: ' + profile.getFamilyName());
        console.log("Image URL: " + profile.getImageUrl());
        console.log("Email: " + profile.getEmail());
        document.getElementById("name").innerHTML = profile.getName();
        document.getElementById('email').innerHTML = profile.getEmail();
        document.getElementById('image').src = profile.getImageUrl();
        
        // The ID token you need to pass to your backend:
        var id_token = googleUser.getAuthResponse().id_token;
        console.log("ID Token: " + id_token);
        document.getElementById('token1').value = id_token;
        

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'http://localhost:8000/testing');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
          console.log('Signed in as: ' + xhr.responseText);
        };
        xhr.send('idtoken=' + id_token);
      }
      function signOut() {
        var auth2 = gapi.auth2.getAuthInstance();
        console.log(auth2);
        auth2.signOut().then(function () {
          console.log('User signed out.');
        });
      }

      var xhr = new XMLHttpRequest();
      xhr.open('POST', 'http://localhost:8000/testing');
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xhr.onload = function() {
        console.log('Signed in as: ' + xhr.responseText);
      };
      xhr.send('idtoken=' + id_token);
    </script>
  </body>
</html>


