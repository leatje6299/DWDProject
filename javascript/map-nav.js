$(document).ready(function () {
    $('#filter-toggle').click(function () {
        $('#filters-on').fadeToggle();
        $('#filters').fadeToggle().css('display', 'flex'); // animate visibility of filters div
    });

    var infos = $('.info'); // get all .info-slide elements
    var currentInfo = 0; // index of the currently visible .info-slide element

    infos.css('visibility', 'hidden'); // hide all .info-slide elements
    infos.eq(currentInfo).css('visibility', 'visible'); // show the first .info-slide element

    $('.info button').click(function() {
        infos.eq(currentInfo).css('visibility', 'hidden'); // hide the current .info-slide element
        currentInfo++; // increment the index
        if (currentInfo >= infos.length) { // if we've reached the end of the .info-slide elements
            currentInfo = 0; // reset the index to 0
        }
        infos.eq(currentInfo).css('visibility', 'visible'); // show the next .info-slide element
    });
});

