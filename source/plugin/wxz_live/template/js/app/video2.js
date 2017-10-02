define(function(){
   if (document.getElementById('my_video') != undefined) {

	    var f = document.getElementById('wxzvideourl').value;
	    i = jQuery('[id="wxzvideoimgurl"]').val();
	    if (f == undefined || f.length  < 1) {
	        f = i;
	    }

	   	var wxzie;
		if(navigator.userAgent.indexOf("MSIE")>0){
		  if(navigator.userAgent.indexOf("MSIE 6.0")>0){   
		   	wxzie = 6;
		  }   
		  if(navigator.userAgent.indexOf("MSIE 7.0")>0){  
		  	wxzie = 7;
		  }   
		  if(navigator.userAgent.indexOf("MSIE 9.0")>0 && !window.innerWidth){//这里是重点，你懂的
		    wxzie = 8;
		  }   
		  if(navigator.userAgent.indexOf("MSIE 9.0")>0){  
		    wxzie = 9;
		  }   
		} 
		var jw_height;
 		try {
            if(typeof getWindowSize === "function") {
               jw_height = getWindowSize() - 30;
            } else {
                
            	jw_height = '100%';
            }
        } catch(e) {}
		if (wxzie >= 6 && wxzie < 9) {
		    var flashvars={
		        f:f,
		        c:0,
		        p:1,
		        b:1,
		        i:i,
		        h:'4',
	            q:'start'
		        };
		        var video=[f];
		    CKobject.embed('source/plugin/wxz_live/template/touch/ckplayer/ckplayer.swf','my_video','ckplayer_my_video','100%',jw_height,false,flashvars,video);
		}else if (f.toLowerCase().indexOf(".m3u8") > 0) {
			console.log(f);
			wxzshowPlayer(f,'my_video')
			function wxzshowPlayer(src, id){
			     //player
			   var flashvars={
			        f : 'source/plugin/wxz_live/template/touch/ckplayer/m3u8.swf',
			        a : src,
			        c : 0,
			        p : 1,
			        b : 1,
			        i : i,
			        h : 4,
			        s:4,
			        q:'start',
			        lv:0//注意，如果是直播，需设置lv:1
			    };
			    var params={bgcolor:'#FFF',allowFullScreen:true,allowScriptAccess:'always',wmode:'transparent'};
			    var video=[src];
			    CKobject.embed('source/plugin/wxz_live/template/touch/ckplayer/ckplayer.swf',id ,'my_video','100%',jw_height,false, flashvars ,video, params);
			 
			}

		}else{
			jwplayer.key="aYB2Ya1BAkZIWfRiC6kkx/dlNzydIi1TAey8iA==";
		    jwplayer("my_video").setup({
			    "file": f,
				"width":'100%',
				"height":jw_height,
				"autostart":"true",
				"startparam": "start",
				"autochange":"true",
				"showset":"false",
			});
		}

    }

 
});