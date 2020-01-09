

$(function(){
	window.onscroll=function(){
	var autoheight=document.body.scrollTop||document.documentElement.scrollTop;
	if(autoheight>100){
		$(".go_top").fadeIn()
		}else{
			$(".go_top").fadeOut()
		}
	}
	$(".btn_top").mousedown(
		function(){
			$("html, body").animate({"scroll-top":0},"fast");
		}
	)
})

// 预加载
window.onload = function () {
	document.getElementById('loading-mask').style.display = 'none';
}

//占位框高度获取
$(function(){
	var top_h=$(".zy_search_top_box").height()
	$(".top_zhanwei_box").css("height",top_h)
})

// 登录注册及会员中心表单填写下划线变色
$(function(){
	$(".form_line label input,.form_line label textarea").focusin(
		function(){
			$(this).parent("label").css("border-color","#f2b300")
		}
	).focusout(
		function(){
			$(this).parent("label").css("border-color","#ddd")
		}
	)
})

// 滑动js
var key=0;
pyz=40;
var startX,startY,moveEndX,moveEndY,X,Y;
function huadong(name1,name2){
	$(name1).on("touchstart", function(e) {
    startX = e.originalEvent.changedTouches[0].pageX;
    startY = e.originalEvent.changedTouches[0].pageY;
    });
    $(name1).on("touchend", function(e) {
        // 判断默认行为是否可以被禁用
        moveEndX = e.originalEvent.changedTouches[0].pageX,
        moveEndY = e.originalEvent.changedTouches[0].pageY,
        X = moveEndX - startX,
        Y = moveEndY - startY;
        // alert(X+" "+Y);
        //判断是否下上
        if ((Y>pyz ||Y<-pyz) && (X>pyz || X<-pyz)) {
            return;
        }
        if (X!=0 && key==1) {
		    	$(name1).stop().animate({left:0},150);
		    	$(name2).stop().animate({right:'-24%'},150);
		    	key=0;
		    	return;
		}
        //左滑
        if ( X > 0 && X>20 && Y<pyz) {
            $(name1).stop().animate({left:0},150);
		    $(name2).stop().animate({right:'-24%'},150);
		    key=0;
		    return;
        }
        //右滑
        else if ( X < 0 && X<-20 && Y<pyz) {
            $(this).stop().animate({left:'-24%'},150);
		    $(this).next().stop().animate({right:0},150);
		    key=1;
        }
        
    });
}

