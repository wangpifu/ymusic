$(document).ready(function()
{
	if( navigator.appName == "Microsoft Internet Explorer" && parseFloat(navigator.appVersion) <= 4 )
	{
		$('body').css({"background-image":"url('static/cover.png')"});

		$('<div class="notice"><div class="title">您的浏览器不受支持。</div><div class="message">我们建议您使用最新版本的 Chrome、Firefox、Safari 或 IE 浏览器。</div></div>').appendTo(document.body);

		return false;
	}
	$(".typewindow").fadeIn(500);

	if (connect == "")
	{
		$(".typewindow").click(showtype);}

function showtype()
{
	$("#typewindowSelect").fadeOut();
	
	$("#typeView").fadeIn();

}
}	