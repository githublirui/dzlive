'use strict';

var urlHead ='';

$(function(){
    $(document).on("pageInit", "#page_home", page_home);
    $(document).on("pageInit", "#page_activityDetail", page_activityDetail);
    $(document).on("pageInit", "#page_personalCenter", page_personalCenter);
    $(document).on("pageInit", "#v1_page_course_detail", v1_page_course_detail_controller);
    $.init();
});

function page_home(e, id, page) {

}

function page_activityDetail(e, id, page) {
    var config = {
        ad_config: {
            initialSlide :0,                    //设定初始化时slide的索引。
            autoplay: 3000,                     //可选选项，自动滑动
            loop: true,
            autoHeight: true,                   //高度随内容变化
            pagination: '.x_swiper_image_pagination'    // 如果需要分页器
        }
    };


    var handle = {
        init: function() {
            this.swiper_ad();
        },
        swiper_ad: function() {
            //var swiper_img = new Swiper(config.ad_config);
            //$(".x_swiper_image").swiper(config.ad_config);
            var mySwiper_ad = new Swiper('.x_swiper_image', config.ad_config);

        }
    };


    handle.init();

}

function page_personalCenter(e, id, page) {
    var config = {

    };

    var handle = {
        init: function() {

        },
        login_popup: function () {
            $.popup('.popup-services');
        }
    };

    $(document).on('click','.open-services', function () {
        handle.login_popup();
    });
}



