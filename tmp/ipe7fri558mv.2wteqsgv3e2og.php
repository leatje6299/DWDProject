<html xmlns="http://www.w3.org/1999/html">

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
                <p>thirdplaces</p>
            </div>
            <div class="navbar-section">
                <ul>
                    <li><a href="<?= ($BASE) ?>/">HOME</a></li>
                    <li><a href="<?= ($BASE) ?>/map">MAP</a></li>
                    <li><a href="https://leafevrier.edinburgh.domains/projectname/report.pdf">REPORT</a></li>
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
                        <form method=post action="<?= ($BASE) ?>/account">
                            <input type="submit" name="submit" value="Account">
                        </form>
                        </p>
                    
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div id="map-container">
        <div class="map" id="map">
            <div class="pin-parent">
                <?php foreach (($thirdplacesData?:[]) as $place): ?>
                    <div class="pin <?= ($place['type']) ?>"
                        style="left: <?= ($place['position_x'] - 10) ?>px; top: <?= ($place['position_y'] - 15) ?>px;"
                        onclick="openModal('<?= ($place['name']) ?>')">
                        <!--                add amount of notes in a place-->
                        <p class="pin-number"><?= ($place['note_count']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="search-container">
        <div class="search">
            <button class="search-icon" id="search-icon"></button>
            <input class="location-input" type="text" onkeyup="showHint(this.value)" placeholder="Search Location">
            <div id="search-results">
                <ul id="textHint"></ul>
            </div>
        </div>
    </div>


    <div class="edit-map">
        <img src="https://hamasahdinillah.edinburgh.domains/Third_Place/DWDProject/img/AddNotes.svg" alt="Edit Map Icon" id="edit-map-icon" onclick="enterEditMode()">
    </div>

    <!--    <div id="overlay"></div>-->

    <div class="modal" id="modal">
        <div class="modal-header">
            <h3 id="modal-thirdplace-name">Placeholder</h3>
            <button class="close-btn" onclick="closeModal()"></button>
        </div>
        <div class="form">

            <?php if ($SESSION['username']!= 'UNSET'): ?>
                
                    <p><?= ($SESSION['username']) ?></p>
                    <form id="notes" name="notes" method="post" action="<?= ($BASE) ?>/submitReason">
                        <label for="stay-anon-check">Stay Anonymous</label>
                        <input type="checkbox" class="check" id="stay-anon-check" name="isAnonymous" value="1">
                        <input type="hidden" id="thirdplace_name" name="thirdplace">
                        <textarea class="input-field" id="reason" name="reason" placeholder="Write your reason here..."
                                  required></textarea>
                        <button type="submit" class="submit-btn" value="reason">Submit</button>
                        <p class="errorNote"> <?= ($error) ?> </p>
                    </form>
                
                <?php else: ?>
                    <p>Log in to leave a note</p>
                
            <?php endif; ?>
        </div>
        <div class="notes-grid">
            <div class="notes-grid-author">
                <div class="notes-grid-message"></div>
            </div>
        </div>


        </div>

    </div>

    <div class="modal" id="modal-location">
        <?php if ($SESSION['username']=='UNSET'): ?>
            
                <p>Login to leave a note</p>
            
            <?php else: ?>
                <form name="notes" id="new-location-notes" method="post" action="<?= ($BASE) ?>/submitReasonAndLocation">

                    <div class="modal-content">
                        <input type="text" name="location" placeholder="Enter the location name...">
                        <input type="hidden" id="x-coord" name="position_x">
                        <input type="hidden" id="y-coord" name="position_y">
                        <select name="thirdplace_type">
                            <option value="park">Park</option>
                            <option value="restaurant">Restaurant</option>
                            <option value="museum">Museum</option>
                            <option value="cafe">Cafe</option>
                            <option value="bar">Bar</option>
                            <option value="landmark">Landmark</option>
                            <option value="street">Street</option>
                            <option value="other">Other</option>
                        </select>
                        <p><?= ($SESSION['username']) ?></p>
                        <label for="stay-anon-check">Stay Anonymous</label>
                        <input type="checkbox" class="check" id="stay-anon-check-2" name="isAnonymous" value="1">
                    </div>
                    <div class="form">
                        <textarea class="input-field" type="text" name="reason" placeholder="Write your reason here..."
                            size="200" required></textarea>
                        <button type="submit" class="submit-btn" value="reason">Submit</button>
                        <p class="errorNote"> <?= ($error) ?> </p>
                    </div>
                </form>
            
        <?php endif; ?>
    </div>

    <div class="filters">
        <label><input type="checkbox" value="park" class="filter">park</label>
        <label><input type="checkbox" value="restaurant" class="filter">restaurant</label>
        <label><input type="checkbox" value="museum" class="filter">museum</label>
        <label><input type="checkbox" value="cafe" class="filter">cafe</label>
        <label><input type="checkbox" value="bar" class="filter">bar</label>
        <label><input type="checkbox" value="landmark" class="filter">landmark</label>
        <label><input type="checkbox" value="street" class="filter">street</label>
        <label><input type="checkbox" value="other" class="filter">other</label>
    </div>
</body>

<script src="javascript/map.js"></script>

</html>