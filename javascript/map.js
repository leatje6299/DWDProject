
gsap.registerPlugin(Draggable);

var container = $("#map-container");
var draggableMap;
var editModeOn = false;

function update() {
    draggableMap = Draggable.create(".map", {
        bounds: container,
        edgeResistance: 0.1,
        type: "x,y",
        throwProps: true,
        autoScroll: true,
        zIndexBoost: false,
        zIndex: -1
    });
}

function enterEditMode() {
    editModeOn = !editModeOn;
    if (editModeOn) {
        draggableMap[0].disable();
        $('.map').css('cursor', 'cell').css('filter', 'opacity(0.5)');

        $('.map').on('click', function (e) {
            var x = event.pageX - $(this).offset().left;
            var y = event.pageY - $(this).offset().top;
            console.log(`hi ${x} and ${y} is the location i clicked`);
            $('#x-coord').val(x);
            $('#y-coord').val(y);
            openNewLocationModal();
        });
    }
    else {
        draggableMap[0].enable();
        $('.map').css('cursor', 'grab').css('filter', 'opacity(1)');
        $('.map').off('click');
    }
}

var baseUrl = 'https://leafevrier.edinburgh.domains/projectname/thirdplaces';

function showHint(str) {
    console.log("showHint(), str is", str);
    if (str.length == 0) {
        $("#txtHint").html("");
        return;
    }

    $.ajax({
        type: 'GET',
        url: baseUrl + "/search/" + str,
        success: function (response) {
            var hints = response.split(', ');
            console.log(hints);
            var html = '';
            for (var i = 0; i < hints.length; i++) {
                html += '<li>' + hints[i] + '</li>';
            }
            $("#textHint").html(html);

            var lis = document.querySelectorAll('#textHint li');
            lis.forEach(function (li) {
                li.addEventListener('click', function () {
                    // Retrieve the location associated with the clicked li
                    var location = this.textContent;

                    // Use the location to navigate on the map
                    navigateToLocation(location);
                });
            });
        },
        failure: function () {
            console.log("ajax failure!");
        }
    });
}

function navigateToLocation(location) {
    console.log("navigateToLocation(), location is", location);
    $.ajax({
        type: 'GET',
        url: baseUrl + "/location/" + location,
        success: function (response) {
            var coordinates = response.split(',');
            var x_pos = coordinates[0];
            var y_pos = coordinates[1];

            var newX = -(x_pos - window.innerWidth / 2);
            var newY = -(y_pos - window.innerHeight / 2);

            gsap.to('.map', { duration: 1, x: newX, y: newY });
        },
    })
}

$(document).ready(function () {
    $('#notes').on('submit', function (e) {
        e.preventDefault();
        var name = $('#thirdplace_name').val();

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function () {
                closeModal();
                openModal(name);
            },
            error: function (jqXHR) {
                $('.errorNote').text('Your input contains profanity. Please try again.');
            }
        });
    });

    $('#new-location-notes').on('submit', function (e) {
        e.preventDefault();
        console.log("i submitted new location");
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function () {
                closeNewLocationModal();
                updatePins();
            },
            error: function (jqXHR) {
                $('.errorNote').text(jqXHR.responseText);
            }
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    update();
    var map = document.getElementById('map');
    var scale = 1, minScale = 0.7, maxScale = 1.7;
    map.addEventListener('wheel', function (event) {
        event.preventDefault();
        if (event.deltaY < 0) {
            if (scale < maxScale) scale += 0.1;
        }
        else {
            if (scale > minScale) scale -= 0.1;
        }

        gsap.to(map, { scale: scale, transformOrigin: "center center" });
    }, { passive: false });
});


// MODALS
function openModal(name) {
    console.log("i open a set modal");
    $('#modal-content').text(name);
    $('#modal').css('display', 'flex').css('flex-direction', 'column');
    $('#modal-thirdplace-name').text(name);
    $('#thirdplace_name').val(name);

    $.ajax({
        url: baseUrl + '/notesByThirdplace/' + name,
        data: { thirdplace: name },
        success: function (data) {
            var notes = JSON.parse(data);
            var notesContainer = $('#modal .notes-grid');
            notesContainer.empty();
            notes.forEach(function (note) {
                var noteElement = $('<p>').text(note.reason);
                notesContainer.append(noteElement);
            });
        }
    })
}

function openNewLocationModal() {
    console.log("I open a new location modal");
    $('#modal-location').css('display', 'flex').css('flex-direction', 'column');
}

function closeModal() {
    console.log("i close a set modal");
    $('#modal').css('display', 'none');
    updatePins();
}

function closeNewLocationModal() {
    console.log("i close a new location modal");
    $('#modal-location').css('display', 'none');
    enterEditMode();
}

function updatePins() {
    var selectedTypes = $('.filter:checked').map(function(){
        return this.value;
    }).get();
    console.log("updating pins");
    $.ajax({
        url: baseUrl + '/updatePins',
        type: 'GET',
        data: {
            types: selectedTypes
        },
        success: function (response) {
            $('.pin-parent').html(response);
        }
    })
}

$('.filter').on('change', function() {
    console.log('Checkbox state changed');
    updatePins();
});
