
//https://support.google.com/analytics/answer/2558867?hl=th&utm_id=ad
//https://developers.google.com/analytics/devguides/collection/gajs/

//var ajax=false;
$(document).ready(function(e) {
	var page=window.location;
	
	
	window.history.pushState({
			pageTitle:document.title,
			title:$("#title").html(),
			article:$("#article").html()
		},
		document.title,
		page);
	
	$("[data-pjax] a").click(function(e) {
		return loadPage($(this).attr('href'));
    });
	window.onpopstate=function(){
		setHTML(window.history.state);
	};
	
	function setHTML(data){
		var title="",article="";
		try{
			title=data.title;
			article=data.article;
			document.title=data.pageTitle;
		}catch(e){
			return;
		}
		$("#title").fadeOut("slow",function(){$("#title").html(title).fadeIn("slow");});
		$("#article").hide("slow",function(){$("#article").html(article).show("slow");});
	}
	function loadPage(url){
		if(url==page) return false;
		_gaq.push(['_trackEvent',"link to "+url, 'clicked']);
	
		var t=new Date();
		$.post("loadpage.php?action=loadpage",{"page": url,"time":t.toString()},function(data,code){
			window.history.pushState(data,data.title,url);
			setHTML(data);
			page=url;
		},"json");
		return false;
	}
});
