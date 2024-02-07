
gsap.registerPlugin(Draggable);

var container = $("#container");

function update() {
    Draggable.create(".map", {
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

function closeModal() {
    $('#modal').css('display', 'none');
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