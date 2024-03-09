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
        infos.eq(currentInfo).css('visibility', 'visible');

        $('.info button').click(function () {
            infos.eq(currentInfo).css('visibility', 'hidden');
            currentInfo++;
            if (currentInfo >= infos.length) {

                $('.info').css('visibility', 'hidden');
                currentInfo = 0;
                return;
            }
            infos.eq(currentInfo).css('visibility', 'visible');
        });
    }
});

