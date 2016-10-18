$(function () {

    //ADD YouTube vid ID to db
    //----------------------------------------------------------------------------------------------------------------//
    $(document).on("keypress", "#addYouTubeVidID_Input", function (e) {
        if (e.which == 13) {
            var VidIDField = '#addYouTubeVidID_Input';
            var VidID = $(VidIDField).val();

            if (VidID.length != 0) {

                $.ajax({
                    url:  window.location.pathname + "home/yt_submit",
                    type: 'GET',
                    data: {'VidID': VidID},
                    success: function (result) {
                        $('#addYouTubeVidID_Input').val(result);
                        revertSubmitButton();
                    },
                    failure: function () {
                        $('#addYouTubeVidID_Input').value('Failed');
                        revertSubmitButton();
                    }
                });

            }
            return false;    //<---- Add this line
        }
    });

    //show Login
    //----------------------------------------------------------------------------------------------------------------//
    $('#ADD-toggle').on('click', function (e) {
        if ( $("#addyoutubevidid_form").hasClass('gone')) {
            appear();
        } else {
            dissapear();
        }
        $("#addyoutubevidid_form").focus();
        e.preventDefault();
    });

    //load initial vidset
    //----------------------------------------------------------------------------------------------------------------//
    $(document).ready(function () {

        var tag = document.createElement('script');

        tag.src = "https://www.youtube.com/iframe_api";
        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    })

    //
    $(document).on("click", ".loadTrigger", function (e) {
        $id = event.target.id;

        $('#'+$id).closest("a").find(".video").hide();
        onYouTubeIframeAPIReady(event.target.id);
        e.preventDefault();
    })

    //Focus moves to the input when clicking the ADD button
    $(document).on("click", "#ADD-toggle", function (e) {
        $("#addYouTubeVidID_Input").focus();
    });

    //load more vid covers on scroll
    //----------------------------------------------------------------------------------------------------------------//
    $(window).scroll(function()
    {
        if($(window).scrollTop() == $(document).height() - $(window).height())
        {
            $.ajax({
                url: window.location.pathname + "home/videoset",
                type: 'POST',
                success: function (result) {
                    $('.img-list ').append(result);
                }
            });
        }
    });
});

//----------------------------------------------------------------------------------------------------------------//
//show/hide ADD input
function dissapear() {
    $("#addyoutubevidid_form").animate({opacity: '0'});
    $("#addyoutubevidid_form").addClass('gone');
}
function appear() {
    $("#addyoutubevidid_form").animate({opacity: '100'});
    $("#addyoutubevidid_form").removeClass('gone');
}
//reset the ADD button text after 2 seconds
function revertSubmitButton() {
    setTimeout(function () {
        $('#addyoutubevidid_form').addClass('gone');
        $('#addyoutubevidid_form').removeAttr('style');
        $('#addYouTubeVidID_Input').val('');
    }, 2000);
}

//----------------------------------------------------------------------------------------------------------------//
//youtube api
function onYouTubeIframeAPIReady(id) {

    player1 = new YT.Player(id, {
        height: '237',
        width: '317',
        videoId: id,
        events: {
            'onReady': onPlayerReady
        }
    });
}

// The API will call this function when the video player is ready.
function onPlayerReady(event) {
    event.target.playVideo();
}
//----------------------------------------------------------------------------------------------------------------//
