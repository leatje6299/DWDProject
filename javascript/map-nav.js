$(document).ready(function () {
    $('#filter-toggle').click(function () {
        if ($('#filters-on').is(':visible')) {
            $('#filters-on').animate({ opacity: 0 }, function () {
                $(this).css('display', 'none');
                $('#filters').css('display', 'flex').animate({ opacity: 1 });
            });
        } else {
            $('#filters').animate({ opacity: 0 }, function () {
                $(this).css('display', 'none');
                $('#filters-on').css('display', 'block').animate({ opacity: 1 });
            });
        }
    });

    var infos = $('.info');
    var currentInfo = 0;

    infos.css('visibility', 'hidden');
    infos.eq(currentInfo).css('visibility', 'visible');

    $('.info button').click(function () {
        infos.eq(currentInfo).css('visibility', 'hidden');
        currentInfo++;
        if (currentInfo >= infos.length) {
            currentInfo = 0;
        }
        infos.eq(currentInfo).css('visibility', 'visible');
    });
});

