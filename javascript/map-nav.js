$(document).ready(function () {
    $('#filter-toggle').click(function () {
        if ($('#filters-on').is(':visible')) {
            $('#filters-on').animate({ opacity: 0 }, function () {
                $(this).css('display', 'none');
                $('#filters').css('display', 'flex').css('pointer-events','all').animate({ opacity: 1 });
            });
        } else {
            $('#filters').animate({ opacity: 0 }, function () {
                $(this).css('display', 'none');
                $('#filters-on').css('display', 'block').css('pointer-events','none').animate({ opacity: 1 });
            });
        }
    });

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
        infos.eq(currentInfo).css('visibility', 'visible').css('pointer-events', 'auto');

        $('.next').click(function () {
            infos.eq(currentInfo).css('visibility', 'hidden').css('pointer-events', 'none');
            currentInfo++;
            if (currentInfo >= infos.length) {
                $('.info').css('visibility', 'hidden').css('pointer-events', 'none');
                currentInfo = 0;
                return;
            }
            infos.eq(currentInfo).css('visibility', 'visible').css('pointer-events', 'auto');
        });

        $('.back').click(function () {
            infos.eq(currentInfo).css('visibility', 'hidden').css('pointer-events', 'none');
            currentInfo--;
            if (currentInfo >= infos.length) {
                $('.info').css('visibility', 'hidden').css('pointer-events', 'none');
                currentInfo = 0;
                return;
            }
            infos.eq(currentInfo).css('visibility', 'visible').css('pointer-events', 'auto');
        });

        $('.skip').click(function(){
            $('.info').css('visibility', 'hidden').css('pointer-events', 'none');
            currentInfo = 0;
        })
    }
});

