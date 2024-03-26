gsap.registerPlugin(Draggable);

var container = $("#map-container");
var draggableMap;
var editModeOn = false;
var baseUrl = 'https://hamasahdinillah.edinburgh.domains/Third_Place/DWDProject';
var scale = 1, minScale = 0.7, maxScale = 1.7;

function update() {
    draggableMap = Draggable.create(".map", {
        bounds: container,
        type: "x,y",
        throwProps: true,
        autoScroll: true,
        zIndexBoost: false,
        zIndex: -1
    });
}

function showHint(str) {
    if (!str || str.length == 0) {
        $("#search-results").fadeToggle();
        return;
    } else {
        $('#search-results').fadeIn();
    }
    $.ajax({
        type: 'GET',
        url: baseUrl + "/search/" + str,
        success: function (response) {
            var hints = response.split(', ');
            var html = '';
            for (var i = 0; i < hints.length; i++) {
                html += '<li>' + hints[i] + '</li>';
            }
            $("#textHint").html(html);

            var lis = document.querySelectorAll('#textHint li');
            lis.forEach(function (li) {
                li.addEventListener('click', function () {
                    var location = this.textContent;
                    navigateToLocation(location);
                });
            });
        },
        failure: function () {
            console.log("ajax failure!");
        }
    });
}

function showHintModal(str) {
    if (!str || str.length == 0) {
        $('.search-results-modal').fadeOut();
        return;
    }
    $.ajax({
        type: 'GET',
        url: baseUrl + "/search/" + str,
        success: function (response) {
            var hints = response.split(', ');
            console.log(hints);
            var html = '';
            if (hints.length === 0 || (hints.length === 1 && hints[0] === "")) {
                $('.new-location-message').fadeIn();
                $('.place-type').fadeIn();
                $('.search-results-modal').fadeOut();
            } else {
                $('.place-type').fadeOut();
                $('.search-results-modal').fadeIn();
                $('.new-location-message').fadeOut();
                for (var i = 0; i < hints.length; i++) {
                    html += '<li>' + hints[i] + '</li>';
                }
            }
            $("#text-hint-modal").html(html);

            var lis = document.querySelectorAll('#text-hint-modal li');
            lis.forEach(function (li) {
                li.addEventListener('click', function () {
                    var location = this.textContent;
                    $('.location-input-modal').val(location);
                    $('.search-results-modal').fadeOut();
                });
            });
        },
        failure: function () {
            console.log("ajax failure!");
        }
    });
}

function navigateToLocation(location) {
    $.ajax({
        type: 'GET',
        url: baseUrl + "/location/" + location,
        success: function (response) {
            var coordinates = response.split(',');
            var x_pos = coordinates[0];
            var y_pos = coordinates[1];

            var mapWidth = 5000;
            var mapHeight = 3904;

            var newX = -(x_pos - window.innerWidth / 2) + mapWidth / 2;
            var newY = -(y_pos - window.innerHeight / 2) + mapHeight / 2;
            console.log(newX, newY);
            scale = 1;
            gsap.to('.map', { scale: scale });
            gsap.to('.map', { duration: 1, x: newX, y: newY });
            // Create a new pin
            var pin = $('<div class="search-pin">' + location + '</div>');
            pin.css({ top: (y_pos - 45) + 'px', left: (x_pos - 45) + 'px', display: 'none' });
            $('.map').append(pin);
            pin.fadeIn(500); // Adjust the duration as needed
            setTimeout(function () {
                pin.fadeOut(500, function () {
                    pin.remove();
                });
            }, 5000);

            // Open the location modal when the pin is clicked
            pin.click(function () {
                // Replace this with your code to open the location modal
                openModal(location);
            });
        },
    })
}

function updateZoomOverlays(){
    $('#overlay1').css('display', scale > 0.6 ? 'block' : 'none');
    $('#overlay2').css('display', scale > 0.9 ? 'block' : 'none');
    $('#overlay3').css('display', scale >1.1 ? 'block' : 'none');
}

$(document).ready(function () {
    update();
    console.log(scale);
    var map = document.querySelector('.map');
    map.addEventListener('wheel', function (event) {
        event.preventDefault();

        if (event.deltaY < 0) {
            if (scale < maxScale) scale += 0.1;
        }
        else {
            if (scale > minScale) scale -= 0.1;
        }
        updateZoomOverlays();
        gsap.to(map, { scale: scale });
        console.log(scale);

    }, { passive: false });

    let startDistance, startScale;

    map.addEventListener('touchstart', function (event) {
        console.log('touchstart event triggered');
        if (event.touches.length === 2) {
            startDistance = Math.hypot(
                event.touches[0].pageX - event.touches[1].pageX,
                event.touches[0].pageY - event.touches[1].pageY
            );
            startScale = scale;
            event.preventDefault();
        }
    }, { passive: false });

    map.addEventListener('touchmove', function (event) {
        console.log('touchmove event triggered');
        if (event.touches.length === 2) {
            const distance = Math.hypot(
                event.touches[0].pageX - event.touches[1].pageX,
                event.touches[0].pageY - event.touches[1].pageY
            );
            scale = startScale * distance / startDistance;

            scale = Math.max(minScale, Math.min(maxScale, scale));
            updateZoomOverlays();
            gsap.to(map, { scale: scale });
            console.log(scale);
            event.preventDefault();
        }
    }, { passive: false });

    function smoothOriginChange(targets, transformOrigin) {
        gsap.utils.toArray(targets).forEach(function (target) {
            var before = target.getBoundingClientRect();
            gsap.set(target, { transformOrigin: transformOrigin });
            var after = target.getBoundingClientRect();
            gsap.set(target, {
                x: "+=" + (before.left - after.left),
                y: "+=" + (before.top - after.top),
            });
        });
    }

    map.addEventListener('mousemove', function (event) {
        var rect = map.getBoundingClientRect();
        var x = event.clientX - rect.left;
        var y = event.clientY - rect.top;

        var origin;
        if (x < rect.width / 2 && y < rect.height / 2) {
            origin = "left top";
        } else if (x >= rect.width / 2 && y < rect.height / 2) {
            origin = "right top";
        } else if (x < rect.width / 2 && y >= rect.height / 2) {
            origin = "left bottom";
        } else {
            origin = "right bottom";
        }

        smoothOriginChange(map, origin);

    });

    var urlParams = new URLSearchParams(window.location.search);
    var location = urlParams.get('location');
    if (location) {
        navigateToLocation(location);
    }

});