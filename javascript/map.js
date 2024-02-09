
gsap.registerPlugin(Draggable);

var container = $("#container");
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

function openModal(name) {
    $('#modal-content').text(name);
    $('#modal').css('display', 'flex').css('flex-direction', 'column');
    $('#thirdplace_name').val(name);
}


function openNewLocationModal() {

}

function closeModal() {
    $('.modal').css('display', 'none');
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
            // Open modal and pass coordinates
            openModal("Edinburgh");
        });
    }
    else {
        draggableMap[0].enable();
        $('.map').css('cursor', 'grab').css('filter', 'opacity(1)');
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

            gsap.to('.map', {duration: 1, x: newX, y: newY});
        },
    })

}

$(document).ready(function () {
    $('#notes').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function () {
                closeModal();
                location.reload();
            },
            error: function (jqXHR) {
                $('.errorNote').text('Your input contains profanity. Please try again.');
            }
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    update();
});