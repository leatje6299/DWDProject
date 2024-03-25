
gsap.registerPlugin(Draggable);

var container = $("#map-container");
var draggableMap;
var editModeOn = false;
var baseUrl = 'https://hamasahdinillah.edinburgh.domains/Third_Place/DWDProject';
var map = $('.map');
var curLocation;
var searchOpen = false;
function enterEditMode() {
    editModeOn = !editModeOn;
    if (editModeOn) {
        draggableMap[0].disable();
        map.css('cursor', 'cell').css('filter', 'opacity(0.5)');

        map.on('click', function (e) {
            var x = event.pageX - $(this).offset().left;
            x /= scale;
            var y = event.pageY - $(this).offset().top;
            y /= scale;
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
        // If filters are open, close them
        if ($('#filters').is(':visible')) {
            $('#filter-toggle').click();
        }

        // Then open the search
        $('.search').animate({ width: '400px' }, 500, function () {
            $('.location-input').css('display', 'block');
            searchOpen = true;
        });
    });

    $(document).on('click', function (event) {
        if (!$(event.target).closest('.search-container').length && searchOpen) {
            $('#search-results').fadeOut();
            $('.location-input').css('display', 'none');
            $('.search').animate({ width: '95%' }, 500);
            searchOpen = false;
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
    $('#modal-notes').css('display', 'flex').css('flex-direction', 'column');
    $('#modal-note-thirdplace-name').text(name);
    $('#thirdplace_name').val(name);
    curLocation = name;

    $.ajax({
        url: baseUrl + '/notesByThirdplace/' + name,
        data: { thirdplace: name },
        success: function (data) {
            var notes = JSON.parse(data);
            console.log(notes);
            var notesContainer = $('#modal-notes .notes-container');

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

function openWriteNoteModal() {
    $('#modal').css('display', 'flex');
    $('#modal-thirdplace-name').text(curLocation);
}

function openNewLocationModal() {
    $('#modal-location').css('display', 'flex').css('flex-direction', 'column');
}

function openReportProblem() {
    $('#report-problem').css('display', 'flex');
}

function closeModal() {
    $('#modal').css('display', 'none');
    $('#modal-notes').css('display', 'none');
    $('#modal-location').css('display', 'none');
    updatePins();
    var notesContainer = $('#modal-notes .notes-container');
    notesContainer.empty();
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
    $('body').addClass('blur');
}

$('#popup-close').click(function () {
    $('#popup').hide();
    $('body').removeClass('blur');
});

$('form').on('submit', function(e) {
    e.preventDefault();

    // your form submission logic here
    var form = $(this);

    $.ajax({
        // your AJAX settings here
    }).done(function() {
        // this will clear all the form fields after the AJAX call is successful
        form[0].reset();
    });
});