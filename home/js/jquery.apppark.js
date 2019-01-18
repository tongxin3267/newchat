//画廊
(function($){
	$.fn.slider = function(ini){ 
		var ini=ini||{};
		//初始化默认值
		var defaults = {
			//动画运行宽度
			step: ini.step || this.width(),
			//动画时长
			duration:ini.duration || 500,
			//是否自动播放
			autoPlay:ini.autoPlay === false ? false : true,
			//自动播放间隔
			autoPlayTime:ini.autoPlayTime || 3000,
			//是否显示控制条
			ctrlBar:ini.ctrlBar === false ? false : true,
			//是否显示文本条
			textBar:ini.textBar === false ? false : true,
		} ;
		
		var options = $.extend(defaults, options); 
		this.each(function(){
			var $this=$(this);
			//计时器
			var timer=$this.attr("id")+"Timer";
			var $ul=$this.find("ul"),leng=$ul.find("li").length;
			//初始化滚动位置
			$ul.css({"margin-left":-options.step}).find("li:last").clone().prependTo($ul);
			//自动播放
			if(options.autoPlay){
				$this.hover(function(){
					clearInterval(timer);
					},function(){
							timer = setInterval(function(){
							scrollImg(1);
					},options.autoPlayTime);
				}).trigger("mouseleave");
			};
			//滚动方式-左右移动
			function scrollImg(m){
				var m=m;
				if(m==1){m=1;l=2*options.step;}else{m=-1;l=0;};
				//console.log(m+","+step+","+index);
				$ul.stop().animate({marginLeft : -l},options.duration,function(){
					if(m==1){
						$ul.find("li:first").remove();
						$ul.find("li:first").clone().appendTo($ul);
					};
					if(m==-1){
						$ul.find("li:last").remove();
						$ul.find("li:last").clone().prependTo($ul);
					};
					$ul.css({"margin-left":-options.step});
					$ul.find("li:eq(1)").addClass("active").siblings().removeClass("active");
					if(options.textBar==true){
						$this.find(".slider-text").html(getText());
					};
				});
			};
			//是否显示控制条
			if(options.ctrlBar){
				showCtrlBar();
			};
			//显示控制条
			function showCtrlBar(){
				$this.append("<div class='slider-ctrl'> <a href='javascript:;' class='prev'>&lt;</a> <a href='javascript:;' class='next'>&gt;</a> </div>");
				var $next=$this.find(".slider-ctrl .next"),$prev=$this.find(".slider-ctrl .prev")
				$next.bind("click",function(){
					scrollImg(1);
				});
				$prev.bind("click",function(){
					scrollImg(-1);
				});
				$this.hover(function(){
					$this.find(".slider-ctrl").stop().fadeIn();
				},function(){
					$this.find(".slider-ctrl").stop().fadeOut();
				});
			};	
			
			//是否显示文本条
			if(options.textBar){
				showTextBar();
			};
			
			//显示文本条
			function showTextBar(){
				$this.append("<a href='javascript:;' class='slider-text ui-ofh'></a>");
				$this.find(".slider-text").html(getText());
			};
			
			function getText(){
				return  $this.find(".active img").data("title");
			};
			
		}); 
	}  
	
})(jQuery); 



//跟随鼠标
(function($){ 
	$.fn.eleOffset = function(ini){ 
		var ePageX,ePageY;
		$(document).mousemove(function (event) {
			event = event || window.event;
			ePageX = event.pageX;
			ePageY = event.pageY;
        });

		function getScrollTop() {
			var scrollTop = document.documentElement.scrollTop || window.pageYOffset || document.body.scrollTop;
			return scrollTop;
		};
		function getScrollLeft() {
			var scrollLeft = document.documentElement.scrollLeft || window.pageXOffset || document.body.scrollLeft;
			return scrollLeft;
		};
		this.each(function(){ 
			var $this = $(this);
			var offX=$(this).data("offsetx"),
				offY=$(this).data("offsety");
			var	pageW=$(window).width(),
				pageH=$(window).height();
			var	posX=$this.position().left,
				posY=$this.position().top;
			//console.log(pageW+","+posY);
			
			var eleOffsetTimer = setInterval(function (){
					var move = moveto();
					if (move){
						var left=$this.position().left;
						var l=left+(move.x-left)*.1;
						var top=$this.position().top;
						var t=top+(move.y-top)*.1;
						//console.log(move.x+","+move.y);
						//console.log(l+","+t);
						$this.css({"left":l,"top":t})
					}
				},15);
				
			function moveto(){
				if (ePageX == null || ePageY == null) return false;
				var scrollX=getScrollLeft(),scrollY=getScrollTop();
				var x=posX+offX/2-(ePageX-scrollX)/pageW*offX;
				var y=posY+offY/2-(ePageY-scrollY)/pageH*offY;
				//console.log(scrollX+","+scrollY);
				return {x:x,y:y};
			};
			
		}); 
	}  	
})(jQuery);


/*标签切换*/
(function($){
	$.fn.tabNav = function(){
		this.each(function(){
			var $this = $(this);
			$this.find(".tab-nav > li > a").bind("click",function(event){
				event.preventDefault();
				var id=$(this).attr("href"),len;
				id!==""? len=$(id+".tab-panel").length :len=0;

				if (len!==0){
					$(this).parent("li").addClass("active").siblings("li").removeClass("active");
					$(id+".tab-panel").addClass("active").siblings(".tab-panel").removeClass("active");
				}
			});
		});
	}
})(jQuery);





//侧边导航
$(function(){
	tabPanelShow("#qq");
	var fadetimeS;
	$(".mod-sidebar .tab-nav li").hover(function(){
		clearTimeout(fadetimeS);
		var id=$(this).find("a").attr("href");
		var top=$(this).position().top;
		//console.log(top);
		if(id){
			$(id).css({"top":top});
			tabPanelShow(id);
		}else{
			tabPanelHide();
		};
	},function(){
		fadetimeS = setTimeout(function(){
			tabPanelShow("#qq");
		},1000);
	});

	//$(".mod-sidebar .tab-panel").hover(function(){
	//	clearTimeout(fadetimeS);
	//},function(){
	//	fadetimeS = setTimeout(function(){
	//		tabPanelHide();
	//	},1000);
	//});

	$(".mod-sidebar .tab-nav li").click(function(){
		return false;
	});


	$(".mod-sidebar .close").click(function(){
		clearTimeout(fadetimeS);
		tabPanelHide();
	});




	function tabPanelShow(id){
		$(id).stop().show().animate({"right":"0","opacity":"1"},200).siblings().stop().animate({"right":"40px","opacity":"0"},200,function(){
			$(this).hide();
		});
	}

	function tabPanelHide(){
		$(".mod-sidebar .tab-content").find(".tab-panel").stop().animate({"right":"40px","opacity":"0"},200,function(){
			$(this).hide();
		});
	}

	//侧边返回顶部
	$(window).scroll(function(){
		if ($(window).scrollTop()>50){
			$(".back-top").fadeIn(500);
		}else{
			$(".back-top").fadeOut(500);
		};
		var h=$("body").height()-window.getHeight();
		//console.log(h);
		if ($(window).scrollTop()>28 &&  h > 120){
			$(".mod-header").addClass("is-fixed").find(".logo-txt").fadeOut(400);
			

		}else if($(window).scrollTop()<28){
			$(".mod-header").removeClass("is-fixed").find(".logo-txt").fadeIn(400);
			$(".mod-topbar").fadeIn(400);

		};
	});

	$(".back-top a").bind("click",function(){
		$("body, html").animate({scrollTop:0},800);
		return false;
	});

});




//页面最小高度一屏
$(document).ready(function(){
	var hh=window.getHeight()-$("body").height();
	var bh=$(".mod-body").height();
	if (hh>0) {
		$(".mod-body").height(hh+bh);
	};
});


window.getHeight= function(){
	if(window.innerHeight!= undefined){
		return window.innerHeight;
	}
	else{
		var B= document.body, D= document.documentElement;
		return Math.min(D.clientHeight, B.clientHeight);
	}
}
// 点击关闭注册上的小标签
function closeResTag(t) {
  $(t).parents("div.xsRegister").find("span").hide();
}
  











