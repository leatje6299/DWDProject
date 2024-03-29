<html>

<head>
    <meta charset='utf8' />
    <link rel="stylesheet" type="text/css" href="style/home.css">
    <link rel="stylesheet" type="text/css" href="style/map.css">
    <script src="libraries/GSAP/gsap.min.js"></script>
    <script src="libraries/GSAP/Draggable.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="assets/fontawesome/css/all.css">
</head>

<body>
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
    <div id="container">
        <div class="map">
            <?php foreach (($thirdplacesData?:[]) as $place): ?>
                <div class="pin" style="left: <?= ($place['position_x'] - 10) ?>px; top: <?= ($place['position_y'] - 15) ?>px;"
                    onclick="openModal('<?= ($place['name']) ?>')">
                    <!--                add amount of notes in a place-->
                    <p class="pin-number">5</p>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="search">
            <div class="search-container">
                <button class="fa-regular fa-search" id="search-icon"></button>
                <input class="location-input" type="text" onkeyup="showHint(this.value)" placeholder="Search Location">
                <div id="search-results">
                    <ul id="textHint">
                    </ul>
                </div>
            </div>
        </div>

        <div class="add-pin">
            <button class="fa-regular fa-plus" id="add-pin-btn" onclick="enterEditMode()"></button>
        </div>
    </div>

    <div id="modal">
        <div>
            <p id="modal-content">Placeholder</p>
        </div>
        <div class="form">
            <form id="notes" name="notes" method="post" action="<?= ($BASE) ?>/submitReason">
                <input type="hidden" id="thirdplace_name" name="thirdplace">
                <input type="text" id="reason" name="reason" placeholder="Write your reason here..." required>
                <button type="submit" class="submit-btn" value="reason">Submit</button>
                <p class="errorNote"> <?= ($error) ?> </p>
            </form>
        </div>
        <button class="submit-btn" onclick="closeModal()">Close</button>
    </div>
</body>

<script src="javascript/map.js"></script>

</html>