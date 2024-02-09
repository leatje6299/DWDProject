
<div class="form-container">
  <div class="form-box">
    <!--    LOG IN -->
    <div class="box-login" id="login">
      <div class="top-header">
        <h3>Hello, again</h3>
      </div>
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
    <!--    Register  -->
    <div class="box-register" id="register">
      <div class="top-header">
        <h3>Sign Up</h3>
      </div>
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
  function loginSwitch() {
    x.style.left = "27px"
    y.style.left = "400px"
    z.style.left = "0px"
    console.log("i pressed sign in");
  }
  function registerSwitch() {
    x.style.left = "-350px"
    y.style.left = "25px"
    z.style.left = "150px"
    console.log("i pressed login");
  }
  loginSwitch();

</script>