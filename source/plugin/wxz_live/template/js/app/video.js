define(function(require, exports, module){
	require('/js/lib/jwplayer.js');
	require("/js/lib/jquery.nanoscroller.js");
	require("/js/lib/drag.js");

	var store = require('/js/lib/store.js');
	var animateMp = require('/js/lib/animate-achievement.js');
	//var commonInterface = require("/js/lib/course_detail_common.js");
	//var verifyCode = require('/js/lib/verify-code.js');
	//var guideLayer = require('/js/lib/guideLayer.js');

	var mediaData = null; // 视频信息数据
	var mediaTimer = null;

	// 倒计时
	function countDowner(opts) {
		var timer = null,
			el = opts.el, // 数值容器
			initCounter = opts.initCounter, // 初始数值
			interval = opts.interval || 1, // 间隔时间，单位s
			callback = opts.callback;

		if (initCounter > 0) {
			initCounter--;
			el.innerHTML = initCounter;
			opts.initCounter = initCounter;
			timer = setTimeout(function() {
				countDowner(opts);
			}, interval * 1000);
		} else {
			clearTimeout(timer);
			callback && callback();
		}
	};

	// 获取视频信息，并回调初始化播放器
	function getMediaInfo(callback){
		$.getJSON("/course/ajaxmediainfo/?mid="+pageInfo.mid+"&mode=flash",function(data){
			mediaData = data.data.result;
			callback && callback();
		});
	}

	// 播放下一节
	function playNextCourse(courseSrc){
		location.href = courseSrc;
	}

	// 获取当前章节
	function getcurrChapterItem(){
		var currentId = $(".J-chaptername").data('id');
   		return $('[data-id='+ currentId +']');
	}

	// 设置章节列表滚动到当前章节
	var nanoScrollerCallback = {
		scrollTo: getcurrChapterItem()
	};

	//verifyCode.renderVerifyCodeBlock('.qa-pop .verify-code','/course/getcoursequestioncode');

$(function(){
	// 第一次访问显示新功能引导层
	var isFirstVisit = store.get('isFirstVisit') || 'Yes';
	if(isFirstVisit === 'Yes'){
		guideLayer.create(function(){
			getMediaInfo(initPlayerMode);
		});
	}else{
		    getMediaInfo(initPlayerMode);
	}

	if(typeof continueTime != 'number'){
		continueTime=0;
        var sv=store.get("_vt");
        if(sv&&sv[pageInfo.mid]){
            continueTime=sv[pageInfo.mid].st||0;
        }
	}

    $(window).on("beforeunload",function(){
        var vt=store.get("_vt")||{},
            it=vt[pageInfo.mid],
            state=thePlayer.getState();
        if(state=="IDLE"){
            delete vt[pageInfo.mid];
            store.set("_vt",vt);
            return ;
        }
        if(it){
            it.t=new Date().getTime();
            it.st=thePlayer.getPosition();
            store.set("_vt",vt);
        }
        else{
            it={
                t:new Date().getTime(),
                st:thePlayer.getPosition()
            }
            ck();
            vt[pageInfo.mid]=it;
            store.set("_vt",vt);
        }
        function ck(){ //check length<10 ,delete overflowed;
            var k,tk,i=0,tt=new Date().getTime();
            for(k in vt){
                i++;
                if(vt[k].t<tt){
                    tt=vt[k].t;
                    tk=k;
                }
            }
            if(i>=10){
                delete vt[tk];
                ck();
            }
        }
    });

	function initPlayerMode(){
		//html5(flash)为默认打开方式
		var mode = store.get('mode') || 'html5';
		initPlayer(mode, 0);
	}

	var sentLearnTime=(function(){
		if(!OP_CONFIG.userInfo){
			return ;
		}
	 	var _params={},
	 		lastTime=0,
	 		startTime=new Date().getTime();
		var fn;
	    _params.mid=pageInfo.mid;

	    mediaTimer = window.setInterval(fn=function(){
			var overTime,
				stayTime;
			if(typeof(thePlayer)!='object') return //no video no time;
			overTime=new Date().getTime();
			stayTime=parseInt(overTime-startTime)/1000;

			_params.time=stayTime-lastTime;
			_params.learn_time =thePlayer.getPosition();

			$.ajax({
				url:'/course/ajaxmediauser/',
				data:_params,
				type:"POST",
				dataType:'json',
				success:function(data){
					if(data.result== '0'){
						lastTime=stayTime;
                        var chapterMp = data.data.media;
                        var courseMp = data.data.course;
                        var data = [];
                        chapterMp && data.push({mp: chapterMp.mp.point, desc: chapterMp.mp.desc});
                        courseMp && data.push({mp: courseMp.mp.point, desc: courseMp.mp.desc});
						//经验值飞出，调用animateMp接口
                        animateMp(data);

                        if(chapterMp){
                        	$('#J_ques_pop').show();
                        }
					}
				}
			});
		},60000);

		window.onbeforeunload=function(){
			var overTime,
				stayTime;
			if(typeof(thePlayer)!='object') return //no video no time;
			overTime=new Date().getTime();
			stayTime=parseInt(overTime-startTime)/1000;

			_params.time=stayTime-lastTime;
			_params.learn_time =thePlayer.getPosition();

			$.ajax({
				url:'/course/ajaxmediauser/',
				data:_params,
				type:"POST",
				async:false,
				dataType:'json',
				success:function(data){
					if(data.result=='0'){
						lastTime=stayTime;
					}
				}
			});
		}
		return fn;
	})();

	function checkH5() {
		return window.applicationCache? true : false;
	}


    function initPlayer(primary,time){
    	
        window.thePlayer = jwplayer('video-box').setup({
            width:'100%',
            height:'100%',
            videotitile: videoTitle,
			primary: primary,
			
            autostart:false,
            startparam: "start",
            autochange:true,

			showset:true,//是否显示设置,true ：显示，false：不显示
			ish5:checkH5(),//是否可以切换h5模式,true ：是，false：否

                sources: [{
                    file: mediaData.mpath[0],
                 
                    label: "普清",
                    "default": true
                },{
                    file: mediaData.mpath[1],
                  
                    label: "高清"
                },{
                   file: mediaData.mpath[2],
                   
                    label: "超清"
                }],
            events: {
                onReady: function(callback) {//
                	//console.log('onReady--------------');
                    if(OP_CONFIG.userInfo){
						if(time!=0){
							continueTime=time;
						}
                        thePlayer.seek(continueTime);
                    }
                },
                onComplete: function(){
                	//播放完成后不再定时请求接口
                	window.clearInterval(mediaTimer);
                	mediaTime = null;
                	var isauto=thePlayer.getAutoPlay();
                	//console.log(isauto+"--isauto-----onComplete");
                	var $nextBox = $('.J_next-box'),
						$nextCourse = $nextBox.find('.J-next-course');

					$nextBox.removeClass('hide');

					sentLearnTime();
					
					// 如果有下一节
					if($nextCourse.length){
						var $nextCourseBtn = $('.J-next-btn'),
							$nextAutoPlay = $('.J-next-auto'),
							nextCourseSrc = $nextCourse.data('next-src');

						// 如果勾选了自动播放下一节
					
				      if(isauto){
				      	$nextAutoPlay.removeClass('hide');

				      	// 3s后自动播放下一节
				      	setTimeout(function(){
				      		countDowner({
				        		el: $nextAutoPlay.find('em')[0],
				        		initCounter: 3,
				        		callback: function(){
				        			playNextCourse(nextCourseSrc);
				        		}
				        	});
				      	}, 1000);

				      }else{ // 如果没有勾选，则显示“手动播放下一节”提示层
				      	$nextCourseBtn
				      		.removeClass('hide')
				        	.on('click', function(){
				        		playNextCourse(nextCourseSrc);
				        	});
				      }
					}else{
						// 如果本章已完结，就显示课程推荐层

					}
                },
                onPlay:function(callback){
                	//console.log(callback.oldstate+"=onPlay-----");
                	if(callback.oldstate=="BUFFERING" && switchType!=0){
                		var bufferTime=new Date().getTime()-playerWaitTime;
                		sendVideoTestData(bufferTime,"",switchType);
                	}
                },
    		    onBuffer:function(callback){//缓冲状态，缓冲图标显示
					
					playerWaitTime=new Date().getTime();
					//console.log(playerWaitTime+"=onBuffer------");
				},
				onQualityChange:function(callback){
					//console.log('onQualityChange---------')
					hdArr.push(thePlayer.getCurrentQuality());
              		switchType=2;
              		//sendVideoTestData(bufferTime,"",2);
				},
				onQualityLevels:function(callback){
					//inint----
					initFeedbackInfo();
              		hdArr.push(thePlayer.getCurrentQuality());
              		//sendVideoTestData(bufferTime,"",1);
              		switchType=1;
            	},
				onFullscreen:function(){
					
					sendVideoTestData(0,"",4);
				},
				onError:function (callback){
					if(switchType==2){//失效--
						getEncryption(thePlayer.getCurrentQuality(),'');
						switchType=0;

					}else{
						sendVideoTestData(new Date().getTime()-playerWaitTime,callback.message,3);
						loadNewVideo(callback.message);//线路选择--
					}
				}
            }
        })

		thePlayer.onSpeedChange=function(){
			
			speedArr.push(document.getElementById('speedTxt').innerHTML);
			sendVideoTestData(0,"",5);
		}
    }

//--------get encryption url--------
function getEncryption(n,_company){

  $.ajax({
	    url:"/api/encrypt",
	    async:true,
	    type:"POST",
	    dataType:"json",
	    data:{mid:pageInfo.mid,clear:n,company:_company},
	    success:function(data){
	        if(data.result==0){
		        mediaData.mpath[n]= data.data.url;
		        jwLoad();
	        }else{
	        	thePlayer.showErrorWin();
	        }
	    }
    })
}
//1初始；2切换hd；3出错；4全屏切换；5速度切换
	//线路切换
	var requsetCount=0;
	function loadNewVideo(message) {
		requsetCount++;
		if(requsetCount>=2){ //2次仍访问不了，返回错误提示界面
			requsetCount=0;
			//console.log(requsetCount+"------------------end-------")
			//if(thePlayer.getRenderingMode()=="html5"){
        	thePlayer.showErrorWin();
       		//}
			return;
		}
		var index=thePlayer.getCurrentQuality();
		var urlArr=mediaData.mpath[index].split("/");
		//console.log(mediaData.mpath[index]+"------------------前------"+requsetCount)
		switch(urlArr[2]){//v2.mukewang.com //video2.mukewang.com

			case "v2.mukewang.com"://1 v1-->video
				//mediaData.mpath[index]=mediaData.mpath[index].replace(/v1/,"video");
				getEncryption(index,'letv');
				break;
			
			case "video2.mukewang.com"://2 video.mukewang-->v1.imooc
				//mediaData.mpath[index]=mediaData.mpath[index].replace(/video/,"v1");
				getEncryption(index,'');
				break;
			//case "v1.imooc.com"://2 video-->v1
			//	mediaData.mpath[index]=mediaData.mpath[index].replace(/v1.imooc.com/,"v2.mukewang.com");
			//	break;
		}
		//console.log(mediaData.mpath[index]+"------------------后------")
		//jwLoad();
}

function jwLoad(){
	thePlayer.load([{
			sources: [{
				file: mediaData.mpath[0],
				label: "普清",
				"default": true
			},{
				file:mediaData.mpath[1],
				label: "高清"
			},{
				file: mediaData.mpath[2],
				label: "超清"
			}]

		}]);

	thePlayer.play();
}
var playerWaitTime=0,hdArr,speedArr,switchType=0;

function initFeedbackInfo(){
	hdArr=[""];
	speedArr=[""];
	//
	var speed='1.0 X';
	if(thePlayer.getRenderingMode()=='html5'){
		speed=document.getElementById('speedTxt').innerHTML;

	}
	speedArr.push(speed);
}
//播放器加载错误日志------
/*
 *合肥微小智www.hfwxz.com
 *备用域名www.hfwxz.com
 *更多精品资源请访问合肥微小智官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */

function sendVideoTestData(bufferTime,msg,type){
  var fullscreen=0,renderingMode=1;
  var currentHd=thePlayer.getCurrentQuality();
  var videoUrl=mediaData.mpath[currentHd];
  var cdnArr=videoUrl.split('/');
    if(thePlayer.getRenderingMode()=="html5"){
        renderingMode=0;       
    }
    if(thePlayer.getFullscreen()==true){
    	fullscreen=1;
    }

	/*
 *合肥微小智www.hfwxz.com
 *备用域名www.hfwxz.com
 *更多精品资源请访问合肥微小智官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */
    $.post("/course/collectvideo",{
        renderingMode:renderingMode,
        bufferTime:   bufferTime,
		videoFileName:videoUrl,
		videoId:      pageInfo.mid,
		errorMsg:     msg,

		currentHd:    currentHd,
		oldHd:        hdArr[hdArr.length-2],
		type:         type,
		fullscreen:   fullscreen,
		cdn:          cdnArr[2],
		source:       1,
		currentSpeed: speedArr[speedArr.length-1],
		oldSpeed:     speedArr[speedArr.length-2],
		winWidth:     window.screen.width,
		winHeight:    window.screen.height
    },
	function(data){
    	//console.log(data+"----ok----" +data.msg);
   });
   switchType=0;
    
}

	window.switchjwplayer=switchjwplayer;
	function switchjwplayer(getRenderingMode){

		var time=thePlayer.getPosition();
		var mode=getRenderingMode;
		store.set('mode', mode);
		thePlayer.remove();
		switchType=2;
		initPlayer(mode,time);
		
	}

 



//截图后flash回调
window.screenReceive=screenReceive;
function screenReceive(data){
	if(typeof data=="string"){

		data=$.parseJSON(data);
	}
	if(data.result==0){
		shot.screenShotFlashBack(data);
	}
	else{
		alert(data.msg||"错误，请稍后重试");
	}
	//console.log(url,typeof url)
}

/*
 *合肥微小智www.hfwxz.com
 *备用域名www.hfwxz.com
 *更多精品资源请访问合肥微小智官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */

/*
 *合肥微小智www.hfwxz.com
 *备用域名www.hfwxz.com
 *更多精品资源请访问合肥微小智官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */

$(".js-shot-video").click(function(){
	//shot.screenShot(this);
	if(!$(this).hasClass('on')){
		$(this).addClass('on');
	}else{
		$(this).removeClass('on');
	}
});

//发问答

	/*
 *合肥微小智www.hfwxz.com
 *备用域名www.hfwxz.com
 *更多精品资源请访问合肥微小智官方网站免费获取
 *本资源来源于网络收集,仅供个人学习交流，请勿用于商业用途，并于下载24小时后删除!
 *如果侵犯了您的权益,请及时告知我们,我们即刻删除!
 */

	(function(){
		//重置视频区域宽度
		var videoWrap = (function(){
			var rObj = {};
			rObj.resetSize = function(){
				//计算宽度
				var bool = $('.chapter').hasClass('light');
				if(bool){
					if($(window).width()>800){
						var w = $(window).width()-$('.section-list').outerWidth(true);
					}else{
						var w = 800;
					}
					w = w+'px';
				}else{
					var w = '100%';
				}
				//计算高度
				var hh = $('#header').outerHeight(true);//顶部高度
				var cm = $('.js-course-menu').outerHeight(true);//课程导航高度
				var h = $(window).height()-hh-cm;
				
				$('.js-box-wrap').css('width',w).css('height',h+'px');
				if(h>500){
					$('.question-tip-layer').css('marginTop','6%');
				}else if(h>400&&h<500){
					$('.question-tip-layer').css('marginTop','3%');
				}else if(h<400){
					$('.question-tip-layer').css('marginTop','1%');
				}
			};
			return rObj;
		})();



		//随屏滚动
		var fixed = (function(){
			var fixedElem = $('.js-course-menu'),
				targetElem = $('.course-left');
			//随屏滚动以.course-left为准
			var targetT = targetElem.offset().top;
			var targetL = targetElem.offset().left;
			var fixedVideo = $('.js-fixed-video');

			$(window).on('scroll',function(){
				var wt = $(window).scrollTop();
				//console.log(wt);
				//var text = thePlayer.getRenderingMode();
				//if(!text){
				//	return ;
				//}
				if( wt >= targetT){
					fixedElem.css('position','fixed').css('left',targetL+'px');
					//thePlayer.pause();
					//var seekPosition=thePlayer.getPosition();
					//var oldState = thePlayer.getState();
					//if($(window).width()>1200){
					//	if(fixedVideo.hasClass('on')){
					//		return ;
					//	}else{
					//		fixedVideo.addClass('on');
					//		if(text==='html5'){
					//			fixedVideo.find('.fixed-video-con').append($('#video-box'));
					//		}else{
					//			fixedVideo.find('.fixed-video-con').append($('#video-box_wrapper'));
					//		}
					//		if(oldState=='PAUSED'){
					//			thePlayer.pause();
					//		}else{
					//			thePlayer.seek(seekPosition);
					//		}
					//		thePlayer.hideControlBar()
					//	}
					//}else{
					//	//thePlayer.pause();
					//	if(fixedVideo.hasClass('on')){
					//		fixedVideo.removeClass('on');
                    //
					//		if(text==='html5') {
					//			$('#J_Box').append($('#video-box'));
					//		}else{
					//			$('#J_Box').append($('#video-box_wrapper'));
					//		}
					//		if(oldState=='PAUSED'){
					//			thePlayer.pause();
					//		}else{
					//			thePlayer.seek(seekPosition);
					//		}
					//		thePlayer.showControlBar();
                    //
					//	}
					//}

				}else{
					fixedElem.css('position','absolute').css('left', 0);
					//if(fixedVideo.hasClass('on')){
					//	fixedVideo.removeClass('on');
					//	//thePlayer.pause();
					//	var seekPosition=thePlayer.getPosition();
					//	var oldState = thePlayer.getState();
                    //
					//	if(text==='html5') {
					//		$('#J_Box').append($('#video-box'));
					//	}else{
					//		$('#J_Box').append($('#video-box_wrapper'));
					//	}
					//	if(oldState=='PAUSED'){
					//		thePlayer.pause();
					//	}else{
					//		thePlayer.seek(seekPosition);
					//	}
					//	thePlayer.showControlBar();
                    //
					//}
				}
			});
			return {
				setLT:function(){
					targetT = targetElem.offset().top;
					targetL = targetElem.offset().left;
				}
			};

		})();

		videoWrap.resetSize();
		//nano 初始化
		$(".nano").nanoScroller(nanoScrollerCallback);
		fixed.setLT();
		$(window).on('resize',function(){
			setTimeout(function(){
				videoWrap.resetSize();
				$(".nano").nanoScroller(nanoScrollerCallback);
				fixed.setLT();
				$(window).trigger('scroll');
			} , 200);
		});


		// 章节列表显隐切换交互
		var $sectionList = $('.section-list');
		$('.chapter').on('click', function(){
			var $this = $(this);
			if($this.hasClass('light')){
				$this.removeClass('light');
				$sectionList.animate({
					right: -360
				}, 200);
			}else{
				$this.addClass('light');
				$sectionList.animate({
					right: 0
				}, 200);
			}
			videoWrap.resetSize();
		});
	})();

});//$(function(){}) end
});
