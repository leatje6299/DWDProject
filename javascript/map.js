
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
    // document.getElementById('modal-content').textContent = name;
    // document.getElementById('modal').style.display = 'flex';
    // document.getElementById('modal').style.flexDirection = 'column';
    //document.getElementById('thirdplace_name').value = name;
}

function closeModal() {
    //document.getElementById('modal').style.display = 'none';
    $('#modal').css('display', 'none');
}

$(document).ready(function () {
    $('#notes').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function (data) {
                // Handle the success case
                // For example, you can close the modal and refresh the page
                closeModal();
                location.reload();
            },
            error: function (xhr) {
                // Handle the error case
                // For example, you can display the error message in the form
                $('.errorNote').text('Your input contains profanity.Please try again.');
            }
        });
    });
    update();
});
