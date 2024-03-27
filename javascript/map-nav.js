
$(document).ready(function () {

    var filtersOn = $('#filters-on');
    var filters = $('#filters');

    $('#filter-toggle').click(function () {
        if (filtersOn.is(':visible')) {
            filtersOn.animate({ opacity: 0 }, function () {
                filtersOn.css('display', 'none');
                filters.css('display', 'flex').css('pointer-events', 'all').animate({ opacity: 1 });
            });
        } else {
            filters.animate({ opacity: 0 }, function () {
                filters.css('display', 'none');
                filtersOn.css('display', 'block').css('pointer-events', 'none').animate({ opacity: 1 });
            });
        }
    });

    $('.filter-close').click(function () {
        if ($('#filters').is(':visible')) {
            $('#filter-toggle').click();
        }
    })

    if (!localStorage.getItem('visitedBefore')) {
        showTutorial();
        console.log("true");
        localStorage.setItem('visitedBefore', 'true');
    }

    $('#show-tutorial-button').click(function () {
        showTutorial();
    });

    // Function to show the tutorial
    function showTutorial() {
        console.log("show tutorial");
        var infos = $('.info');
        var currentInfo = 0;

        infos.css('visibility', 'hidden');
        infos.css('pointer-events', 'none');
        infos.eq(currentInfo).css('visibility', 'visible').css('pointer-events', 'auto').animate({ opacity: 1 }, 200);

        $('.next').off('click').click(function () {
            infos.eq(currentInfo).animate({ opacity: 0 }, 200, function () {
                $(this).css('visibility', 'hidden').css('pointer-events', 'none');
                currentInfo++;
                if (currentInfo >= infos.length) {
                    $('.info').css('visibility', 'hidden').css('pointer-events', 'none');
                    currentInfo = 0;
                    return;
                }
                infos.eq(currentInfo).css('visibility', 'visible').css('pointer-events', 'auto').css('opacity', 0).animate({ opacity: 1 }, 200);
            });
        });

        $('.back').off('click').click(function () {
            infos.eq(currentInfo).animate({ opacity: 0 }, 200, function () {
                $(this).css('visibility', 'hidden').css('pointer-events', 'none');
                currentInfo--;
                if (currentInfo < 0) {
                    $('.info').css('visibility', 'hidden').css('pointer-events', 'none');
                    currentInfo = infos.length - 1;
                    return;
                }
                infos.eq(currentInfo).css('visibility', 'visible').css('pointer-events', 'auto').css('opacity', 0).animate({ opacity: 1 }, 200);
            });
        });

        $('.skip').off('click').click(function () {
            $('.info').css('visibility', 'hidden').css('pointer-events', 'none');
            currentInfo = 0;
        })
    }
});

