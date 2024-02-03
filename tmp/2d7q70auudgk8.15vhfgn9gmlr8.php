<!DOCTYPE html>
<html>

<head>
   <title><?= ($html_title) ?></title>
   <meta charset='utf8' />
   <link rel="stylesheet" type="text/css" href="style/home.css">
   <script src="libraries/GSAP/gsap.min.js"></script>
   <script src="libraries/GSAP/Draggable.min.js"></script>
</head>

<body>
   <?php if ($SESSION['username']=='UNSET'): ?>

      
         <?php if ($thisIsLoginPage=='true'): ?>
            
               <?php echo $this->render($content,NULL,get_defined_vars(),0); ?>
            
            <?php else: ?>
               <span class="loggedin"> Not logged in</span>
               <?php echo $this->render($content,NULL,get_defined_vars(),0); ?>
               <br />
               <a href="<?= ($BASE) ?>/login">Log in</a>
               <br />
               <a href="<?= ($BASE) ?>/login">or register as a new user</a> <!-- JON ADDED THIS -->
            
         <?php endif; ?>
      
      <?php else: ?>
         <span class="loggedin"> Logged in as <?= ($SESSION['username']) ?>.
            <form method=post action="<?= ($BASE) ?>/logout">
               <input type="submit" name="submit" value="Logout">
            </form>
         </span>
         <hr />
         <?php echo $this->render($content,NULL,get_defined_vars(),0); ?>
      

   <?php endif; ?>
</body>

</html>