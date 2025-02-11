var an_media, an_overlay;
var w = $(window).width() * 1.5;
var h = $(window).height() * 1.5;

function anamorph(an_media, an_overlay, an_backup) {
    $.getScript('https://www.youtube.com/iframe_api');
    var isMobile = false;
    if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0, 4))) isMobile = true;
    var an_media_url = an_media.split('.');
    var an_media_id = an_media.split('/');
    var an_length = an_media_url.length - 1;
    var an_yt_length = an_media_id.length - 1;
    $(document.body).css({
        "margin": "0",
        "background": "#000000"
    });
    var an_style = "position: fixed; top: 50%; left: 50%; min-width: 100%; min-height: 100%; transform: translate(-50%, -50%); width=auto; height=auto";
    var an_wrapper = "<div id='an_wrapper' style='z-index: -997; opacity: 0;'></div>";
    var an_over = "<div id='an_overlay' style='z-index: -998; position: fixed; top: 0; width: 100%; height: 100%;'></div>";
    var an_video = "<video id='an_video' style='z-index: -999; " + an_style + "' autoplay loop muted><source src=" + an_media + " type='video/mp4'></video>";
    var an_img = "<div id='an_img' style='z-index: -999; " + an_style + "'><img style='" + an_style + "' src='" + an_media + "'></div>";
    var an_img_backup = "<div id='an_img_backup' style='z-index: -999; " + an_style + "'><img style='" + an_style + "' src='" + an_backup + "'></div>";
    var an_youtube = "<div id='an_ytplayer' width='" + w + "' height='" + h + "' style='z-index: -999; " + an_style + "'></div>";
    $(an_wrapper).prependTo('body');
    if (an_overlay == 'bw') {
        $(an_over).appendTo('#an_wrapper').css('mix-blend-mode', 'saturation');
    } else if (an_overlay > 0) {
        $(an_over).appendTo('#an_wrapper').css('opacity', an_overlay);
    }
    if (an_media.indexOf('youtube.com' || 'youtu.be') !== -1 && isMobile == false) {
        $(an_youtube).appendTo('#an_wrapper');
        window.onYouTubePlayerAPIReady = function () {
            var player = new YT.Player('an_ytplayer', {
                height: h,
                width: w,
                videoId: an_media_id[an_yt_length],
                playerVars: {
                    autoplay: 1,
                    controls: 0,
                    showinfo: 0,
                    modestbranding: 1,
                    loop: 1,
                    fs: 0,
                    cc_load_policty: 0,
                    iv_load_policy: 3,
                    autohide: 0,
                    rel: 0,
                    disablekb: 1,
                    enablejsapi: 1,
                    fs: 0,
                    playsinline: 0
                },
                events: {
                    onReady: function (e) {
                        e.target.mute()
                    },
                    onStateChange: function (e) {
                        if (e.data === YT.PlayerState.ENDED) {
                            player.playVideo();
                        }
                        if (e.data === YT.PlayerState.PLAYING) {
                            $('#an_wrapper').fadeTo("slow", 1);
                        }
                    }
                }
            })
        }
    } else {

        if (an_media_url[an_length] == 'mp4' && isMobile == false) {
            $(an_video).appendTo('#an_wrapper');
            var an_loadedvideo = document.getElementById('an_video');
            an_loadedvideo.addEventListener('playing', function () {
                $('#an_wrapper').fadeTo("slow", 1);
            }, false);
        } else if (an_media_url[an_length] == 'jpg' | 'gif' | 'png') {
            $(an_img).appendTo('#an_wrapper');
            $('#an_img').ready(function () {
                $('#an_wrapper').fadeTo("slow", 1);
            });
        } else if (an_media_url[an_length] == 'mp4' || an_media.indexOf('youtube.com' || 'youtu.be') !== -1 && isMobile == true) {
            if (an_backup == null) {
                console.log("You did not specify a backup image for mobile devices and tablets.");
            }
            $(an_img_backup).appendTo('#an_wrapper');
            $('#an_img_backup').ready(function () {
                $('#an_wrapper').fadeTo("slow", 1);
            });
        }
    }
}