
gsap.registerPlugin(Draggable);

var container = $("#map-container");
var draggableMap;
var editModeOn = false;
var baseUrl = 'https://hamasahdinillah.edinburgh.domains/Third_Place/DWDProject';
var map = $('.map');

function enterEditMode() {
    editModeOn = !editModeOn;
    if (editModeOn) {
        draggableMap[0].disable();
        map.css('cursor', 'cell').css('filter', 'opacity(0.5)');

        map.on('click', function (e) {
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
        map.css('cursor', 'grab').css('filter', 'opacity(1)');
        map.off('click');
    }
}

$(document).ready(function () {
    document.querySelector('.overlay').classList.add('dissolve');

    $('#notes').on('submit', function (e) {
        e.preventDefault();
        var name = $('#thirdplace_name').val();

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                closeModal();
                openModal(name);
                var message = JSON.parse(response);
                showPopup(message.success);
            },
            error: function (response) {
                var message = JSON.parse(response.responseText);
                showPopup(message.error);
            }
        });
    });

    $('#new-location-notes').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                closeNewLocationModal();
                updatePins();
                console.log(response);
                var message = JSON.parse(response);
                showPopup(message.success);
            },
            error: function (response) {
                console.log(response);
                var message = JSON.parse(response.responseText);
                showPopup(message.error);
            }
        });
    });

    $('.search-container').on('click', function () {
        $('.search').animate({ width: '400px' }, 500, function () {
            $('.location-input').css('display', 'block');
        });
    });

    $(document).on('click', function (event) {
        if (!$(event.target).closest('.search-container').length) {
            $('#search-results').fadeOut();
            $('.location-input').css('display', 'none');
            $('.search').animate({ width: '92px' }, 500);
        }
    });

    $('#report-problem-form').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            type: 'POST',
            url: baseUrl + '/reportAProblem',
            data: $(this).serialize(),
            success: function (response) {
                var message = JSON.parse(response);
                showPopup(message.success);
            },
            error: function (response) {
                var message = JSON.parse(response.responseText);
                showPopup(message.error);
            }
        });
    });
});


// MODALS
function openModal(name) {
    $('#modal-content').text(name);
    $('#modal').css('display', 'flex').css('flex-direction', 'column');
    $('#modal-thirdplace-name').text(name);
    $('#thirdplace_name').val(name);

    $.ajax({
        url: baseUrl + '/notesByThirdplace/' + name,
        data: { thirdplace: name },
        success: function (data) {
            var notes = JSON.parse(data);
            console.log(notes);
            var notesContainer = $('#modal .notes-grid');

            notes.forEach(function (note) {
                var noteAuthor = $('<div class="notes-grid-author">').text((note.isAnonymous != null) ? 'Anonymous' : note.username);
                var noteElement = $('<div class="notes-grid-message">').text(note.reason);

                noteAuthor.append(noteElement);

                var noteGrid = $('<div class="notes-grid">');
                noteGrid.append(noteAuthor);

                notesContainer.append(noteGrid);
            });
        }
    })
}

function openNewLocationModal() {
    $('#modal-location').css('display', 'flex').css('flex-direction', 'column');
}

function openReportProblem() {
    $('#report-problem').css('display', 'flex');
}

function closeModal() {
    console.log("i close a set modal");
    $('#modal').css('display', 'none');
    updatePins();
    var notesContainer = $('#modal .notes-grid');
    notesContainer.empty();
}

function closeNewLocationModal() {
    $('#modal-location').css('display', 'none');
    enterEditMode();
}

function closeReportProblem() {
    $('#report-problem').css('display', 'none');
}

function updatePins() {
    var selectedTypes = $('.filter:checked').map(function () {
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

$('.filter').on('change', function () {
    console.log('Checkbox state changed');
    updatePins();
});

function showPopup(message) {
    $('#popup-message').text(message);
    $('#popup').show();
}

$('#popup-close').click(function () {
    $('#popup').hide();
});

