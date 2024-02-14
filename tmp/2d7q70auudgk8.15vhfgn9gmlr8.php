<!DOCTYPE html>
<html>

<head>
   <title><?= ($html_title) ?></title>
   <meta charset='utf8' />
   <link rel="stylesheet" type="text/css" href="style/home.css">
   <link rel="stylesheet" type="text/css" href="style/login-style.css">
   <link rel="stylesheet" type="text/css" href="style/account.css">
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
   <script src="libraries/GSAP/gsap.min.js"></script>
   <script src="libraries/GSAP/Draggable.min.js"></script>
   <script src="javascript/smooth-scroll.js"></script>
   <link rel="stylesheet" href="assets/fontawesome/css/all.css">
</head>

<body>
   <div class="smooth-scroll-wrapper">
      <div class="navbar-container">
         <div class="navbar">
            <div class="navbar-section">
               <p>website name</p>
            </div>
            <div class="navbar-section">
               <ul>
                  <li><a href="<?= ($BASE) ?>/">HOME</a></li>
                  <li><a href="<?= ($BASE) ?>/map">MAP</a></li>
                  <li><a href="<?= ($BASE) ?>/report">REPORT</a></li>
               </ul>
            </div>
            <div class="navbar-section">
               <?php if ($SESSION['username']=='UNSET'): ?>
                  
                     <?php if ($thisIsLoginPage=='true'): ?>
                        
                           <?php echo $this->render($content,NULL,get_defined_vars(),0); ?>
                        
                        <?php else: ?>
                           <a href="<?= ($BASE) ?>/login">LOG IN</a>
                        
                     <?php endif; ?>
                  
                  <?php else: ?>
                     <p> Logged in as <?= ($SESSION['username']) ?>.
                     <form method=post action="<?= ($BASE) ?>/account">
                        <input type="submit" name="submit" value="Account">
                     </form>
                     </p>
                  
               <?php endif; ?>
            </div>
         </div>
      </div>
      <div class="layout-content">
         <?php echo $this->render($content,NULL,get_defined_vars(),0); ?>
      </div>
   </div>
</body>

</html>