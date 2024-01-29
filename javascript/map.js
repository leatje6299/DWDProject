var isDown = false;
var startX, startY, startBackgroundX, startBackgroundY;

var map = document.querySelector('.map');
var mapRect = map.getBoundingClientRect();
var imageWidth = 734; // your image's actual width
var imageHeight = 573;
map.addEventListener('mousedown', function (e) {
    isDown = true;
    startX = e.clientX;
    startY = e.clientY;
    startBackgroundX = parseInt(getComputedStyle(map).backgroundPositionX, 10);
    startBackgroundY = parseInt(getComputedStyle(map).backgroundPositionY, 10);
});

map.addEventListener('mousemove', function (e) {
    if (!isDown) return;

    var x = e.clientX;
    var y = e.clientY;

    var newBackgroundX = startBackgroundX + x - startX;
    var newBackgroundY = startBackgroundY + y - startY;
    
    map.style.backgroundPositionX = newBackgroundX + 'px';
    map.style.backgroundPositionY = newBackgroundY + 'px';
});

map.addEventListener('mouseup', function () {
    isDown = false;
});