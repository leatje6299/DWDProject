$(document).ready(function () {
    $('#filter-toggle').click(function () {
        $('#filters-on').fadeToggle();
        $('#filters').fadeToggle().css('display', 'flex'); // animate visibility of filters div
    });
});