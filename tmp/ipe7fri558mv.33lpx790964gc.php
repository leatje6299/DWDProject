<div class="form-container">
  <div class="form-box" id="formBox">
    <div class="top-header" id="topHeader">
      <h3>Hello, again</h3>
    </div>
    <!--    LOG IN -->
    <div class="box-login" id="login">
      <div>
        <form id="loginForm" class="input-group" name="login" method="post" action="<?= ($BASE) ?>/login">
          <div class="input-group">
            <div class="input-field">
              <input type="text" class="input-box" id="username" name="username" required>
            </div>
            <div class="input-field">
              <input type="password" class="input-box" id="logPassword" name="password" required>
              <div class="eye-area">
                <div class="eye-box" onclick="MyLogPassword">
                  <i class="fa-regular fa-eye" id="eye"></i>
                  <i class="fa-regular fa-eye" id="eye-slash"></i>
                </div>
              </div>
            </div>
            <p class="errorNote">  </p>
            <div class="remember">
              <input type="checkbox" id="formCheck" class="check">
              <label for="formCheck">Remember Me</label>
            </div>
            <div class="input-field">
              <input type="submit" class="input-submit" value="Sign In">
            </div>
          </div>
        </form>
      </div>
    </div>
    <!--    Register  -->
    <div class="box-register" id="register">
      <form id="registerForm" class="input-group" name="register" method="post" action="<?= ($BASE) ?>/register">
        <div class="input-group">
          <div class="input-field">
            <input type="text" class="input-box" id="regUser" name="newUsername" required>
          </div>
          <div class="input-field">
            <input type="text" class="input-box" id="regEmail" name="userEmail" required>
          </div>
          <div class="input-field">
            <input type="password" class="input-box" id="regPassword" name="newPassword" required>
            <div class="eye-area">
              <div class="eye-box" onclick="MyRegPassword">
                <i class="fa-regular fa-eye" id="eye-2"></i>
                <i class="fa-regular fa-eye" id="eye-slash-2"></i>
              </div>
            </div>
          </div>
          <p class="errorNote">  </p>
          <div class="remember">
            <input type="checkbox" id="formCheck-2" class="check">
            <label for="formCheck">Remember Me</label>
          </div>
          <div class="input-field">
            <input type="submit" class="input-submit" value="Sign Up">
          </div>
        </div>
      </form>
    </div>
    <!--    SWITCH -->
    <div class="switch">
      <a href="#" class="login" onclick="loginSwitch()">Log In</a>
      <a href="#" class="register" onclick="registerSwitch()">Register</a>
      <div class="btn-active" id="btn"></div>
    </div>
  </div>

</div>

<script>
  var x = document.getElementById("login");
  var y = document.getElementById("register");
  var z = document.getElementById("btn");
  var baseUrl = 'https://hamasahdinillah.edinburgh.domains/Third_Place/DWDProject';

  var headerText = document.querySelector("#topHeader h3"); // Select the h3 element
  function loginSwitch() {
    x.style.left = "20px"
    y.style.left = "400px"
    z.style.left = "0px"
    headerText.textContent = "Hello, again"; // Change the text back to "Hello, again"
    console.log("i pressed sign in");
  }
  function registerSwitch() {
    x.style.left = "-350px"
    y.style.left = "25px"
    z.style.left = "150px"
    headerText.textContent = "Sign up"; // Change the text to "Sign up"
    console.log("i pressed login");
  }
  loginSwitch();

  $('#loginForm').on('submit', function (e) {
    e.preventDefault();
    $.ajax({
      url: $(this).attr('action'),
      type: 'POST',
      data: $(this).serialize(),
      success: function () {
        console.log("Login Successful");
        window.location.href = baseUrl + '/map';
      },
      error: function (jqXHR) {
        console.log(jqXHR.responseText);
        var error = JSON.parse(jqXHR.responseText);
        $('.errorNote').text(error.error);
      }
    })
  })
  $('#registerForm').on('submit', function (e) {
    e.preventDefault();
    $.ajax({
      url: $(this).attr('action'),
      type: 'POST',
      data: $(this).serialize(),
      success: function () {
        console.log("Register Successful");
        window.location.href = baseUrl + '/map';
        console.log(baseUrl + '/map');
      },
      error: function (jqXHR) {
        console.log(jqXHR.responseText);
        var error = JSON.parse(jqXHR.responseText);
        $('.errorNote').text(error.error);
      }
    })
  })
</script>