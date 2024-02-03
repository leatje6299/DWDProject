<div id="container">
    <div class="map">
        <?php foreach (($thirdplacesData?:[]) as $place): ?>
            <div class="square" style="left: <?= ($place['position_x']) ?>px; top: <?= ($place['position_y']) ?>px;"
                onclick="openModal('<?= ($place['name']) ?>')"></div>
        <?php endforeach; ?>
    </div>
</div>

<div id="modal">
    <div id="modal-content"></div>
    <button onclick="closeModal()">Close</button>
</div>

<script src="javascript/map.js"></script>