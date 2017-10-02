function v1_page_course_detail_controller() {
    $(document).on('click','.flushpage', function () {
        window.location.reload(true);
    });

    var f = document.getElementById('wxzvideourl').value;
    i = $('[id="wxzvideoimgurl"]').val();
    if (f == undefined || f.length  < 1) {
        f = i;
    }

    // jwplayer.key="aYB2Ya1BAkZIWfRiC6kkx/dlNzydIi1TAey8iA==";

    // jwplayer("wxzvideo").setup({
    //     "file": f,
    //     "width":'100%',
    //     "height":'300',
    //     "autostart":false,
    //     "startparam": "start",
    //     "primary":'html5',
    //     "hlshtml":true,

    // });
    if (f !== undefined && f.length  > 0) {
        var flashvars={
            f:f,
            c:0,
            p:1,
            b:1,
            i:i
            };
            var video=[f];

        
        CKobject.embed('ckplayer/ckplayer.swf','wxzvideo','ckplayer_wxzvideo','100%','300',false,flashvars,video);


    }

}

